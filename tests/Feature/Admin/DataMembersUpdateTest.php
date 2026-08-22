<?php

namespace Tests\Feature\Admin;

use App\Models\ProfileDataMain;
use App\Models\ProfileDataPosition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DataMembersUpdateTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function makeProfile(): ProfileDataPosition
    {
        $main = ProfileDataMain::create([
            'nip' => '199001012020121011',
            'name' => 'Anggota Lama',
            'email' => 'lama@example.com',
            'gender' => 'L',
            'nomember' => '00011/01/ASPROSDMA',
        ]);

        return ProfileDataPosition::create([
            'main_id' => $main->id,
            'agency' => 'Instansi Lama',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
        ]);
    }

    public function test_updating_a_member_saves_every_field_not_just_nip()
    {
        $admin = $this->makeAdminUser('keanggotaan');
        $profile = $this->makeProfile();

        $response = $this->actingAs($admin)->put("/admin/members/{$profile->id}", [
            'nip' => $profile->main->nip,
            'name' => 'Anggota Baru',
            'email' => 'baru@example.com',
            'gender' => 'P',
            'agency' => 'Instansi Baru',
            'position' => 'Pranata SDM Aparatur',
            'level' => 'Terampil',
            'status' => 'Aktif',
        ]);

        $response->assertRedirect(route('admin.members.index'));
        $response->assertSessionHas('success');

        $profile->refresh();
        $profile->main->refresh();

        $this->assertSame('Anggota Baru', $profile->main->name);
        $this->assertSame('baru@example.com', $profile->main->email);
        $this->assertSame('P', $profile->main->gender);
        $this->assertSame('Instansi Baru', $profile->agency);
        $this->assertSame('Pranata SDM Aparatur', $profile->position);
        $this->assertSame('Terampil', $profile->level);
    }

    public function test_editing_a_nonexistent_member_returns_404_instead_of_crashing()
    {
        $admin = $this->makeAdminUser('keanggotaan');

        $response = $this->actingAs($admin)->get('/admin/members/999999/edit');

        $response->assertNotFound();
    }

    public function test_viewing_a_nonexistent_member_returns_404_instead_of_crashing()
    {
        $admin = $this->makeAdminUser('keanggotaan');

        $response = $this->actingAs($admin)->get('/admin/members/999999');

        $response->assertNotFound();
    }

    public function test_admin_can_update_a_member_photo()
    {
        \Illuminate\Support\Facades\Storage::fake('public');
        $admin = $this->makeAdminUser('keanggotaan');
        $profile = $this->makeProfile();

        $response = $this->actingAs($admin)->post("/admin/members/{$profile->id}/image", [
            'image' => \Illuminate\Http\UploadedFile::fake()->image('foto.jpg'),
        ]);

        $response->assertRedirect(route('admin.members.edit', $profile->id));
        $response->assertSessionHas('success');
        $this->assertNotNull($profile->main->fresh()->image);
    }
}
