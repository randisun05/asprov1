<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Member;
use App\Models\instansi;
use App\Models\Registration;
use Illuminate\Http\Request;
use App\Mail\SendEmailReject;
use GuzzleHttp\Handler\Proxy;
use App\Mail\SendEmailAprrove;
use App\Mail\SendEmailConfirm;
use App\Models\ProfileDataMain;
use Illuminate\Validation\Rule;
use App\Exports\RegistrationPaid;
use App\Models\RegistrationGroup;
use Illuminate\Support\Facades\DB;
use App\Exports\RegistrationExport;
use App\Imports\RegistrationImport;
use App\Mail\SendEmailRegistration;
use App\Models\ProfileDataPosition;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Intervention\Image\Colors\Rgb\Channels\Red;

class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $registers = Registration::when(request()->q, function($registers) {
            $registers = $registers->where('name', 'like', '%'. request()->q . '%')
            ->orWhere('nip', 'like', '%'. request()->q . '%')
            ->orWhere('agency', 'like', '%'. request()->q . '%')
            ->orWhere('status', 'like', '%'. request()->q . '%')
            ->orWhere('position', 'like', '%'. request()->q . '%')
            ->orWhere('admin', 'like', '%'. request()->q . '%')
            ->orWhere('emailstatus', 'like', '%'. request()->q . '%');
        })
        ->orderByRaw("CASE
        WHEN status = 'submission' THEN 1
        WHEN status = 'paid' THEN 2
        WHEN status = 'confirm' THEN 3
        WHEN status = 'approved' THEN 4
        ELSE 5
    END")->orderBy('emailstatus', 'asc')
    ->orderBy('created_at', 'asc')->paginate(10);


        //render with inertia
        return inertia('Admin/Registration/Index', [
            'registers' => $registers,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $instansis = instansi::get();
        return inertia('Admin/Registration/Create', [
            'instansis' => $instansis,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

  // Validate request including file validation
      $validatedData = $request->validate([
        'nip' => ['required', 'string', 'regex:/^\d{18}$/', 'unique:registrations,nip'],
        'name' => 'required|string',
        'email' => 'required|email|unique:registrations,email',
        'contact' => 'required|string|unique:registrations,contact',
        'agency' => 'required|string',
        'position' => 'required|string',
        'level' => 'required|string',
        'document_jab' => 'required|file|mimes:pdf|max:2048', // Ensure 'document_jab' is a valid file

    ],
    [
        'nip.regex' => 'NIP harus terdiri dari 18 angka.',
        'nip.unique' => 'Data NIP sudah digunakan.',
        'email.unique' => 'Data email sudah digunakan.',
        'contact.unique' => 'Data kontak sudah digunakan.',
        'nip.required' => 'NIP harus diisi.',
        'name.required' => 'Nama harus diisi.',
        'email.required' => 'Email harus diisi.',
        'contact.required' => 'Kontak harus diisi.',
        'agency.required' => 'Instansi harus diisi.',
        'position.required' => 'Jabatan harus diisi.',
        'level.required' => 'Jenjang harus diisi.',
        'document_jab.required' => 'SK jabatan harus diisi.',
    ]);

     // Store the file using Laravel's file storage system
     $document_jab = $request->file('document_jab');
     $paid = $request->file('paid');
     $document_jab = $document_jab->storePublicly('/document');

    if ($paid) {
         $paid = $paid->storePublicly('/images');
         // Jika hanya paid diisi, update semua kecuali document_jab
         $registration = Registration::create(array_merge($validatedData, [ 'status' => 'paid',
         'document_jab' => $document_jab, 'paid' => $paid,
         ]));
     } else { // Create registration
        $registration = Registration::create(array_merge($validatedData, ['document_jab' => $document_jab,]));
    }

     //redirect
     return redirect()->route('admin.registration.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = auth()->user()->name;

        $register = Registration::findOrFail($id);
        $admin = $register->admin;

        if (empty($admin)) {
            // Jika bidang 'admin' kosong, lakukan pembaruan dengan nilai dari $user
            $register->update([
                'admin' => $user,
            ]);
        } else {
            // Jika bidang 'admin' sudah terisi, periksa apakah sama dengan $user
            if ($admin !== $user) {
                // Jika tidak sama, kembalikan respons JSON "Sedang dalam verifikasi"
                return inertia('Admin/Registration/Show', [
                    'register' => $register,
                ])->with('errors','Sedang dalam verifikasi ' . $admin);
            }
        }

        // Lanjutkan ke halaman jika telah melalui verifikasi
        return inertia('Admin/Registration/Show', [
            'register' => $register,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $register = Registration::findOrFail($id);
        //render with inertia
       return inertia('Admin/Registration/Show', [
        'register' => $register,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        // Validate request including file validation
       $validatedData = $request->validate([
        'nip' => [
            'required',
            'string',
            'regex:/^\d{18}$/',
            Rule::unique('registrations')->ignore($id), // Mengabaikan ID saat validasi unik
        ],
        'name' => 'required|string',
        'email' => [
            'required',
            'email',
            Rule::unique('registrations')->ignore($id),
        ],
        'contact' => [
            'required',
            'string',
            Rule::unique('registrations')->ignore($id),
        ],
        'agency' => 'required|string',
        'position' => 'required|string',
        'level' => 'required|string',
    ], [
        'nip.regex' => 'NIP harus terdiri dari 18 angka.',
    ]);
            // Store the file using Laravel's file storage system
            $document_jab = $request->file('document_jab');
            $paid = $request->file('paid');

            if ($document_jab && $paid) {
                // Jika keduanya diisi, update semua
                $document_jab = $document_jab->storePublicly('/document');
                $paid = $paid->storePublicly('/images');
                Registration::where('id',$id)->update(array_merge($validatedData, [
                    'document_jab' => $document_jab,
                    'paid' => $paid,
                    'status' => "paid"
                ]));
            } elseif ($document_jab) {
                $document_jab = $document_jab->storePublicly('/images');
                // Jika hanya document_jab diisi, update semua kecuali paid
                Registration::where('id',$id)->update(array_merge($validatedData, [
                    'document_jab' => $document_jab,
                ]));
            } elseif ($paid) {
                $paid = $paid->storePublicly('/images');
                // Jika hanya paid diisi, update semua kecuali document_jab
                Registration::where('id',$id)->update(array_merge($validatedData, [
                    'paid' => $paid,
                    'status' => "paid"
                ]));
            }

            // Buat registration
            Registration::where('id',$id)->update(array_merge($validatedData, [
            ]));

       //redirect
       return redirect()->route('admin.registration.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //get
        $register = Registration::findOrFail($id);

        //delete
        $register->delete();

        //redirect
        return redirect()->route('admin.registration.index');
    }

    public function paid($id)
    {
        //get
        Registration::where('id', $id)->update([
            'status' => "paid"
        ]);

        //redirect
        return redirect()->route('admin.registration.index');
    }

    public function hadlecode()
    {


    }

    public function approve($id, Request $request)
{
    try {
        return DB::transaction(function () use ($id, $request) {
            $register = Registration::findOrFail($id);

            // 1. Logika Penentuan Suffix & Panjang Karakter
            $positionMap = [
                'Analis SDM Aparatur' => '/01/ASPROSDMA',
                'Pranata SDM Aparatur' => '/02/ASPROSDMA',
            ];

            $suffix = $positionMap[$register->position] ?? '/LB/ASPROSDMA';
            $padLength = ($register->position === "Pranata SDM Aparatur") ? 4 : 5;

            // 2. MENCARI NOMOR TERAKHIR (Anti Duplicate)
            // Cari member terakhir yang memiliki suffix yang sama
            $lastMember = Member::where('nomember', 'LIKE', '%' . $suffix)
                ->orderBy('nomember', 'desc')
                ->first();

            if ($lastMember) {
                // Ambil angka depan (misal '01487/01...' menjadi 1487)
                $lastNumber = (int) substr($lastMember->nomember, 0, $padLength);
                $nextNumber = $lastNumber + 1;
            } else {
                // Jika belum ada member dengan suffix tersebut, mulai dari 1
                $nextNumber = 1;
            }

            $code = str_pad($nextNumber, $padLength, '0', STR_PAD_LEFT) . $suffix;

            // 3. Logika Gender (NIP Karakter ke-15)
            $gender = (strlen($register->nip) >= 15 && substr($register->nip, 14, 1) === '2') ? 'P' : 'L';

            // 4. Create Member
            $member = Member::create([
                'nip'      => $register->nip,
                'name'     => $register->name,
                'email'    => $register->email,
                'agency'   => $register->agency,
                'nomember' => $code,
                'password' => Hash::make($register->nip),
            ]);

            // 5. Create Profile Data Main
            $profileMain = ProfileDataMain::create([
                'nip'       => $register->nip,
                'name'      => $register->name,
                'email'     => $register->email,
                'contact'   => $register->contact,
                'active_at' => now(),
                'gender'    => $gender,
                'nomember'  => $code,
            ]);

            // 6. Create Profile Data Position
            ProfileDataPosition::create([
                'main_id'  => $profileMain->id,
                'agency'   => $register->agency,
                'position' => $register->position,
                'level'    => $register->level,
            ]);

            // 7. Update Status Registrasi
            $register->update([
                'info'        => $request->info,
                'status'      => 'approved',
                'emailstatus' => $register->emailstatus + 1
            ]);

            // 8. Kirim Email
            Mail::to($register->email)->send(new SendEmailAprrove($member));

            return redirect()->route('admin.registration.index')
                             ->with('success', 'Pendaftaran berhasil disetujui dengan nomor: ' . $code);
        });
    } catch (\Exception $e) {
        // Jika terjadi error, tampilkan pesan agar mudah didebug
        return back()->with('error', 'Gagal menyetujui: ' . $e->getMessage());
    }
}

    public function approveGroup(Request $request)
{
    $registrationIds = $request->input('registration_ids', []);

    if (empty($registrationIds)) {
        return redirect()->back()->with('error', 'Pilih data yang akan disetujui.');
    }

    $registrations = Registration::whereIn('id', $registrationIds)
        ->whereNotIn('status', ['approved', 'rejected'])
        ->get();

    if ($registrations->isEmpty()) {
        return redirect()->back()->with('info', 'Tidak ada data valid untuk disetujui.');
    }

    try {
        DB::transaction(function () use ($registrations, $request) {
            $positionMap = [
                'Analis SDM Aparatur' => '/01/ASPROSDMA',
                'Pranata SDM Aparatur' => '/02/ASPROSDMA',
            ];

            // Cache untuk menghitung nomor urut agar tidak tabrakan dalam satu request
            $counters = [];

            foreach ($registrations as $register) {
                // 1. Tentukan Kategori Posisi & Suffix
                $isSpecialPos = isset($positionMap[$register->position]);
                $suffix = $positionMap[$register->position] ?? '/LB/ASPROSDMA';
                $padLength = ($register->position === "Pranata SDM Aparatur") ? 4 : 5;

                // Key untuk membedakan counter antar posisi
                $counterKey = $isSpecialPos ? $register->position : 'LAINNYA';

                // 2. Logika Penomoran (Mencegah Duplikasi dalam Loop)
                if (!isset($counters[$counterKey])) {
                    // Cari nomor terakhir yang sudah ada di tabel MEMBERS, bukan count dari registrations
                    $lastMember = Member::where('nomember', 'LIKE', '%' . $suffix)
                        ->orderBy('nomember', 'desc')
                        ->first();

                    if ($lastMember) {
                        // Ambil 5 digit pertama dari string (misal  '01487/01...' jadi 1487)
                        $lastNumber = (int) substr($lastMember->nomember, 0, $padLength);
                        $counters[$counterKey] = $lastNumber;
                    } else {
                        $counters[$counterKey] = 0;
                    }
}

                $counters[$counterKey]++; // Tambah urutan
                $code = str_pad($counters[$counterKey], $padLength, '0', STR_PAD_LEFT) . $suffix;

                // 3. Logika Gender
                $gender = (strlen($register->nip) >= 15 && substr($register->nip, 14, 1) === '2') ? 'P' : 'L';

                // 4. Create Member & Profile (Sama seperti logic approve satuan)
                $member = Member::create([
                    'nip'      => $register->nip,
                    'name'     => $register->name,
                    'email'    => $register->email,
                    'agency'   => $register->agency,
                    'nomember' => $code,
                    'password' => Hash::make($register->nip),
                ]);

                $profileMain = ProfileDataMain::create([
                    'nip'       => $register->nip,
                    'name'      => $register->name,
                    'email'     => $register->email,
                    'contact'   => $register->contact,
                    'active_at' => now(),
                    'gender'    => $gender,
                    'nomember'  => $code,
                ]);

                ProfileDataPosition::create([
                    'main_id'  => $profileMain->id,
                    'agency'   => $register->agency,
                    'position' => $register->position,
                    'level'    => $register->level,
                ]);

                // 5. Update Status Registrasi
                $register->update([
                    'status'      => 'approved',
                    'info'        => $request->info,
                    'emailstatus' => $register->emailstatus + 1
                ]);

                // 6. Kirim Email (Saran: Gunakan Queue jika data > 10)
                Mail::to($register->email)->send(new SendEmailAprrove($member));
            }
        });

        return redirect()->route('admin.registration.index')
            ->with('success', count($registrations) . ' pendaftaran berhasil disetujui.');

    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'Gagal memproses data: ' . $e->getMessage());
    }
}


    public function reject($id)
    {

        $register = Registration::findOrFail($id);

        Mail::to($register['email'])->send(new SendEmailReject($register));

        Registration::where('id', $id)->update([
            'status' => "rejected",
        ]);
        Registration::where('id', $id)->increment('emailstatus');

        //redirect
        return redirect()->route('admin.registration.index');
    }

    public function sendEmail($id)
    {
        $register = Registration::findOrFail($id);

        Mail::to($register['email'])->send(new SendEmailRegistration($register));

        Registration::where('id', $id)->increment('emailstatus');
        //redirect
        return redirect()->route('admin.registration.index');
    }

    public function confirm($id, Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $register = Registration::findOrFail($id);

        Mail::to($request['email'])->send(new SendEmailConfirm($register));

        Registration::where('id', $id)->update([
            'status' => "confirm",
            'info' => $request->info,
            // 'emailstatus'      => 1,
        ]);
        Registration::where('id', $id)->increment('emailstatus');

        //redirect
        return redirect()->route('admin.registration.index');
    }

    public function import()
    {
        return inertia('Admin/Registration/Import');

    }

    public function importStore(Request $request)
    {
        $this->validate($request, [
            'file' => 'required|mimes:csv,xls,xlsx'
        ]);

        //import data
        Excel::import(new RegistrationImport(), $request->file('file'));


        // $registrations = Registration::where('emailstatus', 0)
        // ->where('from', 'colective')
        // ->latest()
        // ->get();


         // Kirim email setelah import selesai
        //  foreach ($registrations as $registration) {

            // Kirim email kepada setiap member
            // Mail::to($registration['email'])->send(new SendEmailRegistration($registration));

            // Update status_email untuk setiap peserta
        //     $registration->increment('emailstatus');
        // }

        //redirect
        return redirect()->route('admin.registration.index');
    }

    public function group()
    {

        $registers = RegistrationGroup::when(request()->q, function($registers) {
            $registers = $registers->where('agency', 'like', '%'. request()->q . '%');
        })->latest()->paginate(10);

        //append query string to pagination links
        $registers->appends(['q' => request()->q]);

        //render with inertia
        return inertia('Admin/Registration/Group', [
            'registers' => $registers,
        ]);

    }

    public function doneGroup($id)
    {
        //get participant
        // $register = Registration::findOrFail($id);
        // return $register;

        RegistrationGroup::where('id', $id)->update([
            'status' => "Done"
        ]);

        //redirect
        return redirect()->route('admin.registration.group');
    }

    public function rejectGroup($id)
    {
        RegistrationGroup::where('id', $id)->update([
            'status' => "rejected"
        ]);

        //redirect
        return redirect()->route('admin.registration.group');
    }

    public function confirmGroup($id)
    {
        RegistrationGroup::where('id', $id)->update([
            'status' => "confirm"
        ]);

        //redirect
        return redirect()->route('admin.registration.group');
    }

        public function generatePassword($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
            }


            public function exportPaid()
            {
                if (auth()->user()->role !== 'administrator') {
                    return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
                }
                $paids = Registration::whereNotNull('paid')
                ->get();

                return Excel::download(new RegistrationPaid($paids), 'Konfirmasi-'.Carbon::now().'.xlsx');
            }

            public function exportRegistration()
            {
                if (auth()->user()->role === 'administrator') {
                       $datas = Registration::oldest()->get();

                return Excel::download(new RegistrationExport($datas), 'DataRegistrasiPer-'.Carbon::now().'.xlsx');
                } else {
                  return redirect()->back()->with('error', 'Anda tidak memiliki izin untuk mengakses halaman ini.');
                }

            }

            public function sendemailApprove($id)
    {
        $register = Registration::findOrFail($id);

        Mail::to($register['email'])->send(new SendEmailAprrove($register));

        Registration::where('id', $id)->increment('emailstatus');
        //redirect
        return redirect()->route('admin.registration.index');
    }


   public function AnggotaLB(Request $request)
    {
        // Data anggota yang akan diinput secara statis
        // Ini adalah array dari objek data yang Anda berikan sebelumnya
        $membersData = [
            [
                'nip'          => '196908241999031001',
                'name'         => 'Prof. Dr. Zudan Arif Fakrulloh, SH., M.H',
                'email'        => 'nisaaa912@gmail.com',
                'agency'       => 'Badan Kepegawaian Negara',
                'position'     => 'Kepala Badan Kepegawaian Negara',
                'level'        => '-',
                'contact'      => '-',
                'document_jab' => null, // Set null karena tidak ada file yang diupload secara nyata
                'paid'         => null, // Set null karena tidak ada file yang diupload secara nyata
            ],
            [
                'nip'          => '196509141992031001',
                'name'         => 'Drs. Haryomo Dwi Putranto, M. Hum.',
                'email'        => 'nisaaa912@gmail.com',
                'agency'       => 'Badan Kepegawaian Negara',
                'position'     => 'Wakil Kepala Badan Kepegawaian Negara',
                'level'        => '-',
                'contact'      => '-',
                'document_jab' => null,
                'paid'         => null,
            ],
            [
                'nip'          => '196605091986032001',
                'name'         => 'Hj. Imas Sukmariah, S.Sos, MAP',
                'email'        => 'nisaaa912@gmail.com',
                'agency'       => 'Badan Kepegawaian Negara',
                'position'     => 'Sekretaris Utama Badan Kepegawaian Negara',
                'level'        => '-',
                'contact'      => '-',
                'document_jab' => null,
                'paid'         => null,
            ],
            [
                'nip'          => '196903161999121001',
                'name'         => 'Dr. Herman., M.Si',
                'email'        => 'nisaaa912@gmail.com',
                'agency'       => 'Badan Kepegawaian Negara',
                'position'     => 'Deputi Bidang Pembinaan Penyelenggaraan Manajemen Aparatur Sipil Negara',
                'level'        => '-',
                'contact'      => '-',
                'document_jab' => null,
                'paid'         => null,
            ],
            [
                'nip'          => '196509111991031001',
                'name'         => 'Drs. Aris Windiyanto, M.Si',
                'email'        => 'nisaaa912@gmail.com',
                'agency'       => 'Badan Kepegawaian Negara',
                'position'     => 'Deputi Bidang Penyelenggaraan Layanan Manajemen Aparatur Sipil Negara',
                'level'        => '-',
                'contact'      => '-',
                'document_jab' => null,
                'paid'         => null,
            ],
            [
                'nip'          => '196702271990031002',
                'name'         => 'Suharmen, S.KOM, Msi',
                'email'        => 'nisaaa912@gmail.com',
                'agency'       => 'Badan Kepegawaian Negara',
                'position'     => 'Deputi Bidang Sistem Informasi dan Digitalisasi Manajemen Aparatur Sipil Negara',
                'level'        => '-',
                'contact'      => '-',
                'document_jab' => null,
                'paid'         => null,
            ],
        ];

        $successCount = 0;
        $errorMessages = [];

        // Loop melalui setiap set data anggota dan simpan ke database
        foreach ($membersData as $memberData) {
            try {
                // Siapkan data untuk disimpan ke model Registration
                // Karena ini bypass, kita asumsikan 'document_jab' dan 'paid' tidak diupload
                // secara real-time, jadi kita gunakan nilai null atau path statis jika ada.
                $dataToCreate = [
                    'nip'          => $memberData['nip'],
                    'name'         => $memberData['name'],
                    'email'        => $memberData['email'],
                    'agency'       => $memberData['agency'],
                    'position'     => $memberData['position'],
                    'level'        => $memberData['level'],
                    'contact'      => $memberData['contact'],
                    'document_jab' => $memberData['document_jab'], // Akan null sesuai definisi di atas
                    'paid'         => $memberData['paid'],         // Akan null sesuai definisi di atas
                ];

                // Atur status menjadi 'paid' jika Anda ingin semua data ini dianggap sudah dibayar
                // Atau Anda bisa menambahkan kondisi di data anggota jika ada yang 'paid' atau tidak
                // Contoh: $dataToCreate['status'] = 'paid'; // Jika semua dianggap paid
                // Atau: $dataToCreate['status'] = ($memberData['paid'] !== null) ? 'paid' : 'pending'; // Jika ingin berdasarkan kolom paid
                $dataToCreate['status'] = 'paid'; // Contoh: semua data ini dianggap berstatus 'paid'

                // Buat record baru di tabel 'registrations'
                Registration::create($dataToCreate);
                $successCount++;

            } catch (\Exception $e) {
                // Tangani error jika terjadi masalah saat menyimpan salah satu record
                // Ini adalah bagian penting untuk debugging!
                Log::error('Gagal menyimpan pendaftaran Anggota LB untuk ' . $memberData['name'] . ': ' . $e->getMessage());
                $errorMessages[] = 'Gagal menyimpan data untuk ' . $memberData['name'] . ': ' . $e->getMessage();
            }
        }

        // Redirect setelah semua data diproses
        if ($successCount > 0 && empty($errorMessages)) {
            return redirect()->route('admin.registration.index')->with('success', $successCount . ' Anggota Luar Biasa berhasil ditambahkan!');
        } elseif ($successCount > 0 && !empty($errorMessages)) {
            return redirect()->route('admin.registration.index')->with('warning', $successCount . ' Anggota berhasil ditambahkan, namun ada beberapa yang gagal: ' . implode(', ', $errorMessages));
        } else {
            return redirect()->back()->withInput()->withErrors(['error' => 'Tidak ada Anggota Luar Biasa yang berhasil ditambahkan. ' . implode(', ', $errorMessages)]);
        }
    }


    public function approveLB($id, Request $request)
    {
        // Validasi input jabatan dari admin
        $request->validate([
            'position' => 'required|string|max:255',
            'info'     => 'nullable|string'
        ]);

        return DB::transaction(function () use ($id, $request) {

            // 1. Ambil data registrasi
            $register = Registration::findOrFail($id);

            // 2. Logika Penomoran khusus LB (Luar Biasa)
            // Menghitung total approved yang bukan Analis atau Pranata
            $number = Registration::where('status', 'approved')
                ->whereNotIn('position', ['Analis SDM Aparatur', 'Pranata SDM Aparatur'])
                ->count() + 1;

            $code = str_pad($number, 4, '0', STR_PAD_LEFT) . "/LB/ASPROSDMA";

            // 3. Logika Gender dari NIP
            $gender = 'L';
            if (strlen($register->nip) >= 15) {
                $genderCode = substr($register->nip, 14, 1);
                $gender = ($genderCode === '2') ? 'P' : 'L';
            }

            // 4. Create Member
            $member = Member::create([
                'nip'            => $register->nip,
                'name'           => $register->name,
                'email'          => $register->email,
                'agency'         => $register->agency,
                'nomember'       => $code,
                'password'       => Hash::make($register->nip),
            ]);

            // 5. Create Profile Data Main
            $profileMain = ProfileDataMain::create([
                'nip'             => $register->nip,
                'name'            => $register->name,
                'email'           => $register->email,
                'contact'         => $register->contact,
                'active_at'       => Carbon::now(),
                'gender'          => $gender,
                'nomember'        => $code,
            ]);

            // 6. Create Profile Data Position
            // Menggunakan input position dari Admin dan level '-'
            ProfileDataPosition::create([
                'main_id'         => $profileMain->id,
                'agency'          => $register->agency,
                'position'        => $request->position, // Input manual dari admin
                'level'           => '-',                // Level otomatis strip
            ]);

            // 7. Update Registration
            $register->update([
                'info'     => $request->info,
                'status'   => 'approved',
                // Update position di tabel registrasi agar sinkron dengan input admin
                'position' => $request->position,
                'level'    => '-',
            ]);

            return redirect()->route('admin.registration.index')
                             ->with('success', 'Anggota Luar Biasa berhasil disetujui dengan jabatan: ' . $request->position);
        });
    }

}
