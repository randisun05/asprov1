<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelSmokeTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_guest_is_redirected_away_from_the_dashboard()
    {
        $this->get('/admin/dashboard')->assertRedirect('/login');
    }

    public function test_any_authenticated_staff_can_view_the_dashboard()
    {
        $humas = $this->makeAdminUser('humas');

        $this->actingAs($humas)->get('/admin/dashboard')->assertOk();
    }

    public function test_any_authenticated_staff_can_view_management_content()
    {
        $hukum = $this->makeAdminUser('hukum');

        $this->actingAs($hukum)->get('/admin/management')->assertOk();
    }

    public function test_any_authenticated_staff_can_view_document_digital_inbox()
    {
        $kapasitas = $this->makeAdminUser('kapasitas');

        $this->actingAs($kapasitas)->get('/admin/docudigi')->assertOk();
    }

    public function test_any_authenticated_staff_can_view_archives_inbox()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');

        $this->actingAs($keanggotaan)->get('/admin/archives')->assertOk();
    }
}
