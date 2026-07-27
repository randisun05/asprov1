<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AdminLoginRecaptchaTest extends TestCase
{
    use RefreshDatabase;

    private function makeAdmin(): User
    {
        return User::create([
            'nip' => '198001012010121001',
            'name' => 'Admin Test',
            'email' => 'admin@example.com',
            'role' => 'administrator',
            'position' => 'Sekretariat',
            'ref' => '-',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_login_is_rejected_without_a_recaptcha_token()
    {
        $this->makeAdmin();

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('recaptcha_token');
        $this->assertGuest();
    }

    public function test_login_is_rejected_when_recaptcha_verification_fails()
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => false]),
        ]);
        $this->makeAdmin();

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
            'recaptcha_token' => 'fake-token',
        ]);

        $response->assertSessionHasErrors('recaptcha_token');
        $this->assertGuest();
    }

    public function test_login_succeeds_with_valid_recaptcha_and_credentials()
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true, 'score' => 0.9]),
        ]);
        $this->makeAdmin();

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
            'recaptcha_token' => 'fake-token',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs(User::first());
    }

    public function test_recaptcha_token_is_only_verified_once_per_login_attempt()
    {
        // Fortify's login pipeline invokes Fortify::authenticateUsing() from
        // two separate stages for a single POST /login. A real reCAPTCHA v3
        // token is single-use - Google rejects it on a second verification -
        // so if this endpoint were hit twice with a fake that only succeeds
        // once, login would incorrectly fail. Asserting exactly one call
        // pins down that the request-scoped cache prevents that.
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::sequence()
                ->push(['success' => true, 'score' => 0.9])
                ->push(['success' => false, 'error-codes' => ['timeout-or-duplicate']]),
        ]);
        $this->makeAdmin();

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
            'recaptcha_token' => 'fake-token',
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs(User::first());
        Http::assertSentCount(1);
    }

    public function test_login_fails_with_valid_recaptcha_but_wrong_password()
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true, 'score' => 0.9]),
        ]);
        $this->makeAdmin();

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
            'recaptcha_token' => 'fake-token',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
