<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Member;
use App\Models\Merchan;
use App\Models\Post;
use App\Models\MemberNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MemberNotificationBroadcastTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function makeMember(): Member
    {
        static $counter = 0;
        $counter++;

        return Member::create([
            'nip' => '19900101202099' . str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'name' => 'Penulis ' . $counter,
            'email' => "penulis{$counter}@example.com",
            'agency' => 'Instansi Test',
            'nomember' => '0009' . $counter . '/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);
    }

    private function eventPayload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Workshop SDMA',
            'body' => 'Deskripsi kegiatan',
            'date' => now()->toDateString(),
            'participant' => 100,
            'enddate' => now()->addDay()->toDateString(),
            'place' => 'Jakarta',
            'link' => 'https://zoom.example/workshop',
            'file' => 'N',
            'category' => 'Kombel',
            'template' => 'dummy-template-id',
            'duration' => 60,
            'start_at' => now()->toDateTimeString(),
            'end_at' => now()->addHour()->toDateTimeString(),
            'image' => UploadedFile::fake()->image('event.jpg'),
        ], $overrides);
    }

    public function test_creating_an_event_broadcasts_a_notification_to_members()
    {
        Storage::fake('public');
        $admin = $this->makeAdminUser('administrator');

        $this->actingAs($admin)->post('/admin/events', $this->eventPayload())->assertRedirect(route('admin.events.index'));

        $this->assertDatabaseHas('member_notifications', [
            'type' => 'event',
            'title' => 'Kegiatan baru: Workshop SDMA',
        ]);
    }

    public function test_creating_a_tryout_category_event_broadcasts_a_tryout_notification()
    {
        Storage::fake('public');
        $admin = $this->makeAdminUser('administrator');

        $this->actingAs($admin)->post('/admin/events', $this->eventPayload([
            'title' => 'Tryout CPNS',
            'category' => 'Tryout',
        ]))->assertRedirect(route('admin.events.index'));

        $this->assertDatabaseHas('member_notifications', [
            'type' => 'tryout',
            'title' => 'Tryout baru: Tryout CPNS',
            'link' => '/user/tryouts',
        ]);
    }

    public function test_approving_a_post_broadcasts_a_notification()
    {
        $admin = $this->makeAdminUser('administrator');
        $category = Category::create(['title' => 'Cerita']);
        $post = Post::create([
            'title' => 'Cerita Anggota',
            'slug' => 'cerita-anggota',
            'body' => 'Isi cerita',
            'excerpt' => 'Ringkasan cerita',
            'category_id' => $category->id,
            'member_id' => $this->makeMember()->id,
            'status' => 'submission',
        ]);

        $this->actingAs($admin)->post("/admin/posts/{$post->id}/approve")->assertRedirect(route('admin.posts.index'));

        $this->assertDatabaseHas('member_notifications', [
            'type' => 'post',
            'title' => 'Berita baru: Cerita Anggota',
            'link' => '/user/posts/cerita-anggota',
        ]);
    }

    public function test_submitting_a_post_does_not_broadcast_a_notification()
    {
        $category = Category::create(['title' => 'Cerita']);

        Post::create([
            'title' => 'Draft Anggota',
            'slug' => 'draft-anggota',
            'body' => 'Isi draft',
            'excerpt' => 'Ringkasan draft',
            'category_id' => $category->id,
            'member_id' => $this->makeMember()->id,
            'status' => 'submission',
        ]);

        $this->assertDatabaseCount('member_notifications', 0);
    }

    public function test_creating_merchandise_broadcasts_a_notification()
    {
        Storage::fake('public');
        $admin = $this->makeAdminUser('administrator');

        $this->actingAs($admin)->post('/admin/merchans', [
            'title' => 'Kaos Aspro',
            'body' => 'Deskripsi',
            'how' => 'Cara beli',
            'price' => '100000',
            'image' => UploadedFile::fake()->image('kaos.jpg'),
        ])->assertRedirect(route('admin.merchans.index'));

        $merchan = Merchan::where('title', 'Kaos Aspro')->firstOrFail();

        $this->assertDatabaseHas('member_notifications', [
            'type' => 'merchan',
            'title' => 'Merchandise baru: Kaos Aspro',
            'link' => "/user/merchans/{$merchan->id}",
        ]);
    }

    public function test_creating_an_announcement_broadcasts_a_notification()
    {
        $admin = $this->makeAdminUser('administrator');

        $this->actingAs($admin)->post('/admin/announcements', [
            'title' => 'Libur Nasional',
            'body' => 'Kantor tutup pada tanggal tersebut.',
        ])->assertRedirect(route('admin.announcements.index'));

        $this->assertDatabaseHas('member_notifications', [
            'type' => 'announcement',
            'title' => 'Pengumuman: Libur Nasional',
        ]);
    }

    public function test_announcement_management_is_restricted_to_administrator_and_humas()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');

        $this->actingAs($keanggotaan)->get('/admin/announcements')->assertForbidden();
    }
}
