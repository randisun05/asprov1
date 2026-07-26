<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataMembersRoleAccessTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_guest_is_redirected_to_login()
    {
        $this->get('/admin/members')->assertRedirect('/login');
    }

    public function test_unrelated_role_is_redirected_with_an_error()
    {
        $humas = $this->makeAdminUser('humas');

        $response = $this->actingAs($humas)->get('/admin/members');

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_keanggotaan_can_view_members_index()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');

        $this->actingAs($keanggotaan)->get('/admin/members')->assertOk();
    }

    public function test_pendanaan_can_view_members_index()
    {
        $pendanaan = $this->makeAdminUser('pendanaan');

        $this->actingAs($pendanaan)->get('/admin/members')->assertOk();
    }
}
