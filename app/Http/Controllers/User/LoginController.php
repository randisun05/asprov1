<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use App\Models\Member;
use Illuminate\Http\Request;
use PhpParser\Node\Stmt\Else_;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Intervention\Image\Colors\Rgb\Channels\Red;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
       // Validate the form data
            $this->validate($request, [
                'nip'      => 'required',
                'password'  => 'required',
                 'recaptcha_token' => 'required|string',
            ],[
                'nip.required' => 'NIP harus diisi',
                'password.required' => 'Password harus diisi',
                'recaptcha_token.required' => 'reCAPTCHA token is missing. Please try again.',
            ]);

             // ✅ 2. Verifikasi token ke Google
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret'   => env('RECAPTCHA_SECRET_KEY'),
                'response' => $request->recaptcha_token,
                'remoteip' => $request->ip(),
            ]);

             Log::info('Recaptcha Response:', $response->json());
              $result = $response->json();

              // ✅ 3. Pastikan berhasil diverifikasi
        if (!($result['success'] ?? false)) {
            return back()->withErrors([
                'recaptcha' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.',
            ])->withInput();
        }

        // ✅ 4. (Opsional) Cek skor minimum (nilai 0–1)
        if (($result['score'] ?? 0) < 0.5) {
            return back()->withErrors([
                'recaptcha' => 'Aktivitas mencurigakan terdeteksi. Silakan coba lagi.',
            ])->withInput();
        }

            // Cek nip dan password
            $member = Member::where('nip', $request->nip)->first();

            if (!$member) {
                return redirect()->back()->with('error', 'NIP belum terdaftar. Silakan lakukan pendaftaran keanggotaan.');
            } elseif (!password_verify($request->password, $member->password)) {
                return redirect()->back()->with('error', 'NIP atau Password salah');
            }

            // Login the user
            auth()->guard('member')->login($member);

            // Redirect to dashboard
           return redirect()->intended(route('user.dashboard'));
    }

    public function setting()
    {
        $member = Member::where('nip',auth()->guard('member')->user()->nip)
        ->first();


        return inertia('User/Setting/Index', [
            'member' => $member,
         ]);



    }

    public function resetPassword(Request $request)
    {

        $member = Member::where('nip', auth()->guard('member')->user()->nip)->first();

        $request->validate([
            'oldpassword' => 'required',
            'password' => [
                'required',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_])[A-Za-z\d\W_]+$/',
                'confirmed',
                // At least one lowercase, one uppercase, one number, and one special character
            ],
        ],[
            'password.regex' => 'Password terdiri dari kombinasi huruf kapaital, huruf kecil, angka dan karakter spesial, contoh:A5proSDM@',
            'oldpassword.required' => 'Password lama harus diisi',
            'password.required' => 'Password baru harus diisi',
            'password.confirmed' => 'Konfirmasi password tidak sama',
            'oldpassword.min:8' => 'Password baru minimal 8 karakter'
        ]);

        // Check if the old password matches
            if (!$member || !Hash::check($request->oldpassword, $member->password)) {
                return back()->withErrors(['oldpassword' => 'Password lama yang Anda masukkan tidak cocok']);
            }

            // Hash the new password
            $newPasswordHash = Hash::make($request->password);

            // Update the member's password
            $member->update([
                'password' => $newPasswordHash,
            ]);

            return redirect()->route('user.dashboard')->with('success', 'Password berhasil diperbarui.');
    }
}
