<?php

namespace Tests\Feature\User;

use App\Models\DetailEvent;
use App\Models\Event;
use App\Models\EventPoint;
use App\Models\Member;
use App\Models\PointTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventPointRedemptionTest extends TestCase
{
    use RefreshDatabase;

    private function makeMember(): Member
    {
        return Member::create([
            'nip' => '199001012020121008',
            'name' => 'Anggota Redeem',
            'email' => 'anggotaredeem@example.com',
            'agency' => 'Instansi Test',
            'nomember' => '00008/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);
    }

    private function makeEvent(int $pointCost = 0): Event
    {
        $event = Event::create([
            'title' => 'Workshop Berbayar Poin',
            'slug' => 'workshop-berbayar-poin-' . uniqid(),
            'body' => 'Deskripsi kegiatan',
            'date' => now()->toDateString(),
            'enddate' => now()->addDay()->toDateString(),
            'participant' => 100,
            'place' => 'Jakarta',
            'link' => '-',
            'status' => 'active',
            'file' => 'N',
            'absen' => 'N',
        ]);

        if ($pointCost > 0) {
            EventPoint::create(['event_id' => $event->id, 'point_cost' => $pointCost]);
        }

        return $event;
    }

    public function test_joining_a_free_event_does_not_touch_points()
    {
        $member = $this->makeMember();
        $event = $this->makeEvent(pointCost: 0);

        $response = $this->actingAs($member, 'member')->post("/user/events/{$event->id}/join");

        $response->assertRedirect(route('user.events.index'));
        $this->assertDatabaseHas('detail_events', ['event_id' => $event->id, 'member_id' => $member->id]);
        $this->assertSame(0, PointTransaction::count());
    }

    public function test_member_with_enough_points_can_join_a_paid_event()
    {
        $member = $this->makeMember();
        $event = $this->makeEvent(pointCost: 20);

        PointTransaction::create([
            'member_id' => $member->id,
            'type' => 'reward',
            'amount' => 50,
            'balance_after' => 50,
        ]);

        $response = $this->actingAs($member, 'member')->post("/user/events/{$event->id}/join");

        $response->assertRedirect(route('user.events.index'));
        $this->assertDatabaseHas('detail_events', ['event_id' => $event->id, 'member_id' => $member->id]);
        $this->assertDatabaseHas('point_transactions', [
            'member_id' => $member->id,
            'type' => 'redeem',
            'amount' => 20,
            'balance_after' => 30,
            'event_id' => $event->id,
        ]);
    }

    public function test_member_without_enough_points_cannot_join_a_paid_event()
    {
        $member = $this->makeMember();
        $event = $this->makeEvent(pointCost: 20);

        PointTransaction::create([
            'member_id' => $member->id,
            'type' => 'reward',
            'amount' => 10,
            'balance_after' => 10,
        ]);

        $response = $this->actingAs($member, 'member')->post("/user/events/{$event->id}/join");

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('detail_events', ['event_id' => $event->id, 'member_id' => $member->id]);
        // Balance must be untouched - no partial deduction on a failed join.
        $this->assertSame(10, (int) PointTransaction::where('member_id', $member->id)->latest('id')->value('balance_after'));
    }

    public function test_rejoining_an_already_joined_paid_event_does_not_charge_points_again()
    {
        $member = $this->makeMember();
        $event = $this->makeEvent(pointCost: 20);

        PointTransaction::create([
            'member_id' => $member->id,
            'type' => 'reward',
            'amount' => 50,
            'balance_after' => 50,
        ]);

        DetailEvent::create([
            'event_id' => $event->id,
            'member_id' => $member->id,
            'title' => 'peserta',
            'status' => 'approved',
        ]);

        $this->actingAs($member, 'member')->post("/user/events/{$event->id}/join");

        // Still only the original reward transaction - no redeem happened.
        $this->assertSame(1, PointTransaction::where('member_id', $member->id)->count());
        $this->assertSame(50, (int) PointTransaction::where('member_id', $member->id)->latest('id')->value('balance_after'));
    }
}
