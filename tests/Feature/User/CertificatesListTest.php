<?php

namespace Tests\Feature\User;

use App\Models\Certificate;
use App\Models\Event;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificatesListTest extends TestCase
{
    use RefreshDatabase;

    private function makeMember(): Member
    {
        return Member::create([
            'nip' => '199001012020121007',
            'name' => 'Anggota Sertifikat',
            'email' => 'anggotasertifikat@example.com',
            'agency' => 'Instansi Test',
            'nomember' => '00007/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_certificates_list_loads_without_a_search_term()
    {
        $member = $this->makeMember();

        $this->actingAs($member, 'member')->get('/user/certificates')->assertOk();
    }

    public function test_searching_certificates_does_not_crash()
    {
        $member = $this->makeMember();
        $event = Event::create([
            'title' => 'Workshop Sertifikasi',
            'slug' => 'workshop-sertifikasi',
            'body' => 'desc',
            'date' => now()->toDateString(),
            'enddate' => now()->toDateString(),
            'participant' => 10,
            'place' => 'Jakarta',
            'link' => '-',
            'status' => 'active',
        ]);
        Certificate::create([
            'event_id' => $event->id,
            'no_certificate' => 'CERT-001',
            'nip' => $member->nip,
            'name' => $member->name,
            'body' => 'Sertifikat',
            'date' => now()->toDateString(),
            'template' => '1',
            'category' => 'Workshop',
            'qr_code' => 'https://example.com/verify/1',
            'link' => '-',
            'doc' => '-',
        ]);

        $response = $this->actingAs($member, 'member')->get('/user/certificates?q=Workshop');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('User/Certificates/Index')
            ->has('datas.data', 1)
        );
    }

    private function makeEventAndCertificate(string $nip, string $name): Certificate
    {
        $event = Event::create([
            'title' => 'Workshop ' . $name,
            'slug' => 'workshop-' . \Illuminate\Support\Str::slug($name),
            'body' => 'desc',
            'date' => now()->toDateString(),
            'enddate' => now()->toDateString(),
            'participant' => 10,
            'place' => 'Jakarta',
            'link' => '-',
            'status' => 'active',
        ]);

        return Certificate::create([
            'event_id' => $event->id,
            'no_certificate' => 'CERT-' . $nip,
            'nip' => $nip,
            'name' => $name,
            'body' => 'Sertifikat',
            'date' => now()->toDateString(),
            'template' => '1',
            'category' => 'Workshop',
            'qr_code' => 'https://example.com/verify/' . $nip,
            'link' => '-',
            'doc' => '',
        ]);
    }

    // Note: a "member can view their own certificate" happy-path test would
    // also belong here, but certificateView() unconditionally calls
    // QrCode::generate() before rendering, which requires the imagick PHP
    // extension - not installed in this sandbox - so it can't be exercised
    // end-to-end here. The security-relevant case (ownership is enforced)
    // is verified below, since that 404s before QR generation is reached.

    public function test_member_cannot_view_another_members_certificate()
    {
        $member = $this->makeMember();
        $otherCertificate = $this->makeEventAndCertificate('199001012020121999', 'Anggota Lain');

        $this->actingAs($member, 'member')->get("/user/certificates/{$otherCertificate->id}")->assertNotFound();
    }
}
