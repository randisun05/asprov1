<?php

namespace Tests\Feature\Public;

use App\Mail\SendEmailForgetPassword;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MemberPasswordResetTest extends TestCase
{
    use RefreshDatabase;

    private function makeMember(array $overrides = []): Member
    {
        return Member::create(array_merge([
            'nip' => '199001012020121010',
            'name' => 'Anggota Reset',
            'email' => 'anggotareset@example.com',
            'agency' => 'Instansi Test',
            'nomember' => '00010/01/ASPROSDMA',
            'password' => bcrypt('password-lama'),
        ], $overrides));
    }

    public function test_requesting_a_reset_link_sets_an_expiry()
    {
        Mail::fake();
        $member = $this->makeMember();

        $this->post('/forget-password/email', [
            'nip' => $member->nip,
            'email' => $member->email,
        ])->assertSessionHas('success');

        $member->refresh();
        $this->assertNotNull($member->{'code-password'});
        $this->assertNotNull($member->code_password_expires_at);
        $this->assertTrue($member->code_password_expires_at->isFuture());
        Mail::assertSent(SendEmailForgetPassword::class);
    }

    public function test_reset_page_rejects_an_unknown_code()
    {
        $response = $this->get('/user/forget-password/not-a-real-code');

        $response->assertRedirect(route('user.login'));
        $response->assertSessionHas('error');
    }

    public function test_reset_page_rejects_an_expired_code()
    {
        $member = $this->makeMember([
            'code-password' => 'expired-code',
            'code_password_expires_at' => now()->subMinute(),
        ]);

        $response = $this->get('/user/forget-password/expired-code');

        $response->assertRedirect(route('user.login'));
        $response->assertSessionHas('error');
    }

    public function test_submitting_reset_with_an_unknown_code_does_not_crash()
    {
        $response = $this->put('/user/forget-password/not-a-real-code/reset', [
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('user.login'));
        $response->assertSessionHas('error');
    }

    public function test_submitting_reset_with_an_expired_code_is_rejected_without_changing_password()
    {
        $member = $this->makeMember([
            'code-password' => 'expired-code',
            'code_password_expires_at' => now()->subMinute(),
        ]);
        $originalPassword = $member->password;

        $response = $this->put('/user/forget-password/expired-code/reset', [
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('user.login'));
        $response->assertSessionHas('error');
        $this->assertSame($originalPassword, $member->fresh()->password);
    }

    public function test_member_can_reset_password_with_a_valid_code()
    {
        $member = $this->makeMember([
            'code-password' => 'valid-code',
            'code_password_expires_at' => now()->addMinutes(30),
        ]);

        $response = $this->put('/user/forget-password/valid-code/reset', [
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('user.login'));
        $response->assertSessionHas('success');

        $member->refresh();
        $this->assertNull($member->{'code-password'});
        $this->assertNull($member->code_password_expires_at);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('Password123!', $member->password));
    }
}
