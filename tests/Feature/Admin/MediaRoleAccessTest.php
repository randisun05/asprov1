<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaRoleAccessTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_guest_is_redirected_to_login()
    {
        $this->get('/admin/medias')->assertRedirect('/login');
    }

    public function test_role_without_media_access_is_forbidden()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');

        $this->actingAs($keanggotaan)->get('/admin/medias')->assertForbidden();
    }

    public function test_humas_can_view_media_index()
    {
        $humas = $this->makeAdminUser('humas');

        $this->actingAs($humas)->get('/admin/medias')->assertOk();
    }

    public function test_administrator_can_view_media_index()
    {
        $admin = $this->makeAdminUser('administrator');

        $this->actingAs($admin)->get('/admin/medias')->assertOk();
    }
}
