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

        if ($existingRegistration) {
            if ($existingRegistration->status !== 'rejected') {
            return redirect()->back()->withErrors([
                'nip' => 'Anda sudah terdaftar dengan NIP, email, atau kontak ini.'
            ]);
            }
            // Jika status 'rejected', validasi tanpa unique NIP
            $existingRegistration->update([
                'nip' => $existingRegistration->nip . '-1',

            ]);

            $validatedData = $request->validate([
            'nip' => ['required', 'string', 'regex:/^\d{18}$/'],
            'name' => 'required|string',
            'email' => 'required|email|unique:registrations,email',
            'contact' => 'required|string|unique:registrations,contact',
            'agency' => 'required|string',
            'position' => 'required|string',
            'level' => 'required|string',
            'document_jab' => 'required|file|mimes:pdf|max:2048',
            ],
            [
            'nip.regex' => 'NIP harus terdiri dari 18 angka.',
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
        } else {
            // Jika tidak ada registration, validasi dengan unique NIP
            $validatedData = $request->validate([
            'nip' => ['required', 'string', 'regex:/^\d{18}$/', 'unique:registrations,nip'],
            'name' => 'required|string',
            'email' => 'required|email|unique:registrations,email',
            'contact' => 'required|string|unique:registrations,contact',
            'agency' => 'required|string',
            'position' => 'required|string',
            'level' => 'required|string',
            'document_jab' => 'required|file|mimes:pdf|max:2048',
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
            'document_jab.max' => 'Ukuran Dokumen tidak boleh lebih dari 2MB.',
            ]);
        }

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

    // Create registration
    $registration = Registration::create(array_merge($validatedData, ['document_jab' => $document_jab, 'from' => 'individu']));
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

        if ($register->paid) {
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
        'paid' => 'required|max:2048', // Adjust validation rule for file
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
        'total' => 'required|string',
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
