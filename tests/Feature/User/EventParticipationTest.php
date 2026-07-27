<?php

namespace Tests\Feature\User;

use App\Models\DetailEvent;
use App\Models\Event;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventParticipationTest extends TestCase
{
    use RefreshDatabase;

    private function makeMember(): Member
    {
        return Member::create([
            'nip' => '199001012020121006',
            'name' => 'Anggota Event',
            'email' => 'anggotaevent@example.com',
            'agency' => 'Instansi Test',
            'nomember' => '00006/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);
    }

    private function makeEvent(array $overrides = []): Event
    {
        return Event::create(array_merge([
            'title' => 'Workshop Test',
            'slug' => 'workshop-test',
            'body' => 'Deskripsi kegiatan',
            'date' => now()->toDateString(),
            'enddate' => now()->addDay()->toDateString(),
            'participant' => 100,
            'place' => 'Jakarta',
            'link' => '-',
            'status' => 'active',
            'file' => 'N',
            'absen' => 'N',
        ], $overrides));
    }

    public function test_member_can_join_an_active_event()
    {
        $member = $this->makeMember();
        $event = $this->makeEvent(['status' => 'active']);

        $response = $this->actingAs($member, 'member')->post("/user/events/{$event->id}/join");

        $response->assertRedirect(route('user.events.index'));
        $this->assertDatabaseHas('detail_events', [
            'event_id' => $event->id,
            'member_id' => $member->id,
        ]);
    }

    public function test_member_cannot_join_a_closed_event()
    {
        $member = $this->makeMember();
        $event = $this->makeEvent(['status' => 'closed']);

        $response = $this->actingAs($member, 'member')->post("/user/events/{$event->id}/join");

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('detail_events', [
            'event_id' => $event->id,
            'member_id' => $member->id,
        ]);
    }

    public function test_member_can_self_check_in_when_absen_is_open()
    {
        $member = $this->makeMember();
        $event = $this->makeEvent(['absen' => 'Y']);
        DetailEvent::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'status' => 'approved',
        ]);

        $this->actingAs($member, 'member')->post("/user/events/{$event->id}/absen");

        $this->assertSame('hadir', DetailEvent::where('event_id', $event->id)->where('member_id', $member->id)->value('status'));
    }

    public function test_member_cannot_self_check_in_when_absen_is_closed()
    {
        $member = $this->makeMember();
        $event = $this->makeEvent(['absen' => 'N']);
        DetailEvent::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'status' => 'approved',
        ]);

        $response = $this->actingAs($member, 'member')->post("/user/events/{$event->id}/absen");

        $response->assertSessionHas('error');
        $this->assertSame('approved', DetailEvent::where('event_id', $event->id)->where('member_id', $member->id)->value('status'));
    }
}
