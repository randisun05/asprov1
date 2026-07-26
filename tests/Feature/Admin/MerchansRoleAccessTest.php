<?php

namespace Tests\Feature\Admin;

use App\Models\Merchan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MerchansRoleAccessTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_guest_is_redirected_to_login()
    {
        $this->get('/admin/merchans')->assertRedirect('/login');
    }

    public function test_role_without_merchans_access_is_forbidden()
    {
        $humas = $this->makeAdminUser('humas');

        $this->actingAs($humas)->get('/admin/merchans')->assertForbidden();
    }

    public function test_pendanaan_can_view_merchans_index()
    {
        $pendanaan = $this->makeAdminUser('pendanaan');

        $this->actingAs($pendanaan)->get('/admin/merchans')->assertOk();
    }

    public function test_pendanaan_can_toggle_merchan_status()
    {
        $pendanaan = $this->makeAdminUser('pendanaan');
        $merchan = Merchan::create([
            'title' => 'Kaos Aspro',
            'image' => 'kaos.jpg',
            'body' => 'Deskripsi',
            'how' => 'Cara beli',
            'price' => 100000,
            'status' => 'active',
        ]);

        $this->actingAs($pendanaan)->post("/admin/merchans/{$merchan->id}/change");

        $this->assertSame('non-active', $merchan->fresh()->status);
    }
}
