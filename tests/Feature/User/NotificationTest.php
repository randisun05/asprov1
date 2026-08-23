<?php

namespace Tests\Feature\User;

use App\Models\Member;
use App\Models\MemberNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    private function makeMember(array $overrides = []): Member
    {
        static $counter = 0;
        $counter++;

        return Member::create(array_merge([
            'nip' => '19900101202012' . str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'name' => 'Anggota Notif ' . $counter,
            'email' => "notif{$counter}@example.com",
            'agency' => 'Instansi Test',
            'nomember' => '0000' . $counter . '/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ], $overrides));
    }

    public function test_guest_is_redirected_to_member_login()
    {
        $this->get('/user/notifications')->assertRedirect('/user/login');
    }

    public function test_member_sees_notifications_with_correct_read_state()
    {
        $member = $this->makeMember();
        $read = MemberNotification::broadcast('event', 'Kegiatan A', 'body', '/user/events/a');
        $unread = MemberNotification::broadcast('post', 'Berita B', 'body', '/user/posts/b');

        $member->notificationReads()->create(['member_notification_id' => $read->id, 'read_at' => now()]);

        $response = $this->actingAs($member, 'member')->get('/user/notifications');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('User/Notifications/Index')
            ->where('notifications.data.0.id', $unread->id)
            ->where('notifications.data.0.is_read', false)
            ->where('notifications.data.1.id', $read->id)
            ->where('notifications.data.1.is_read', true)
        );
    }

    public function test_reading_one_notification_marks_it_read_and_redirects_to_its_link()
    {
        $member = $this->makeMember();
        $notification = MemberNotification::broadcast('merchan', 'Kaos Baru', 'body', '/user/merchans/1');

        $response = $this->actingAs($member, 'member')->post("/user/notifications/{$notification->id}/read");

        $response->assertRedirect('/user/merchans/1');
        $this->assertDatabaseHas('member_notification_reads', [
            'member_notification_id' => $notification->id,
            'member_id' => $member->id,
        ]);
    }

    public function test_reading_the_same_notification_twice_does_not_error()
    {
        $member = $this->makeMember();
        $notification = MemberNotification::broadcast('merchan', 'Kaos Baru', 'body', '/user/merchans/1');

        $this->actingAs($member, 'member')->post("/user/notifications/{$notification->id}/read")->assertRedirect();
        $this->actingAs($member, 'member')->post("/user/notifications/{$notification->id}/read")->assertRedirect();

        $this->assertDatabaseCount('member_notification_reads', 1);
    }

    public function test_read_all_marks_every_unread_notification_read_for_that_member_only()
    {
        $member = $this->makeMember();
        $otherMember = $this->makeMember();
        MemberNotification::broadcast('event', 'A', 'body', '/user/events/a');
        MemberNotification::broadcast('post', 'B', 'body', '/user/posts/b');

        $this->actingAs($member, 'member')->post('/user/notifications/read-all')->assertRedirect();

        $this->assertSame(2, $member->notificationReads()->count());
        $this->assertSame(0, $otherMember->notificationReads()->count());
    }
}
