<?php

namespace Tests\Feature\Admin;

use App\Models\Certificate;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CertificateNumberSequenceTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function store(Event $event, array $overrides = [])
    {
        $admin = $this->makeAdminUser('administrator');

        return $this->actingAs($admin)->post("/admin/events/{$event->id}/certificates/store", array_merge([
            'category' => 'Seminar',
            'nip' => '199001012020121030',
            'name' => 'Anggota Sertifikat',
            'date' => '2026-08-22',
            'template' => 'template-id',
            'agency' => 'Instansi Test',
        ], $overrides));
    }

    public function test_consecutive_certificates_in_the_same_scope_get_distinct_sequential_numbers()
    {
        $event = Event::factory()->create();

        $this->store($event, ['nip' => '199001012020121031']);
        $this->store($event, ['nip' => '199001012020121032']);

        $numbers = Certificate::where('category', 'Seminar')
            ->whereYear('date', 2026)
            ->pluck('no_certificate')
            ->sort()
            ->values();

        $this->assertSame(
            ['0001/Seminar/PP Aspro SDMA/08/2026', '0002/Seminar/PP Aspro SDMA/08/2026'],
            $numbers->all()
        );
    }

    public function test_numbering_continues_from_existing_certificates_instead_of_restarting_at_one()
    {
        $event = Event::factory()->create();

        Certificate::create([
            'event_id' => $event->id,
            'no_certificate' => '0005/Seminar/PP Aspro SDMA/08/2026',
            'nip' => '199001012020121033',
            'name' => 'Anggota Lama',
            'body' => 'template',
            'date' => '2026-08-10',
            'template' => 'template-id',
            'status' => '1',
            'qr_code' => 'https://example.com/qr',
            'link' => 'link-lama',
            'doc' => 'Instansi',
            'category' => 'Seminar',
        ]);

        $this->store($event, ['nip' => '199001012020121034']);

        $newest = Certificate::where('nip', '199001012020121034')->value('no_certificate');
        $this->assertSame('0006/Seminar/PP Aspro SDMA/08/2026', $newest);
    }

    public function test_different_categories_do_not_share_a_counter()
    {
        $event = Event::factory()->create();

        $this->store($event, ['nip' => '199001012020121035', 'category' => 'Seminar']);
        $this->store($event, ['nip' => '199001012020121036', 'category' => 'Workshop']);

        $this->assertSame(
            '0001/Seminar/PP Aspro SDMA/08/2026',
            Certificate::where('nip', '199001012020121035')->value('no_certificate')
        );
        $this->assertSame(
            '0001/Workshop/PP Aspro SDMA/08/2026',
            Certificate::where('nip', '199001012020121036')->value('no_certificate')
        );
    }
}
