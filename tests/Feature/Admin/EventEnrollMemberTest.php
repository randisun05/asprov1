<?php

namespace Tests\Feature\Admin;

use App\Models\DetailEvent;
use App\Models\Event;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventEnrollMemberTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function makeMember(): Member
    {
        return Member::create([
            'nip' => '199001012020121007',
            'name' => 'Anggota Enroll',
            'email' => 'anggotaenroll@example.com',
            'agency' => 'Instansi Test',
            'nomember' => '00007/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_enrolling_the_same_member_twice_does_not_create_duplicate_rows()
    {
        $admin = $this->makeAdminUser('administrator');
        $event = Event::factory()->create();
        $member = $this->makeMember();

        $payload = ['member_id' => $member->id, 'title' => 'peserta'];

        $first = $this->actingAs($admin)->post("/admin/events/{$event->id}/enroll", $payload);
        $first->assertRedirect();
        $first->assertSessionHas('success');

        $second = $this->actingAs($admin)->post("/admin/events/{$event->id}/enroll", $payload);
        $second->assertRedirect();
        $second->assertSessionHas('error');

        $this->assertSame(1, DetailEvent::where('event_id', $event->id)->where('member_id', $member->id)->count());
    }
}
