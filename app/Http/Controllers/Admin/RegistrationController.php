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
     return redirect()->route('admin.registration.index')->with('success', 'Data registrasi berhasil ditambahkan.');
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

            $extra = [];

            if ($document_jab) {
                $extra['document_jab'] = $document_jab->storePublicly('/document');
            }

            if ($paid) {
                $extra['paid'] = $paid->storePublicly('/images');
                $extra['status'] = 'paid';
            }

            Registration::where('id', $id)->update(array_merge($validatedData, $extra));

       //redirect
       return redirect()->route('admin.registration.index')->with('success', 'Data registrasi berhasil diperbarui.');
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
        return redirect()->route('admin.registration.index')->with('success', 'Data registrasi berhasil dihapus.');
    }

    public function paid($id)
    {
        //get
        Registration::where('id', $id)->update([
            'status' => "paid"
        ]);

        //redirect
        return redirect()->route('admin.registration.index')->with('success', 'Status pembayaran berhasil diperbarui.');
    }

    public function hadlecode()
    {


    }

    public function approve($id, Request $request)
    {
        $registration = Registration::findOrFail($id);
        $result = $this->attemptApproval($registration, $request->info);

        if ($result['already_processed']) {
            return redirect()->route('admin.registration.index')
                ->with('info', 'Pendaftaran ini sudah diproses sebelumnya.');
        }

        if (!$result['success']) {
            return back()->with('error', 'Gagal menyetujui pendaftaran karena gangguan sistem. Silakan coba lagi.');
        }

        return redirect()->route('admin.registration.index')
            ->with('success', 'Pendaftaran berhasil disetujui dengan nomor: ' . $result['member']->nomember);
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

        $approved = 0;
        $skipped = 0;
        $failed = 0;

        foreach ($registrations as $registration) {
            $result = $this->attemptApproval($registration, $request->info);

            if ($result['already_processed']) {
                $skipped++;
            } elseif ($result['success']) {
                $approved++;
            } else {
                $failed++;
            }
        }

        $message = "{$approved} pendaftaran berhasil disetujui.";
        if ($skipped > 0) {
            $message .= " {$skipped} dilewati (sudah diproses sebelumnya).";
        }
        if ($failed > 0) {
            $message .= " {$failed} gagal diproses karena gangguan sistem, silakan coba lagi untuk data tersebut.";
        }

        return redirect()->route('admin.registration.index')
            ->with($failed > 0 ? 'error' : 'success', $message);
    }

    /**
     * Approve a single registration: create the Member/ProfileDataMain/
     * ProfileDataPosition rows and mark the registration approved.
     *
     * Two things used to make this fail intermittently:
     *   - The next membership number was read without locking the row it was
     *     derived from, so two approvals running at (almost) the same time -
     *     two admins, or two rows in a bulk approve - could compute the same
     *     number and collide on the unique `nomember` constraint.
     *   - The registration's own status wasn't checked/locked before
     *     processing, so a double click or a retried request could attempt
     *     to approve (and create a duplicate Member for) the same
     *     registration twice.
     *
     * This locks the registration row and the membership-number source row
     * for the duration of the transaction, and retries a few times
     * specifically on a unique-constraint collision (the one case row
     * locking can't fully prevent: the very first member ever created for a
     * given position, where there's no existing row to lock). Email is sent
     * after the transaction commits, so a slow/failing mail server can't
     * hold a lock open or roll back an otherwise-successful approval.
     *
     * @return array{success: bool, already_processed: bool, member: ?Member}
     */
    private function attemptApproval(Registration $registration, ?string $info): array
    {
        $maxAttempts = 3;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $outcome = DB::transaction(function () use ($registration, $info) {
                    $locked = Registration::where('id', $registration->id)->lockForUpdate()->firstOrFail();

                    if (in_array($locked->status, ['approved', 'rejected'], true)) {
                        return ['already_processed' => true, 'member' => null];
                    }

                    $code = $this->nextMemberNumber($locked->position);
                    $gender = (strlen($locked->nip) >= 15 && substr($locked->nip, 14, 1) === '2') ? 'P' : 'L';

                    $member = Member::create([
                        'nip'      => $locked->nip,
                        'name'     => $locked->name,
                        'email'    => $locked->email,
                        'agency'   => $locked->agency,
                        'nomember' => $code,
                        'password' => Hash::make($locked->nip),
                        'expires_at' => now()->addYear(),
                    ]);

                    $profileMain = ProfileDataMain::create([
                        'nip'       => $locked->nip,
                        'name'      => $locked->name,
                        'email'     => $locked->email,
                        'contact'   => $locked->contact,
                        'active_at' => now(),
                        'gender'    => $gender,
                        'nomember'  => $code,
                    ]);

                    ProfileDataPosition::create([
                        'main_id'  => $profileMain->id,
                        'agency'   => $locked->agency,
                        'position' => $locked->position,
                        'level'    => $locked->level,
                    ]);

                    $locked->update([
                        'info'        => $info,
                        'status'      => 'approved',
                        'emailstatus' => $locked->emailstatus + 1,
                    ]);

                    return ['already_processed' => false, 'member' => $member];
                });

                if ($outcome['already_processed']) {
                    return ['success' => false, 'already_processed' => true, 'member' => null];
                }

                $member = $outcome['member'];

                try {
                    Mail::to($member->email)->send(new SendEmailAprrove($member));
                } catch (\Throwable $mailError) {
                    Log::error('Gagal mengirim email approval registrasi', [
                        'registration_id' => $registration->id,
                        'error' => $mailError->getMessage(),
                    ]);
                }

                return ['success' => true, 'already_processed' => false, 'member' => $member];
            } catch (\Illuminate\Database\QueryException $e) {
                if ($this->isDuplicateKeyViolation($e) && $attempt < $maxAttempts) {
                    usleep(50000 * $attempt);
                    continue;
                }

                Log::error('Gagal menyetujui pendaftaran', [
                    'registration_id' => $registration->id,
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                ]);

                return ['success' => false, 'already_processed' => false, 'member' => null];
            }
        }

        return ['success' => false, 'already_processed' => false, 'member' => null];
    }

    private function nextMemberNumber(string $position): string
    {
        [$suffix, $padLength] = $this->positionSuffix($position);

        return $this->lockedNextNumber($suffix, $padLength);
    }

    /**
     * Draw the next number for a suffix from its dedicated counter row in
     * member_number_sequences, rather than scanning/locking `members`
     * itself. That old approach used `LIKE '%suffix'`, a leading wildcard
     * that can't use the unique index on nomember - it full-table-scanned
     * (and lockForUpdate() locked across) the entire members table on
     * every single approval, and had nothing to lock at all for the very
     * first member of a suffix. Both made concurrent approvals collide or
     * block far more than they needed to. Locking one indexed row per
     * suffix confines contention to approvals racing for that exact
     * suffix, and the row always exists so there's no phantom-lock gap.
     */
    private function lockedNextNumber(string $suffix, int $padLength): string
    {
        DB::table('member_number_sequences')->insertOrIgnore([
            'suffix' => $suffix,
            'last_number' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sequence = DB::table('member_number_sequences')
            ->where('suffix', $suffix)
            ->lockForUpdate()
            ->first();

        $nextNumber = $sequence->last_number + 1;

        DB::table('member_number_sequences')
            ->where('suffix', $suffix)
            ->update([
                'last_number' => $nextNumber,
                'updated_at' => now(),
            ]);

        return str_pad($nextNumber, $padLength, '0', STR_PAD_LEFT) . $suffix;
    }

    private function positionSuffix(string $position): array
    {
        $positionMap = [
            'Analis SDM Aparatur' => '/01/ASPROSDMA',
            'Pranata SDM Aparatur' => '/02/ASPROSDMA',
        ];

        $suffix = $positionMap[$position] ?? '/LB/ASPROSDMA';
        $padLength = $position === 'Pranata SDM Aparatur' ? 4 : 5;

        return [$suffix, $padLength];
    }

    private function isDuplicateKeyViolation(\Illuminate\Database\QueryException $e): bool
    {
        return $e->getCode() === '23000';
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
        return redirect()->route('admin.registration.index')->with('success', 'Pendaftaran berhasil ditolak.');
    }

    public function sendEmail($id)
    {
        $register = Registration::findOrFail($id);

        Mail::to($register['email'])->send(new SendEmailRegistration($register));

        Registration::where('id', $id)->increment('emailstatus');
        //redirect
        return redirect()->route('admin.registration.index')->with('success', 'Email permintaan pembayaran berhasil dikirim.');
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
        return redirect()->route('admin.registration.index')->with('success', 'Link perbaikan berhasil dikirim.');
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
        return redirect()->route('admin.registration.index')->with('success', 'Data registrasi berhasil diimport.');
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
        return redirect()->route('admin.registration.group')->with('success', 'Grup registrasi berhasil diselesaikan.');
    }

    public function rejectGroup($id)
    {
        RegistrationGroup::where('id', $id)->update([
            'status' => "rejected"
        ]);

        //redirect
        return redirect()->route('admin.registration.group')->with('success', 'Grup registrasi berhasil ditolak.');
    }

    public function confirmGroup($id)
    {
        RegistrationGroup::where('id', $id)->update([
            'status' => "confirm"
        ]);

        //redirect
        return redirect()->route('admin.registration.group')->with('success', 'Grup registrasi berhasil dikonfirmasi.');
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
        return redirect()->route('admin.registration.index')->with('success', 'Email berhasil dikirim ulang.');
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

        $maxAttempts = 3;

        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            try {
                $outcome = DB::transaction(function () use ($id, $request) {
                    // Lock the registration row so a double click / retried
                    // request can't process the same registration twice.
                    $register = Registration::where('id', $id)->lockForUpdate()->firstOrFail();

                    if (in_array($register->status, ['approved', 'rejected'], true)) {
                        return ['already_processed' => true];
                    }

                    // Penomoran khusus LB (Luar Biasa), diambil dari nomor
                    // member terakhir dengan suffix yang sama (bukan dihitung
                    // dari jumlah registrasi) dan dikunci agar tidak tabrakan
                    // dengan approval lain yang berjalan bersamaan.
                    $code = $this->lockedNextNumber('/LB/ASPROSDMA', 4);

                    $gender = 'L';
                    if (strlen($register->nip) >= 15) {
                        $genderCode = substr($register->nip, 14, 1);
                        $gender = ($genderCode === '2') ? 'P' : 'L';
                    }

                    Member::create([
                        'nip'            => $register->nip,
                        'name'           => $register->name,
                        'email'          => $register->email,
                        'agency'         => $register->agency,
                        'nomember'       => $code,
                        'password'       => Hash::make($register->nip),
                        'expires_at'     => now()->addYear(),
                    ]);

                    $profileMain = ProfileDataMain::create([
                        'nip'             => $register->nip,
                        'name'            => $register->name,
                        'email'           => $register->email,
                        'contact'         => $register->contact,
                        'active_at'       => Carbon::now(),
                        'gender'          => $gender,
                        'nomember'        => $code,
                    ]);

                    ProfileDataPosition::create([
                        'main_id'         => $profileMain->id,
                        'agency'          => $register->agency,
                        'position'        => $request->position, // Input manual dari admin
                        'level'           => '-',                // Level otomatis strip
                    ]);

                    $register->update([
                        'info'     => $request->info,
                        'status'   => 'approved',
                        // Update position di tabel registrasi agar sinkron dengan input admin
                        'position' => $request->position,
                        'level'    => '-',
                    ]);

                    return ['already_processed' => false, 'code' => $code];
                });

                if ($outcome['already_processed']) {
                    return redirect()->route('admin.registration.index')
                        ->with('info', 'Pendaftaran ini sudah diproses sebelumnya.');
                }

                return redirect()->route('admin.registration.index')
                    ->with('success', 'Anggota Luar Biasa berhasil disetujui dengan jabatan: ' . $request->position);
            } catch (\Illuminate\Database\QueryException $e) {
                if ($this->isDuplicateKeyViolation($e) && $attempt < $maxAttempts) {
                    usleep(50000 * $attempt);
                    continue;
                }

                Log::error('Gagal menyetujui pendaftaran anggota luar biasa', [
                    'registration_id' => $id,
                    'attempt' => $attempt,
                    'error' => $e->getMessage(),
                ]);

                return back()->with('error', 'Gagal menyetujui pendaftaran karena gangguan sistem. Silakan coba lagi.');
            }
        }

        return back()->with('error', 'Gagal menyetujui pendaftaran karena gangguan sistem. Silakan coba lagi.');
    }

}
