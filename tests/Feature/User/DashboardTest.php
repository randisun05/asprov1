<?php

namespace Tests\Feature\User;

use App\Models\Member;
use App\Models\ProfileDataMain;
use App\Models\ProfileDataPosition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_member_login()
    {
        $this->get('/user/dashboard')->assertRedirect('/user/login');
    }

    public function test_member_sees_their_own_summary_on_the_dashboard()
    {
        $member = Member::create([
            'nip' => '199001012020121004',
            'name' => 'Anggota Dashboard',
            'email' => 'anggotadashboard@example.com',
            'agency' => 'Instansi Test',
            'nomember' => '00003/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);

        $main = ProfileDataMain::create([
            'nip' => $member->nip,
            'nomember' => $member->nomember,
            'name' => $member->name,
            'email' => $member->email,
            'contact' => '081234567892',
            'statusmember' => 'Anggota Biasa',
        ]);

        ProfileDataPosition::create([
            'main_id' => $main->id,
            'agency' => 'Instansi Test',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
        ]);

        $response = $this->actingAs($member, 'member')->get('/user/dashboard');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('User/Dashboard/Index')
            ->where('summary.eventsJoined', 0)
            ->where('summary.certificates', 0)
            ->where('summary.achievements', 0)
            ->where('profile.position', 'Analis SDM Aparatur')
            ->has('qrCode')
            ->has('foto')
        );
        $this->assertNotNull($member->fresh()->qr_link);
    }
}
