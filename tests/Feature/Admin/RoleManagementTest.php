<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_creating_staff_with_an_unknown_role_is_rejected()
    {
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/setting', [
            'nip' => '199999999999999999',
            'name' => 'Staff Baru',
            'email' => 'staffbaru@example.com',
            'role' => 'not-a-real-role',
            'password' => 'password',
            'position' => 'anggota',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'staffbaru@example.com']);
    }

    public function test_creating_staff_with_a_valid_role_succeeds()
    {
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/setting', [
            'nip' => '199999999999999999',
            'name' => 'Staff Baru',
            'email' => 'staffbaru@example.com',
            'role' => 'humas',
            'password' => 'password',
            'position' => 'anggota',
        ]);

        $response->assertRedirect(route('admin.setting.index'));
        $this->assertDatabaseHas('users', ['email' => 'staffbaru@example.com', 'role' => 'humas']);
    }

    public function test_non_administrator_cannot_manage_staff_accounts()
    {
        $humas = $this->makeAdminUser('humas');

        $response = $this->actingAs($humas)->get('/admin/setting');

        $response->assertForbidden();
    }

    public function test_administrator_can_manage_staff_accounts()
    {
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->get('/admin/setting');

        $response->assertOk();
    }
}
