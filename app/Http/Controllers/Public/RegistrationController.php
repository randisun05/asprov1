<?php

namespace App\Http\Controllers\Public;

use Inertia\Inertia;
use App\Models\instansi;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\RegistrationGroup;
use App\Mail\SendEmailRegistration;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use PHPUnit\TextUI\XmlConfiguration\Group;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx\Rels;
use Illuminate\Support\Facades\Storage;


class RegistrationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $token = $request->session()->get('token');
        $register = $request->session()->get('registration');

        return inertia('Public/Registration/Success', [
            'token' => $token,
            'register' => $register
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
        return inertia('Public/Registration/Registration', [
            'instansis' => $instansis
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
        // Check if the user is already registered
        $existingRegistration = Registration::where(function ($query) use ($request) {
            $query->where('nip', $request->nip)
            ->orWhere('email', $request->email)
            ->orWhere('contact', $request->contact);
        })->first();

        if ($existingRegistration && $existingRegistration->status !== 'rejected') {
            return redirect()->back()->withErrors([
                'nip' => 'Anda sudah terdaftar dengan NIP, email, atau kontak ini.'
            ]);
        }

        // A rejected applicant is allowed to resubmit, but nip/email/contact
        // are all unique at the DB level - the old code "freed up" the nip
        // by renaming it to "<nip>-1" and left email/contact untouched,
        // which meant resubmitting with the *same* email or phone (the
        // overwhelmingly likely case, since it's their own real contact
        // info) still failed uniqueness validation and corrupted the old
        // row's nip permanently in the process. Ignoring the existing
        // rejected row's own id in the uniqueness check - and reusing that
        // row instead of inserting a new one - avoids the DB constraint
        // entirely without mangling any data.
        $ignoreId = $existingRegistration?->id;

        $validatedData = $request->validate([
            'nip' => ['required', 'string', 'regex:/^\d{18}$/', Rule::unique('registrations', 'nip')->ignore($ignoreId)],
            'name' => 'required|string',
            'email' => ['required', 'email', Rule::unique('registrations', 'email')->ignore($ignoreId)],
            'contact' => ['required', 'string', Rule::unique('registrations', 'contact')->ignore($ignoreId)],
            'agency' => 'required|string',
            'position' => 'required|string',
            'level' => 'required|string',
            'document_jab' => 'required|file|mimes:pdf|max:2048',
        ], [
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
            'document_jab.max' => 'Ukuran Dokumen tidak boleh lebih dari 2MB.',
        ]);

    $request->validate([
        'code' => 'required|string',
        'captcha' => 'required|same:code',
        'term' => 'in:1',
    ], [
        'captcha.required' => 'Captcha harus diisi.',
        'captcha.same' => 'Captcha Salah.',
        'term.in' => 'Checklist jika bersedia.',
    ]);


 // Store the file using Laravel's file storage system
    $document_jab = $request->file('document_jab')->storePublicly('/documents');

    if ($existingRegistration) {
        $existingRegistration->update(array_merge($validatedData, [
            'document_jab' => $document_jab,
            'status' => 'submission',
            'from' => 'individu',
        ]));
        $registration = $existingRegistration;
    } else {
        $registration = Registration::create(array_merge($validatedData, ['document_jab' => $document_jab, 'from' => 'individu']));
    }
    $token = $registration->id;

     //redirect
     return redirect()->route('registration.success')->with([
        'token' => $token,
        'registration' => $registration
    ]);
}


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $register = Registration::findOrFail($id);

        if($register->paid)
        // Jika status registrasi bukan 'confirm', arahkan pengguna kembali atau tampilkan pesan kesalahan
        return redirect()->route('/')->with('error', 'Link paid telah ditutup.');

        return inertia('Public/Registration/Show', [
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

        if( $register->status !== 'confirm')
        // Jika status registrasi bukan 'confirm', arahkan pengguna kembali atau tampilkan pesan kesalahan
        return redirect()->route('/')->with('error', 'Link konfirmasi telah ditutup.');

        $instansis = instansi::get();
        return inertia('Public/Registration/Edit', [
           'register' => $register,
           'instansis' =>   $instansis
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
        'document_jab' => 'nullable|file|mimes:pdf|max:2048',
        'paid' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ], [
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
        'document_jab.mimes' => 'SK jabatan harus berupa file PDF.',
        'document_jab.max' => 'Ukuran SK jabatan tidak boleh lebih dari 2MB.',
        'paid.mimes' => 'Bukti transfer harus berupa file JPG, PNG, atau PDF.',
        'paid.max' => 'Ukuran bukti transfer tidak boleh lebih dari 2MB.',
    ]);

            // document_jab/paid are only in $validatedData for their file
            // validation rules above - both are optional here, and merging
            // a null value for whichever one wasn't uploaded this time
            // would wipe out the column already set from a previous step.
            // Each branch below re-adds the one(s) that were actually
            // uploaded, as a stored path string.
            $validatedData = collect($validatedData)->except(['document_jab', 'paid'])->all();

            // Store the file using Laravel's file storage system
            $document_jab = $request->file('document_jab');
            $paid = $request->file('paid');

            if ($document_jab && $paid) {
                // Jika keduanya diisi, update semua
                $document_jab = $document_jab->storePublicly('/documents');
                $paid = $paid->storePublicly('/images');
                Registration::where('id',$id)->update(array_merge($validatedData, [
                    'document_jab' => $document_jab,
                    'paid' => $paid,
                    'status' => "paid"
                ]));
            } elseif ($document_jab) {
                $document_jab = $document_jab->storePublicly('/documents');
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
            } else { // Buat registration
                Registration::where('id',$id)->update(array_merge($validatedData, [ 'status' => 'submission'
                ]));
            }

     //redirect
     return redirect()->route('registration.berhasil')->with('success', 'Registrasi berhasil dikonfirmasi.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Show the Midtrans Snap payment page as an alternative to manually
     * uploading proof of transfer. Requires MIDTRANS_SERVER_KEY and
     * MIDTRANS_REGISTRATION_FEE to be configured.
     */
    public function payment($id, \App\Services\MidtransService $midtrans)
    {
        $register = Registration::findOrFail($id);

        // $register->paid only covers the manual proof-of-transfer upload;
        // an online Midtrans payment instead moves `status` to 'paid' (and
        // beyond, once admin reviews it) without ever touching `paid`. Check
        // both so someone who already paid online can't reopen this page and
        // create a second Snap transaction.
        if ($register->paid || in_array($register->status, ['paid', 'confirm', 'approved'], true)) {
            return redirect()->route('/')->with('error', 'Link pembayaran telah ditutup.');
        }

        try {
            $snap = $midtrans->createSnapToken($register);
        } catch (\RuntimeException $e) {
            return back()->with('error', 'Pembayaran online belum tersedia. Silakan unggah bukti transfer manual.');
        }

        return inertia('Public/Registration/Payment', [
            'register' => $register,
            'snapToken' => $snap['token'],
            'clientKey' => config('midtrans.client_key'),
            'isProduction' => config('midtrans.is_production'),
        ]);
    }

    public function paid(Request $request, $id)
    {

        // Validate request including file validation
    $request->validate([
        'paid' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
    ], [
        'paid.mimes' => 'Bukti transfer harus berupa file JPG, PNG, atau PDF.',
        'paid.max' => 'Ukuran bukti transfer tidak boleh lebih dari 2MB.',
    ]);

    // Store the file using Laravel's file storage system
    $paid = $request->file('paid')->storePublicly('/images');

    // Create registration
    Registration::where('id', $id)->update(['paid' => $paid, 'status' => "paid"]);

     //redirect
     return redirect('/registration')->with('success','Data berhasil diupdate.');

    }

    public function group()
    {
        $instansis = instansi::get();
        return inertia('Public/Registration/Group', [
            'instansis' => $instansis,
         ]);
    }

    public function groupStore(Request $request)
    {
        // Validate request including file validation
      $validatedData = $request->validate([
        'agency' => 'required|string',
        'name' => 'required|string',
        'email' => 'required|email|unique:registration_groups,email',
        'contact' => 'required|string|unique:registration_groups,contact',
        'total' => 'required|integer|min:1',
        'file' => 'required|file|mimes:xls,xlsx|max:2048', // Ensure 'document_jab' is a valid file
    ],
            [
                'email.unique' => 'Data email sudah digunakan.',
                'contact.unique' => 'Data kontak sudah digunakan.',
                'agency.required' => 'Instansi harus diisi.',
                'name.required' => 'Nama harus diisi.',
                'email.required' => 'Email harus diisi.',
                'contact.required' => 'Kontak harus diisi.',
                'total.required' => 'Total data harus diisi.',
                'total.integer' => 'Total data harus berupa angka.',
                'total.min' => 'Total data minimal 1.',
                'file.required' => 'File harus diisi.',
            ]);

            $request->validate([
                'code' => 'required|string',
                'captcha' => 'required|same:code',
                'term' => 'in:1',
            ], [
                'captcha.same' => 'Captcha Salah.',
                'captcha.required' => 'Captcha harus diisi.',
                'term.in' => 'Checklist jika bersedia.',
            ]);


    // Store the file using Laravel's file storage system
    $file = $request->file('file')->storePublicly('/documents');

    // Create registration
    $register = RegistrationGroup::create(array_merge($validatedData, ['file' => $file]));
    $token = $register->id;

     //redirect
     return inertia('Public/Registration/GroupSuccess', [
        'register' => $register,
        'token' => $token,
     ]);

    }

    public function confirm($id)
    {
        return inertia('Public/Registration/Confirm', [

        ]);
    }

    public function berhasil()
    {
        return inertia('Public/Registration/Berhasil', [

        ]);
    }



}
