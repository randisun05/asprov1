<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventsRoleAccessTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_guest_is_redirected_to_login()
    {
        $this->get('/admin/events')->assertRedirect('/login');
    }

    public function test_role_without_events_access_is_forbidden()
    {
        $pendanaan = $this->makeAdminUser('pendanaan');

        $this->actingAs($pendanaan)->get('/admin/events')->assertForbidden();
    }

    public function test_humas_can_view_events_index()
    {
        $humas = $this->makeAdminUser('humas');

        $this->actingAs($humas)->get('/admin/events')->assertOk();
    }

    public function test_administrator_can_view_events_index()
    {
        $admin = $this->makeAdminUser('administrator');

        $this->actingAs($admin)->get('/admin/events')->assertOk();
    }
}
