<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorEnforcementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_administrator_without_confirmed_2fa_is_redirected_to_setup()
    {
        $admin = $this->makeAdminUser('administrator', [], twoFactorConfirmed: false);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertRedirect(route('admin.security.two-factor'));
    }

    public function test_setup_page_itself_is_reachable_without_2fa_confirmed()
    {
        $admin = $this->makeAdminUser('administrator', [], twoFactorConfirmed: false);

        $this->actingAs($admin)->get('/admin/security/two-factor')->assertOk();
    }

    public function test_role_without_2fa_requirement_is_not_redirected()
    {
        $humas = $this->makeAdminUser('humas', [], twoFactorConfirmed: false);

        $this->actingAs($humas)->get('/admin/dashboard')->assertOk();
    }

    public function test_administrator_with_confirmed_2fa_is_not_redirected()
    {
        $admin = $this->makeAdminUser('administrator');

        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
    }

    public function test_full_setup_and_confirmation_flow()
    {
        $admin = $this->makeAdminUser('administrator', [], twoFactorConfirmed: false);

        $this->actingAs($admin)
            ->withSession(['auth.password_confirmed_at' => time()])
            ->postJson('/user/two-factor-authentication')
            ->assertStatus(200);

        $admin->refresh();
        $this->assertNotNull($admin->two_factor_secret);
        $this->assertNull($admin->two_factor_confirmed_at);

        $secret = decrypt($admin->two_factor_secret);
        $validCode = (new Google2FA())->getCurrentOtp($secret);

        $response = $this->actingAs($admin)->postJson('/user/confirmed-two-factor-authentication', [
            'code' => $validCode,
        ]);

        $response->assertStatus(200);
        $this->assertNotNull($admin->fresh()->two_factor_confirmed_at);

        // Now that 2FA is confirmed, the enforcement redirect should be gone.
        $this->actingAs($admin)->get('/admin/dashboard')->assertOk();
    }
}
