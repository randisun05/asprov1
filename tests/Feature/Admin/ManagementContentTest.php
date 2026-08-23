<?php

namespace Tests\Feature\Admin;

use App\Models\Management;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ManagementContentTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_index_loads_real_data_instead_of_an_empty_array()
    {
        $admin = $this->makeAdminUser('administrator');
        Management::create(['item' => 'popup', 'body' => 'Isi popup']);

        $response = $this->actingAs($admin)->get('/admin/management');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Management/Index')
            ->has('datas.data', 1)
        );
    }

    public function test_store_requires_an_item_category()
    {
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/management/store', [
            'sub' => 'Sub tanpa item',
        ]);

        $response->assertSessionHasErrors('item');
        $this->assertDatabaseCount('management', 0);
    }

    public function test_store_creates_a_management_entry()
    {
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/management/store', [
            'item' => 'popup',
            'sub' => 'Judul Popup',
            'body' => 'Isi popup',
        ]);

        $response->assertRedirect(route('admin.management.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('management', ['item' => 'popup', 'sub' => 'Judul Popup']);
    }

    public function test_update_changes_fields_and_keeps_body_when_not_sent()
    {
        $admin = $this->makeAdminUser('administrator');
        $data = Management::create(['item' => 'popup', 'sub' => 'Lama', 'body' => 'Isi Asli']);

        $response = $this->actingAs($admin)->post('/admin/management/update', [
            'id' => $data->id,
            'item' => 'popup',
            'sub' => 'Baru',
        ]);

        $response->assertRedirect(route('admin.management.index'));
        $response->assertSessionHas('success');
        $data->refresh();
        $this->assertSame('Baru', $data->sub);
        // body wasn't sent in the payload - it must be preserved, not wiped.
        $this->assertSame('Isi Asli', $data->body);
    }

    public function test_status_toggles_and_redirects_with_a_flash_message()
    {
        $admin = $this->makeAdminUser('administrator');
        $data = Management::create(['item' => 'popup', 'status' => '1']);

        $response = $this->actingAs($admin)->post('/admin/management/update/status', [
            'id' => $data->id,
        ]);

        $response->assertRedirect(route('admin.management.index'));
        $response->assertSessionHas('success');
        $this->assertSame('0', $data->fresh()->status);
    }

    public function test_destroy_removes_the_entry_with_a_flash_message()
    {
        $admin = $this->makeAdminUser('administrator');
        $data = Management::create(['item' => 'popup']);

        $response = $this->actingAs($admin)->delete("/admin/management/{$data->id}");

        $response->assertRedirect(route('admin.management.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('management', ['id' => $data->id]);
    }
}
