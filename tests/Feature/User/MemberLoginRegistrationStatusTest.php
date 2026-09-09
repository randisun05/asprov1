<?php

namespace Tests\Feature\User;

use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MemberLoginRegistrationStatusTest extends TestCase
{
    use RefreshDatabase;

    private function fakeValidRecaptcha(): void
    {
        Http::fake([
            'https://www.google.com/recaptcha/api/siteverify' => Http::response(['success' => true, 'score' => 0.9]),
        ]);
    }

    private function attemptLogin(string $nip): \Illuminate\Testing\TestResponse
    {
        return $this->post('/user/login', [
            'nip' => $nip,
            'password' => 'whatever',
            'recaptcha_token' => 'fake-token',
        ]);
    }

    private function makeRegistration(array $overrides): Registration
    {
        return Registration::create(array_merge([
            'nip' => '199001012020129201',
            'name' => 'Pendaftar Test',
            'email' => 'pendaftar@example.com',
            'contact' => '081234560000',
            'agency' => 'Kementerian Contoh',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
        ], $overrides));
    }

    public function test_login_with_a_nip_that_was_never_registered_gets_the_generic_not_registered_message()
    {
        $this->fakeValidRecaptcha();

        $response = $this->attemptLogin('199001012020129999');

        $response->assertSessionHas('error', 'NIP belum terdaftar. Silakan lakukan pendaftaran keanggotaan.');
        $this->assertGuest('member');
    }

    public function test_login_with_a_pending_registration_gets_a_waiting_for_approval_message()
    {
        $this->fakeValidRecaptcha();
        $this->makeRegistration(['status' => 'submission']);

        $response = $this->attemptLogin('199001012020129201');

        $response->assertSessionHas('error', 'Pendaftaran Anda sedang diproses dan belum disetujui oleh admin. Mohon tunggu, Anda akan menerima notifikasi setelah disetujui.');
        $this->assertGuest('member');
    }

    public function test_login_with_a_paid_but_not_yet_approved_registration_gets_a_waiting_for_approval_message()
    {
        $this->fakeValidRecaptcha();
        $this->makeRegistration(['status' => 'paid']);

        $response = $this->attemptLogin('199001012020129201');

        $response->assertSessionHas('error', 'Pendaftaran Anda sedang diproses dan belum disetujui oleh admin. Mohon tunggu, Anda akan menerima notifikasi setelah disetujui.');
    }

    public function test_login_with_a_rejected_registration_gets_a_distinct_rejection_message()
    {
        $this->fakeValidRecaptcha();
        $this->makeRegistration(['status' => 'rejected']);

        $response = $this->attemptLogin('199001012020129201');

        $response->assertSessionHas('error', 'Pendaftaran Anda dengan NIP ini telah ditolak. Silakan hubungi admin untuk informasi lebih lanjut.');
    }

    public function test_login_with_a_corrected_status_registration_gets_a_waiting_for_approval_message()
    {
        $this->fakeValidRecaptcha();
        $this->makeRegistration(['status' => 'corrected']);

        $response = $this->attemptLogin('199001012020129201');

        $response->assertSessionHas('error', 'Pendaftaran Anda sedang diproses dan belum disetujui oleh admin. Mohon tunggu, Anda akan menerima notifikasi setelah disetujui.');
    }
}
