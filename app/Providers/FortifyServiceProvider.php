<?php

namespace App\Providers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Laravel\Fortify\Fortify;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use App\Actions\Fortify\CreateNewUser;
use App\Http\Responses\LogoutResponse;
use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Actions\Fortify\UpdateUserProfileInformation;



class FortifyServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        //login
        Fortify::loginView(function () {
            return Inertia::render('Auth/Login');
        });

        Fortify::twoFactorChallengeView(function () {
            return Inertia::render('Auth/TwoFactorChallenge');
        });

        Fortify::confirmPasswordView(function () {
            return Inertia::render('Auth/ConfirmPassword');
        });

        Fortify::authenticateUsing(function (Request $request) {
            // Fortify's login pipeline calls this callback from two separate
            // stages (RedirectIfTwoFactorAuthenticatable, then
            // AttemptToAuthenticate) for a single login POST. A reCAPTCHA v3
            // token can only be verified once - Google rejects it the second
            // time - so without this cache every login failed on the second,
            // redundant verification even with a perfectly valid token.
            if ($request->attributes->has('fortify_authenticated_user')) {
                return $request->attributes->get('fortify_authenticated_user');
            }

            $request->validate([
                'email' => 'required|string',
                'password' => 'required|string',
                'recaptcha_token' => 'required|string',
            ], [
                'recaptcha_token.required' => 'reCAPTCHA token is missing. Please try again.',
            ]);

            $verification = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $request->recaptcha_token,
                'remoteip' => $request->ip(),
            ])->json();

            if (!($verification['success'] ?? false) || ($verification['score'] ?? 0) < 0.5) {
                throw ValidationException::withMessages([
                    'recaptcha_token' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.',
                ]);
            }

            $user = User::where('email', $request->email)->first();
            $authenticated = ($user && Hash::check($request->password, $user->password)) ? $user : null;

            $request->attributes->set('fortify_authenticated_user', $authenticated);

            return $authenticated;
        });


        /**
         * logout
         */
        $this->app->singleton(\Laravel\Fortify\Contracts\LogoutResponse::class,LogoutResponse::class);
    }
}
