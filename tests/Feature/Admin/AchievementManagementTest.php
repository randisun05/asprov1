<?php

namespace Tests\Feature\Admin;

use App\Models\Achievement;
use App\Models\Member;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementManagementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function makeMember(): Member
    {
        return Member::create([
            'nip' => '199001012020121005',
            'name' => 'Anggota Berprestasi',
            'email' => 'berprestasi@example.com',
            'agency' => 'Instansi Contoh',
            'nomember' => '00004/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_store_rejects_a_nip_that_does_not_belong_to_any_member()
    {
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/achievements', [
            'nip' => '999999999999999999',
            'title' => 'Penghargaan Tahunan',
            'description' => 'Deskripsi',
            'category' => 'penghargaan',
            'date' => '2026-01-01',
            'icon' => 'fa fa-star',
        ]);

        $response->assertSessionHasErrors('nip');
        $this->assertDatabaseCount('achievements', 0);
    }

    public function test_store_creates_an_achievement_for_a_valid_member()
    {
        $admin = $this->makeAdminUser('administrator');
        $member = $this->makeMember();

        $response = $this->actingAs($admin)->post('/admin/achievements', [
            'nip' => $member->nip,
            'title' => 'Penghargaan Tahunan',
            'description' => 'Deskripsi',
            'category' => 'penghargaan',
            'date' => '2026-01-01',
            'icon' => 'fa fa-star',
        ]);

        $response->assertRedirect(route('admin.achievements.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('achievements', [
            'member_id' => $member->id,
            'title' => 'Penghargaan Tahunan',
        ]);
    }

    public function test_update_rejects_a_nip_that_does_not_belong_to_any_member()
    {
        $admin = $this->makeAdminUser('administrator');
        $member = $this->makeMember();
        $achievement = Achievement::create([
            'member_id' => $member->id,
            'title' => 'Judul Lama',
            'category' => 'penghargaan',
            'description' => 'Deskripsi',
            'date' => '2026-01-01',
            'icon' => 'fa fa-star',
            'status' => '1',
        ]);

        $response = $this->actingAs($admin)->post("/admin/achievements/{$achievement->id}", [
            'nip' => '999999999999999999',
            'title' => 'Judul Baru',
            'description' => 'Deskripsi',
            'category' => 'penghargaan',
            'date' => '2026-01-01',
            'icon' => 'fa fa-star',
        ]);

        $response->assertSessionHasErrors('nip');
        $this->assertSame('Judul Lama', $achievement->fresh()->title);
    }

    public function test_update_changes_the_achievement_for_a_valid_member()
    {
        $admin = $this->makeAdminUser('administrator');
        $member = $this->makeMember();
        $achievement = Achievement::create([
            'member_id' => $member->id,
            'title' => 'Judul Lama',
            'category' => 'penghargaan',
            'description' => 'Deskripsi',
            'date' => '2026-01-01',
            'icon' => 'fa fa-star',
            'status' => '1',
        ]);

        $response = $this->actingAs($admin)->post("/admin/achievements/{$achievement->id}", [
            'nip' => $member->nip,
            'title' => 'Judul Baru',
            'description' => 'Deskripsi',
            'category' => 'penghargaan',
            'date' => '2026-01-01',
            'icon' => 'fa fa-star',
        ]);

        $response->assertRedirect(route('admin.achievements.index'));
        $response->assertSessionHas('success');
        $this->assertSame('Judul Baru', $achievement->fresh()->title);
    }

    public function test_change_toggles_status_and_destroy_removes_the_achievement()
    {
        $admin = $this->makeAdminUser('administrator');
        $member = $this->makeMember();
        $achievement = Achievement::create([
            'member_id' => $member->id,
            'title' => 'Judul',
            'category' => 'penghargaan',
            'description' => 'Deskripsi',
            'date' => '2026-01-01',
            'icon' => 'fa fa-star',
            'status' => '1',
        ]);

        $response = $this->actingAs($admin)->post("/admin/achievements/{$achievement->id}/change");
        $response->assertSessionHas('success');
        $this->assertSame('0', $achievement->fresh()->status);

        $response = $this->actingAs($admin)->delete("/admin/achievements/{$achievement->id}");
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('achievements', ['id' => $achievement->id]);
    }
}
