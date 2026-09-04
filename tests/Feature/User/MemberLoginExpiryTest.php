<?php

namespace Tests\Feature\User;

use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MemberLoginExpiryTest extends TestCase
{
    use RefreshDatabase;

    private function makeMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'nip' => '199001012020121005',
            'name' => 'Anggota Login',
            'email' => 'anggotalogin@example.com',
            'nomember' => '00004/01/ASPROSDMA',
            'password' => bcrypt('password'),
            'agency' => 'Instansi Test',
        ], $overrides));
    }

    private function fakeValidRecaptcha(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true, 'score' => 0.9]),
        ]);
    }

    public function test_login_succeeds_for_a_member_whose_membership_is_still_active()
    {
        $this->fakeValidRecaptcha();
        $this->makeMember(['expires_at' => now()->addMonths(6)]);

        $response = $this->post('/user/login', [
            'nip' => '199001012020121005',
            'password' => 'password',
            'recaptcha_token' => 'fake-token',
        ]);

        $response->assertRedirect(route('user.dashboard'));
        $this->assertAuthenticated('member');
    }

    public function test_login_succeeds_for_a_member_with_no_expiry_set()
    {
        $this->fakeValidRecaptcha();
        $this->makeMember(['expires_at' => null]);

        $response = $this->post('/user/login', [
            'nip' => '199001012020121005',
            'password' => 'password',
            'recaptcha_token' => 'fake-token',
        ]);

        $response->assertRedirect(route('user.dashboard'));
    }

    public function test_login_is_blocked_for_an_expired_member()
    {
        $this->fakeValidRecaptcha();
        $this->makeMember(['expires_at' => now()->subDay()]);

        $response = $this->post('/user/login', [
            'nip' => '199001012020121005',
            'password' => 'password',
            'recaptcha_token' => 'fake-token',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertGuest('member');
    }

    public function test_expired_member_is_force_logged_out_mid_session()
    {
        $member = $this->makeMember(['expires_at' => now()->addMinute()]);

        // Membership is still valid at the start of the request, but expires
        // between requests - the middleware must catch that on the very next
        // page load rather than only checking once at login time.
        $this->travel(2)->minutes();

        $response = $this->actingAs($member, 'member')->get('/user/setting');

        $response->assertRedirect('/user/login');
        $response->assertSessionHas('error');
        $this->assertGuest('member');
    }

    public function test_member_with_active_membership_is_not_disturbed_mid_session()
    {
        $member = $this->makeMember(['expires_at' => now()->addMonths(6)]);

        $response = $this->actingAs($member, 'member')->get('/user/setting');

        $response->assertOk();
    }
}
