<?php

namespace Tests\Feature\Admin;

use App\Models\Event;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaManagementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function makeEvent(string $title): Event
    {
        return Event::create([
            'title' => $title,
            'slug' => \Illuminate\Support\Str::slug($title),
            'body' => 'Deskripsi kegiatan',
            'date' => '2026-01-01',
            'enddate' => '2026-01-02',
            'participant' => 10,
            'place' => 'Jakarta',
            'link' => 'https://example.com',
            'status' => 'active',
        ]);
    }

    public function test_edit_returns_the_requested_media_not_the_first_row_in_the_table()
    {
        $admin = $this->makeAdminUser('administrator');
        $eventA = $this->makeEvent('Kegiatan A');
        $eventB = $this->makeEvent('Kegiatan B');

        $first = Media::create(['title' => 'Foto Pertama', 'media' => 'first.jpg', 'event_id' => $eventA->id]);
        $second = Media::create(['title' => 'Foto Kedua', 'media' => 'second.jpg', 'event_id' => $eventB->id]);

        $response = $this->actingAs($admin)->get("/admin/medias/{$second->id}/edit");

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Medias/Edit')
            ->where('media.id', $second->id)
            ->where('media.title', 'Foto Kedua')
        );
    }

    public function test_store_rejects_a_non_image_file()
    {
        Storage::fake('public');
        $admin = $this->makeAdminUser('administrator');
        $event = $this->makeEvent('Kegiatan');

        $response = $this->actingAs($admin)->post('/admin/medias', [
            'title' => 'Foto Kegiatan',
            'event_id' => $event->id,
            'media' => UploadedFile::fake()->create('malicious.php', 10),
        ]);

        $response->assertSessionHasErrors('media');
        $this->assertDatabaseCount('media', 0);
    }

    public function test_store_creates_a_media_entry_with_a_success_flash()
    {
        Storage::fake('public');
        $admin = $this->makeAdminUser('administrator');
        $event = $this->makeEvent('Kegiatan');

        $response = $this->actingAs($admin)->post('/admin/medias', [
            'title' => 'Foto Kegiatan',
            'event_id' => $event->id,
            'media' => UploadedFile::fake()->image('foto.jpg'),
        ]);

        $response->assertRedirect(route('admin.medias.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('media', ['title' => 'Foto Kegiatan', 'event_id' => $event->id]);
    }

    public function test_update_without_a_new_file_keeps_the_existing_media_instead_of_crashing()
    {
        $admin = $this->makeAdminUser('administrator');
        $event = $this->makeEvent('Kegiatan');
        $media = Media::create(['title' => 'Judul Lama', 'media' => 'existing.jpg', 'event_id' => $event->id]);

        $response = $this->actingAs($admin)->post("/admin/medias/{$media->id}", [
            'title' => 'Judul Baru',
            'event_id' => $event->id,
        ]);

        $response->assertRedirect(route('admin.medias.index'));
        $response->assertSessionHas('success');
        $media->refresh();
        $this->assertSame('Judul Baru', $media->title);
        $this->assertSame('existing.jpg', $media->media);
    }

    public function test_destroy_removes_the_media_with_a_success_flash()
    {
        $admin = $this->makeAdminUser('administrator');
        $event = $this->makeEvent('Kegiatan');
        $media = Media::create(['title' => 'Foto', 'media' => 'foto.jpg', 'event_id' => $event->id]);

        $response = $this->actingAs($admin)->delete("/admin/medias/{$media->id}");

        $response->assertRedirect(route('admin.medias.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }
}
