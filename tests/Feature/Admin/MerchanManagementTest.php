<?php

namespace Tests\Feature\Admin;

use App\Models\Merchan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MerchanManagementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_store_rejects_a_non_image_file()
    {
        Storage::fake('public');
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/merchans', [
            'title' => 'Kaos Aspro',
            'body' => 'Deskripsi',
            'how' => 'Cara beli',
            'price' => '100000',
            'image' => UploadedFile::fake()->create('malicious.php', 10),
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('merchans', 0);
    }

    public function test_store_creates_a_merchan_with_a_success_flash()
    {
        Storage::fake('public');
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/merchans', [
            'title' => 'Kaos Aspro',
            'body' => 'Deskripsi',
            'how' => 'Cara beli',
            'price' => '100000',
            'image' => UploadedFile::fake()->image('kaos.jpg'),
        ]);

        $response->assertRedirect(route('admin.merchans.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('merchans', ['title' => 'Kaos Aspro', 'status' => 'active']);
    }

    public function test_update_rejects_a_non_image_file()
    {
        Storage::fake('public');
        $admin = $this->makeAdminUser('administrator');
        $merchan = Merchan::create([
            'title' => 'Kaos Lama', 'image' => 'kaos.jpg', 'body' => 'Deskripsi',
            'how' => 'Cara beli', 'price' => '100000', 'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post("/admin/merchans/{$merchan->id}", [
            'title' => 'Kaos Baru',
            'body' => 'Deskripsi',
            'how' => 'Cara beli',
            'price' => '150000',
            'image' => UploadedFile::fake()->create('malicious.php', 10),
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertSame('Kaos Lama', $merchan->fresh()->title);
    }

    public function test_update_changes_fields_with_a_success_flash()
    {
        $admin = $this->makeAdminUser('administrator');
        $merchan = Merchan::create([
            'title' => 'Kaos Lama', 'image' => 'kaos.jpg', 'body' => 'Deskripsi',
            'how' => 'Cara beli', 'price' => '100000', 'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post("/admin/merchans/{$merchan->id}", [
            'title' => 'Kaos Baru',
            'body' => 'Deskripsi',
            'how' => 'Cara beli',
            'price' => '150000',
        ]);

        $response->assertRedirect(route('admin.merchans.index'));
        $response->assertSessionHas('success');
        $this->assertSame('Kaos Baru', $merchan->fresh()->title);
        // image untouched when no new file uploaded
        $this->assertSame('kaos.jpg', $merchan->fresh()->image);
    }

    public function test_destroy_removes_the_merchan_with_a_success_flash()
    {
        $admin = $this->makeAdminUser('administrator');
        $merchan = Merchan::create([
            'title' => 'Kaos', 'image' => 'kaos.jpg', 'body' => 'Deskripsi',
            'how' => 'Cara beli', 'price' => '100000', 'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->delete("/admin/merchans/{$merchan->id}");

        $response->assertRedirect(route('admin.merchans.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('merchans', ['id' => $merchan->id]);
    }

    public function test_change_toggles_status_with_a_success_flash()
    {
        $admin = $this->makeAdminUser('administrator');
        $merchan = Merchan::create([
            'title' => 'Kaos', 'image' => 'kaos.jpg', 'body' => 'Deskripsi',
            'how' => 'Cara beli', 'price' => '100000', 'status' => 'active',
        ]);

        $response = $this->actingAs($admin)->post("/admin/merchans/{$merchan->id}/change");

        $response->assertRedirect(route('admin.merchans.index'));
        $response->assertSessionHas('success');
        $this->assertSame('non-active', $merchan->fresh()->status);
    }
}
