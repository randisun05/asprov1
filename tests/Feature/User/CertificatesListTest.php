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
}
