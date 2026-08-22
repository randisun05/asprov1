<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestionCategoriesRoleAccessTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_guest_is_redirected_to_login()
    {
        $this->get('/admin/question-categories')->assertRedirect('/login');
    }

    public function test_non_administrator_is_forbidden()
    {
        $humas = $this->makeAdminUser('humas');

        $this->actingAs($humas)->get('/admin/question-categories')->assertForbidden();
    }

    public function test_administrator_can_view_question_categories_index()
    {
        $admin = $this->makeAdminUser('administrator');

        $this->actingAs($admin)->get('/admin/question-categories')->assertOk();
    }
}
