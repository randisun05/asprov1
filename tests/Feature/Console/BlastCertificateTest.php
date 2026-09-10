<?php

namespace Tests\Feature\Console;

use App\Mail\SertifikatEmail;
use App\Models\Certificate;
use App\Models\EmailLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BlastCertificateTest extends TestCase
{
    use RefreshDatabase;

    private function makeEventEight(): void
    {
        // blast:certificate hardcodes event_id = 8, so the test event must
        // literally have that id - inserted directly since "id" isn't
        // mass-assignable on the Event model.
        DB::table('events')->insert([
            'id' => 8,
            'title' => 'Event Sertifikat Test',
            'body' => 'Deskripsi',
            'date' => now()->toDateString(),
            'enddate' => now()->toDateString(),
            'participant' => 100,
            'place' => 'Jakarta',
            'link' => 'https://zoom.example/test',
            'status' => 'closed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function makeCertificate(array $overrides = []): Certificate
    {
        return Certificate::create(array_merge([
            'event_id' => 8,
            'no_certificate' => 'CERT-001',
            'nip' => '199001012020121099',
            'name' => 'Peserta Test',
            'body' => 'Event Sertifikat Test',
            'date' => now()->toDateString(),
            'template' => 'dummy-template',
            'category' => 'Kombel',
            'qr_code' => 'qr-code-value',
            'link' => 'https://example.com/certificate/1',
            'doc' => 'certificate.pdf',
            'email' => 'peserta@example.com',
            'is_emailed' => 0,
        ], $overrides));
    }

    public function test_blast_queues_a_logged_email_for_each_unsent_certificate()
    {
        Mail::fake();
        $this->makeEventEight();
        $certificate = $this->makeCertificate();

        $this->artisan('blast:certificate', ['event_id' => 8])->assertExitCode(0);

        $this->assertEquals(1, $certificate->fresh()->is_emailed);
        Mail::assertQueued(SertifikatEmail::class);
        $this->assertDatabaseHas('email_logs', [
            'mailable' => SertifikatEmail::class,
            'type' => 'certificate',
            'to_email' => 'peserta@example.com',
            'status' => EmailLog::STATUS_PENDING,
        ]);
    }

    public function test_blast_skips_certificates_already_marked_as_emailed()
    {
        Mail::fake();
        $this->makeEventEight();
        $this->makeCertificate(['is_emailed' => 1]);

        $this->artisan('blast:certificate', ['event_id' => 8])->assertExitCode(0);

        Mail::assertNothingQueued();
        $this->assertDatabaseCount('email_logs', 0);
    }

    public function test_sending_the_certificate_email_logs_it_as_sent()
    {
        config(['mail.default' => 'array']);
        $this->makeEventEight();
        $certificate = $this->makeCertificate();

        $this->artisan('blast:certificate', ['event_id' => 8]);

        $this->assertDatabaseHas('email_logs', [
            'mailable' => SertifikatEmail::class,
            'to_email' => $certificate->email,
            'status' => EmailLog::STATUS_SENT,
        ]);
    }
}
