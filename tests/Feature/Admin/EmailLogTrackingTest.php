<?php

namespace Tests\Feature\Admin;

use App\Models\EmailLog;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailLogTrackingTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    protected function setUp(): void
    {
        parent::setUp();

        // The "array" transport actually runs the full Mailable::send()
        // pipeline (unlike Mail::fake(), which short-circuits before ever
        // calling send()) without touching the network - exactly what's
        // needed to exercise the EmailLog-marking logic for real.
        config(['mail.default' => 'array']);
    }

    private function makeRegistration(array $overrides): Registration
    {
        return Registration::create(array_merge([
            'contact' => '081234560000',
            'agency' => 'Kementerian Contoh',
            'level' => 'Ahli Pertama',
            'status' => 'confirm',
        ], $overrides));
    }

    public function test_approving_a_registration_logs_the_approval_email_as_sent()
    {
        $admin = $this->makeAdminUser('administrator');
        $registration = $this->makeRegistration([
            'nip' => '199001012020129001',
            'name' => 'Anggota Approve',
            'email' => 'approve@example.com',
            'position' => 'Analis SDM Aparatur',
        ]);

        $this->actingAs($admin)->post("/admin/registration/{$registration->id}/approve");

        $this->assertDatabaseHas('email_logs', [
            'mailable' => \App\Mail\SendEmailAprrove::class,
            'type' => 'registration_approved',
            'to_email' => 'approve@example.com',
            'status' => EmailLog::STATUS_SENT,
        ]);
    }

    public function test_rejecting_a_registration_logs_the_rejection_email_as_sent()
    {
        $admin = $this->makeAdminUser('administrator');
        $registration = $this->makeRegistration([
            'nip' => '199001012020129002',
            'name' => 'Anggota Reject',
            'email' => 'reject@example.com',
            'position' => 'Analis SDM Aparatur',
        ]);

        $this->actingAs($admin)->post("/admin/registration/{$registration->id}/reject");

        $this->assertDatabaseHas('email_logs', [
            'mailable' => \App\Mail\SendEmailReject::class,
            'type' => 'registration_rejected',
            'to_email' => 'reject@example.com',
            'status' => EmailLog::STATUS_SENT,
        ]);
    }

    public function test_sending_payment_request_email_logs_it_as_sent()
    {
        $admin = $this->makeAdminUser('administrator');
        $registration = $this->makeRegistration([
            'nip' => '199001012020129003',
            'name' => 'Anggota Bayar',
            'email' => 'bayar@example.com',
            'position' => 'Analis SDM Aparatur',
        ]);

        $this->actingAs($admin)->post("/admin/registration/{$registration->id}/email");

        $this->assertDatabaseHas('email_logs', [
            'mailable' => \App\Mail\SendEmailRegistration::class,
            'type' => 'registration_payment_request',
            'to_email' => 'bayar@example.com',
            'status' => EmailLog::STATUS_SENT,
        ]);
    }

    public function test_confirming_a_registration_logs_the_email_to_the_admin_entered_address()
    {
        $admin = $this->makeAdminUser('administrator');
        $registration = $this->makeRegistration([
            'nip' => '199001012020129004',
            'name' => 'Anggota Confirm',
            'email' => 'confirm-original@example.com',
            'position' => 'Analis SDM Aparatur',
        ]);

        $this->actingAs($admin)->post("/admin/registration/{$registration->id}/confirm", [
            'email' => 'confirm-entered@example.com',
        ]);

        $this->assertDatabaseHas('email_logs', [
            'mailable' => \App\Mail\SendEmailConfirm::class,
            'type' => 'registration_confirm',
            'to_email' => 'confirm-entered@example.com',
            'status' => EmailLog::STATUS_SENT,
        ]);
    }

    public function test_email_logs_index_is_restricted_to_administrator()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');

        $this->actingAs($keanggotaan)->get('/admin/email-logs')->assertForbidden();
    }

    public function test_email_logs_index_shows_summary_counts_and_supports_status_filter()
    {
        $admin = $this->makeAdminUser('administrator');
        EmailLog::start('App\\Mail\\Dummy', 'forgot_password', 'a@example.com')->markSent();
        EmailLog::start('App\\Mail\\Dummy', 'forgot_password', 'b@example.com')->markFailed('SMTP down');
        EmailLog::start('App\\Mail\\Dummy', 'forgot_password', 'c@example.com');

        $response = $this->actingAs($admin)->get('/admin/email-logs');

        $response->assertInertia(fn ($page) => $page
            ->component('Admin/EmailLogs/Index')
            ->where('summary.total', 3)
            ->where('summary.sent', 1)
            ->where('summary.failed', 1)
            ->where('summary.pending', 1)
        );

        $failedOnly = $this->actingAs($admin)->get('/admin/email-logs?status=failed');
        $failedOnly->assertInertia(fn ($page) => $page
            ->where('emailLogs.data', fn ($rows) => count($rows) === 1)
        );
    }
}
