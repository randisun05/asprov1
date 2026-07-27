<?php

namespace Tests\Feature\User;

use App\Models\Member;
use App\Models\ProfileDataMain;
use App\Models\ProfileDataPosition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    private function makeMember(): Member
    {
        $member = Member::create([
            'nip' => '199001012020121005',
            'name' => 'Anggota Profil',
            'email' => 'anggotaprofil@example.com',
            'agency' => 'Instansi Lama',
            'nomember' => '00004/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);

        $main = ProfileDataMain::create([
            'nip' => $member->nip,
            'nomember' => $member->nomember,
            'name' => $member->name,
            'email' => $member->email,
            'contact' => '081234567893',
        ]);

        ProfileDataPosition::create([
            'main_id' => $main->id,
            'agency' => 'Instansi Lama',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
        ]);

        return $member;
    }

    public function test_member_can_update_their_main_profile_data()
    {
        $member = $this->makeMember();

        $response = $this->actingAs($member, 'member')->post('/user/profile/edit', [
            'nip' => '199001012020121099',
            'name' => 'Anggota Baru',
            'leveledu' => 'S1',
            'lastedu' => 'Manajemen SDM',
            'place' => 'Jakarta',
            'dob' => '1990-01-01',
            'email' => 'anggotabaru@example.com',
            'contact' => '081234567894',
            'gender' => 'L',
            'religion' => 'Islam',
            'agency' => 'Instansi Baru',
        ]);

        $response->assertRedirect(route('user.profile'));
        $response->assertSessionHasNoErrors();

        $member->refresh();
        $this->assertSame('Anggota Baru', $member->name);
        $this->assertSame('anggotabaru@example.com', $member->email);
        $this->assertSame('Instansi Baru', $member->agency);
        $this->assertSame('199001012020121099', $member->nip);

        // The member's own NIP must stay in sync with profile_data_mains,
        // otherwise the next page load can't find their profile anymore.
        $main = ProfileDataMain::where('nip', '199001012020121099')->first();
        $this->assertNotNull($main);
        $this->assertSame('Anggota Baru', $main->name);
    }

    public function test_updating_profile_with_a_nip_already_used_by_another_member_fails_validation()
    {
        $existing = Member::create([
            'nip' => '199001012020121999',
            'name' => 'Anggota Lain',
            'email' => 'anggotalain@example.com',
            'agency' => 'Instansi Lain',
            'nomember' => '00005/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);
        ProfileDataMain::create([
            'nip' => $existing->nip,
            'nomember' => $existing->nomember,
            'name' => $existing->name,
            'email' => $existing->email,
            'contact' => '081234567895',
        ]);

        $member = $this->makeMember();

        $response = $this->actingAs($member, 'member')->post('/user/profile/edit', [
            'nip' => '199001012020121999',
            'name' => 'Anggota Baru',
            'leveledu' => 'S1',
            'lastedu' => 'Manajemen SDM',
            'place' => 'Jakarta',
            'dob' => '1990-01-01',
            'email' => 'anggotabaru@example.com',
            'contact' => '081234567894',
            'gender' => 'L',
            'religion' => 'Islam',
            'agency' => 'Instansi Baru',
        ]);

        $response->assertSessionHasErrors('nip');
        $this->assertSame('199001012020121005', $member->fresh()->nip);
    }

    public function test_updating_main_profile_with_an_invalid_nip_fails_validation()
    {
        $member = $this->makeMember();

        $response = $this->actingAs($member, 'member')->post('/user/profile/edit', [
            'nip' => '123',
            'name' => 'Anggota Baru',
            'leveledu' => 'S1',
            'lastedu' => 'Manajemen SDM',
            'place' => 'Jakarta',
            'dob' => '1990-01-01',
            'email' => 'anggotabaru@example.com',
            'contact' => '081234567894',
            'gender' => 'L',
            'religion' => 'Islam',
            'agency' => 'Instansi Baru',
        ]);

        $response->assertSessionHasErrors('nip');
        $this->assertSame('199001012020121005', $member->fresh()->nip);
    }

    public function test_member_can_update_their_position_data()
    {
        $member = $this->makeMember();

        $response = $this->actingAs($member, 'member')->post('/user/profile/data-jabatan/edit', [
            'type' => 'PNSP',
            'status' => 'Aktif',
            'agency' => 'Instansi Baru',
            'unit' => 'Unit A',
            'subunit' => 'Sub Unit A',
            'position' => 'Pranata SDM Aparatur',
            'level' => 'Terampil',
            'location' => 'Jakarta',
            'tmtpos' => '2020-01-01',
            'golru' => 'III/a',
            'tmtgolru' => '2020-01-01',
        ]);

        $response->assertRedirect(route('user.profile.jabatan'));
        $response->assertSessionHasNoErrors();

        $main = ProfileDataMain::where('nip', $member->nip)->first();
        $position = ProfileDataPosition::where('main_id', $main->id)->first();
        $this->assertSame('Pranata SDM Aparatur', $position->position);
        $this->assertSame('Instansi Baru', $position->agency);
    }
}
