<?php

namespace Tests\Feature\Admin;

use App\Models\ProfileDataMain;
use App\Models\ProfileDataPosition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataMembersManagementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function makeMemberProfile(): ProfileDataPosition
    {
        $main = ProfileDataMain::create([
            'nip' => '199001012020121003',
            'nomember' => '00002/01/ASPROSDMA',
            'name' => 'Anggota Test',
            'email' => 'anggotatest@example.com',
            'contact' => '081234567891',
            'gender' => 'L',
        ]);

        return ProfileDataPosition::create([
            'main_id' => $main->id,
            'agency' => 'Instansi Test',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
        ]);
    }

    public function test_unrelated_role_is_redirected_from_members_index()
    {
        $humas = $this->makeAdminUser('humas');

        $response = $this->actingAs($humas)->get('/admin/members');

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_keanggotaan_can_view_and_search_members()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');
        $this->makeMemberProfile();

        $this->actingAs($keanggotaan)->get('/admin/members')->assertOk();
    }

    public function test_keanggotaan_can_update_a_member_profile()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');
        $profile = $this->makeMemberProfile();

        $response = $this->actingAs($keanggotaan)->put("/admin/members/{$profile->id}", [
            'nip' => '199001012020121099',
            'name' => 'Anggota Diperbarui',
            'email' => 'anggotatest@example.com',
            'contact' => '081234567891',
            'gender' => 'L',
            'agency' => 'Instansi Baru',
            'position' => 'Pranata SDM Aparatur',
            'level' => 'Ahli Muda',
            'status' => 'Aktif',
        ]);

        $response->assertRedirect(route('admin.members.index'));
        $this->assertSame('199001012020121099', $profile->fresh()->main->nip);
        $this->assertSame('Instansi Baru', $profile->fresh()->agency);
    }

    public function test_report_page_does_not_crash_on_a_sparse_database()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');
        $this->makeMemberProfile();

        $this->actingAs($keanggotaan)->get('/admin/members/report')->assertOk();
    }

    public function test_unrelated_role_cannot_download_the_recap()
    {
        $humas = $this->makeAdminUser('humas');

        $response = $this->actingAs($humas)->get('/admin/members/report/recap');

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_keanggotaan_can_download_the_recap()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');
        $this->makeMemberProfile();

        $response = $this->actingAs($keanggotaan)->get('/admin/members/report/recap');

        $response->assertOk();
    }
}
