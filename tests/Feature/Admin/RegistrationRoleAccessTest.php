<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationRoleAccessTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_guest_is_redirected_to_login()
    {
        $this->get('/admin/registration/')->assertRedirect('/login');
    }

    public function test_role_without_registration_access_is_forbidden()
    {
        $humas = $this->makeAdminUser('humas');

        $this->actingAs($humas)->get('/admin/registration/')->assertForbidden();
    }

    public function test_keanggotaan_can_view_registration_index()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');

        $this->actingAs($keanggotaan)->get('/admin/registration/')->assertOk();
    }

    public function test_administrator_can_view_registration_index()
    {
        $admin = $this->makeAdminUser('administrator');

        $this->actingAs($admin)->get('/admin/registration/')->assertOk();
    }
}
