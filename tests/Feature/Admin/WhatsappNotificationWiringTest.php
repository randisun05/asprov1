<?php

namespace Tests\Feature\Admin;

use App\Jobs\SendWhatsappMessage;
use App\Models\Certificate;
use App\Models\Member;
use App\Models\ProfileDataMain;
use App\Models\Registration;
use App\Models\WhatsappSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class WhatsappNotificationWiringTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    protected function setUp(): void
    {
        parent::setUp();

        Bus::fake();
        WhatsappSetting::current()->update(['enabled' => true, 'api_token' => 'token-123']);
        Mail::fake();
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

    public function test_approving_a_registration_queues_a_whatsapp_message_to_its_contact_number()
    {
        $admin = $this->makeAdminUser('administrator');
        $registration = $this->makeRegistration([
            'nip' => '199001012020129101',
            'name' => 'Anggota WA Approve',
            'email' => 'wa-approve@example.com',
            'contact' => '081111111101',
            'position' => 'Analis SDM Aparatur',
        ]);

        $this->actingAs($admin)->post("/admin/registration/{$registration->id}/approve");

        Bus::assertDispatched(SendWhatsappMessage::class, function ($job) {
            return $job->phone === '081111111101';
        });
    }

    public function test_rejecting_a_registration_queues_a_whatsapp_message_to_its_contact_number()
    {
        $admin = $this->makeAdminUser('administrator');
        $registration = $this->makeRegistration([
            'nip' => '199001012020129102',
            'name' => 'Anggota WA Reject',
            'email' => 'wa-reject@example.com',
            'contact' => '081111111102',
            'position' => 'Analis SDM Aparatur',
        ]);

        $this->actingAs($admin)->post("/admin/registration/{$registration->id}/reject");

        Bus::assertDispatched(SendWhatsappMessage::class, function ($job) {
            return $job->phone === '081111111102';
        });
    }

    public function test_sending_payment_request_queues_a_whatsapp_message_to_its_contact_number()
    {
        $admin = $this->makeAdminUser('administrator');
        $registration = $this->makeRegistration([
            'nip' => '199001012020129103',
            'name' => 'Anggota WA Bayar',
            'email' => 'wa-bayar@example.com',
            'contact' => '081111111103',
            'position' => 'Analis SDM Aparatur',
        ]);

        $this->actingAs($admin)->post("/admin/registration/{$registration->id}/email");

        Bus::assertDispatched(SendWhatsappMessage::class, function ($job) {
            return $job->phone === '081111111103';
        });
    }

    public function test_confirming_a_registration_queues_a_whatsapp_message_to_the_registrants_own_contact_not_the_admin_entered_email_address()
    {
        $admin = $this->makeAdminUser('administrator');
        $registration = $this->makeRegistration([
            'nip' => '199001012020129104',
            'name' => 'Anggota WA Confirm',
            'email' => 'wa-confirm@example.com',
            'contact' => '081111111104',
            'position' => 'Analis SDM Aparatur',
        ]);

        $this->actingAs($admin)->post("/admin/registration/{$registration->id}/confirm", [
            'email' => 'admin-entered@example.com',
        ]);

        Bus::assertDispatched(SendWhatsappMessage::class, function ($job) {
            return $job->phone === '081111111104';
        });
    }

    public function test_no_whatsapp_message_is_queued_when_the_feature_is_disabled()
    {
        WhatsappSetting::current()->update(['enabled' => false]);
        $admin = $this->makeAdminUser('administrator');
        $registration = $this->makeRegistration([
            'nip' => '199001012020129105',
            'name' => 'Anggota WA Off',
            'email' => 'wa-off@example.com',
            'contact' => '081111111105',
            'position' => 'Analis SDM Aparatur',
        ]);

        $this->actingAs($admin)->post("/admin/registration/{$registration->id}/approve");

        Bus::assertNotDispatched(SendWhatsappMessage::class);
    }

    public function test_forgot_password_queues_a_whatsapp_message_using_the_members_profile_contact_number()
    {
        $member = Member::create([
            'nip' => '199001012020129106',
            'name' => 'Anggota WA Lupa',
            'email' => 'wa-lupa@example.com',
            'agency' => 'Instansi Test',
            'nomember' => '00099/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);
        ProfileDataMain::create([
            'nip' => $member->nip,
            'nomember' => $member->nomember,
            'name' => $member->name,
            'email' => $member->email,
            'contact' => '081111111106',
        ]);

        $this->post('/forget-password/email', [
            'nip' => $member->nip,
            'email' => $member->email,
        ]);

        Bus::assertDispatched(SendWhatsappMessage::class, function ($job) {
            return $job->phone === '081111111106';
        });
    }

    public function test_forgot_password_does_not_queue_a_whatsapp_message_when_the_member_has_no_profile_contact_on_file()
    {
        $member = Member::create([
            'nip' => '199001012020129107',
            'name' => 'Anggota WA Tanpa Profil',
            'email' => 'wa-noprofile@example.com',
            'agency' => 'Instansi Test',
            'nomember' => '00098/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);

        $this->post('/forget-password/email', [
            'nip' => $member->nip,
            'email' => $member->email,
        ]);

        Bus::assertNotDispatched(SendWhatsappMessage::class);
    }

    public function test_blast_certificate_queues_a_whatsapp_message_using_the_profile_contact_number_looked_up_by_nip()
    {
        DB::table('events')->insert([
            'id' => 8,
            'title' => 'Event WA Sertifikat',
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
        ProfileDataMain::create([
            'nip' => '199001012020129108',
            'nomember' => '00097/01/ASPROSDMA',
            'name' => 'Peserta WA Sertifikat',
            'email' => 'wa-cert@example.com',
            'contact' => '081111111108',
        ]);
        Certificate::create([
            'event_id' => 8,
            'no_certificate' => 'CERT-WA-001',
            'nip' => '199001012020129108',
            'name' => 'Peserta WA Sertifikat',
            'body' => 'Event WA Sertifikat',
            'date' => now()->toDateString(),
            'template' => 'dummy-template',
            'category' => 'Kombel',
            'qr_code' => 'qr-code-value',
            'link' => 'https://example.com/certificate/wa-1',
            'doc' => 'certificate.pdf',
            'email' => 'wa-cert@example.com',
            'is_emailed' => 0,
        ]);

        $this->artisan('blast:certificate');

        Bus::assertDispatched(SendWhatsappMessage::class, function ($job) {
            return $job->phone === '081111111108';
        });
    }
}
