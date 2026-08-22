<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventDetailRoleUpdateTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_updating_role_for_a_nonexistent_detail_returns_404_instead_of_crashing()
    {
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/events/999999/updaterole', [
            'title' => 'Panitia',
        ]);

        $response->assertNotFound();
    }
}
