<?php

namespace Tests\Feature\Public;

use App\Models\Certificate;
use App\Models\Event;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PublicEventAbsenAndCertificateTest extends TestCase
{
    use RefreshDatabase;

    private function makeEvent(array $overrides = []): Event
    {
        return Event::create(array_merge([
            'title' => 'Workshop SDMA',
            'slug' => 'workshop-sdma',
            'body' => 'Deskripsi kegiatan',
            'date' => now()->toDateString(),
            'enddate' => now()->toDateString(),
            'place' => 'Jakarta',
            'link' => 'https://zoom.example/workshop-sdma',
            'participant' => 100,
            'status' => 'active',
            'absen' => 'Y',
            'category' => 'Kombel',
            'template_id' => (string) Str::uuid(),
        ], $overrides));
    }

    public function test_absen_is_rejected_when_event_attendance_is_not_open()
    {
        $event = $this->makeEvent(['absen' => 'N']);

        $response = $this->post("/events/{$event->id}/absen", [
            'nip' => '199001012020121005',
            'name' => 'Anggota Uji',
            'agency' => 'Instansi Uji',
        ]);

        $response->assertSessionHasErrors('message');
        $this->assertDatabaseCount('certificates', 0);
    }

    public function test_absen_succeeds_and_issues_a_certificate_when_attendance_is_open()
    {
        $event = $this->makeEvent(['absen' => 'Y']);

        $response = $this->post("/events/{$event->id}/absen", [
            'nip' => '199001012020121005',
            'name' => 'Anggota Uji',
            'agency' => 'Instansi Uji',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('certificates', [
            'event_id' => $event->id,
            'nip' => '199001012020121005',
            'template' => $event->template_id,
        ]);

        // "doc" (the generated PDF path) is filled in on demand later, the
        // first time the certificate is actually viewed/downloaded - it
        // used to be mistakenly set to the requester's agency name here
        // instead of being left empty until then.
        $this->assertSame('', \App\Models\Certificate::where('nip', '199001012020121005')->value('doc'));
    }

    public function test_absen_rejects_a_duplicate_submission_for_the_same_nip()
    {
        $event = $this->makeEvent(['absen' => 'Y']);

        $this->post("/events/{$event->id}/absen", [
            'nip' => '199001012020121005',
            'name' => 'Anggota Uji',
            'agency' => 'Instansi Uji',
        ]);

        $response = $this->post("/events/{$event->id}/absen", [
            'nip' => '199001012020121005',
            'name' => 'Anggota Uji',
            'agency' => 'Instansi Uji',
        ]);

        $response->assertSessionHasErrors('message');
        $this->assertDatabaseCount('certificates', 1);
    }

    public function test_download_sertifikat_returns_the_matching_certificate_for_member_and_event()
    {
        $event = $this->makeEvent();

        $member = Member::create([
            'nip' => '199001012020121005',
            'name' => 'Anggota Uji',
            'email' => 'anggotauji@example.com',
            'agency' => 'Instansi Uji',
            'nomember' => '00099/01/ASPROSDMA',
            'password' => bcrypt('password'),
            'qr_link' => (string) Str::uuid(),
        ]);

        $certificate = Certificate::create([
            'event_id' => $event->id,
            'no_certificate' => '0001/Kombel/PP Aspro SDMA/01/2026',
            'category' => 'Kombel',
            'nip' => $member->nip,
            'name' => $member->name,
            'body' => $event->title,
            'date' => now()->toDateString(),
            'template' => (string) Str::uuid(),
            'status' => '1',
            'qr_code' => 'https://asprosdma.id/certificates/' . Str::uuid(),
            'link' => (string) Str::uuid(),
            'doc' => $member->agency,
        ]);

        $response = $this->get("/identity-verification/{$member->qr_link}/{$event->slug}/download");

        $response->assertOk();
        $response->assertViewIs('Reports.Certificates.Certificate');
        $response->assertViewHas('data', function ($data) use ($certificate) {
            return $data->id === $certificate->id;
        });
    }

    public function test_certificates_show_reports_a_friendly_error_when_the_template_is_missing_instead_of_crashing()
    {
        $event = $this->makeEvent();
        $certificate = \App\Models\Certificate::create([
            'event_id' => $event->id,
            'no_certificate' => '0001/Kombel/PP Aspro SDMA/01/2026',
            'category' => 'Kombel',
            'nip' => '199001012020121005',
            'name' => 'Anggota Uji',
            'body' => $event->title,
            'date' => now()->toDateString(),
            'template' => (string) \Illuminate\Support\Str::uuid(), // no TemplateCertificate row exists for this id
            'status' => '1',
            'qr_code' => 'https://asprosdma.id/certificates/' . \Illuminate\Support\Str::uuid(),
            'link' => (string) \Illuminate\Support\Str::uuid(),
            'doc' => '',
        ]);

        $response = $this->get("/certificates/{$certificate->id}/view");

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_download_sertifikat_redirects_back_when_no_certificate_exists_for_the_member_and_event()
    {
        $event = $this->makeEvent();

        $member = Member::create([
            'nip' => '199001012020121005',
            'name' => 'Anggota Uji',
            'email' => 'anggotauji2@example.com',
            'agency' => 'Instansi Uji',
            'nomember' => '00098/01/ASPROSDMA',
            'password' => bcrypt('password'),
            'qr_link' => (string) Str::uuid(),
        ]);

        $response = $this->get("/identity-verification/{$member->qr_link}/{$event->slug}/download");

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
