<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostsRoleAccessTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_guest_is_redirected_to_login()
    {
        $this->get('/admin/posts')->assertRedirect('/login');
    }

    public function test_role_without_posts_access_is_forbidden()
    {
        $pendanaan = $this->makeAdminUser('pendanaan');

        $this->actingAs($pendanaan)->get('/admin/posts')->assertForbidden();
    }

    public function test_humas_can_view_posts_index()
    {
        $humas = $this->makeAdminUser('humas');

        $this->actingAs($humas)->get('/admin/posts')->assertOk();
    }

    public function test_administrator_can_view_posts_index()
    {
        $admin = $this->makeAdminUser('administrator');

        $this->actingAs($admin)->get('/admin/posts')->assertOk();
    }
}
