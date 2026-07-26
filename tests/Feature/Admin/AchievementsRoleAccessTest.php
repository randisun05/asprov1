<?php

namespace Tests\Feature\Admin;

use App\Models\Achievement;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementsRoleAccessTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_guest_is_redirected_to_login()
    {
        $this->get('/admin/achievements')->assertRedirect('/login');
    }

    public function test_non_administrator_is_forbidden()
    {
        $humas = $this->makeAdminUser('humas');

        $this->actingAs($humas)->get('/admin/achievements')->assertForbidden();
    }

    public function test_administrator_can_view_achievements_index()
    {
        $admin = $this->makeAdminUser('administrator');

        $this->actingAs($admin)->get('/admin/achievements')->assertOk();
    }

    public function test_administrator_can_toggle_achievement_status()
    {
        $admin = $this->makeAdminUser('administrator');
        $member = Member::create([
            'nip' => '199001012020121002',
            'name' => 'Anggota Test',
            'email' => 'anggota@example.com',
            'agency' => 'Instansi Test',
            'nomember' => '00001/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);
        $achievement = Achievement::create([
            'member_id' => $member->id,
            'title' => 'Penghargaan Test',
            'category' => 'Nasional',
            'description' => 'Deskripsi',
            'date' => now(),
            'icon' => 'fa-trophy',
            'status' => '1',
        ]);

        $response = $this->actingAs($admin)->post("/admin/achievements/{$achievement->id}/change");

        $response->assertRedirect(route('admin.achievements.index'));
        $this->assertSame('0', $achievement->fresh()->status);
    }
}
