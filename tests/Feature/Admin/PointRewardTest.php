<?php

namespace Tests\Feature\Admin;

use App\Models\Member;
use App\Models\PointTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PointRewardTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function makeMember(): Member
    {
        return Member::create([
            'nip' => '199001012020121007',
            'name' => 'Anggota Poin',
            'email' => 'anggotapoin@example.com',
            'agency' => 'Instansi Test',
            'nomember' => '00007/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ]);
    }

    public function test_guest_cannot_reward_points()
    {
        $member = $this->makeMember();

        $response = $this->post("/admin/members/{$member->id}/points/reward", ['amount' => 10]);

        $response->assertRedirect('/login');
        $this->assertSame(0, PointTransaction::count());
    }

    public function test_unrelated_role_cannot_reward_points()
    {
        $admin = $this->makeAdminUser('humas');
        $member = $this->makeMember();

        $response = $this->actingAs($admin)->post("/admin/members/{$member->id}/points/reward", ['amount' => 10]);

        $response->assertSessionHas('error');
        $this->assertSame(0, PointTransaction::count());
    }

    public function test_keanggotaan_can_reward_points()
    {
        $admin = $this->makeAdminUser('keanggotaan');
        $member = $this->makeMember();

        $response = $this->actingAs($admin)->post("/admin/members/{$member->id}/points/reward", [
            'amount' => 50,
            'description' => 'Reward keaktifan',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('point_transactions', [
            'member_id' => $member->id,
            'type' => 'reward',
            'amount' => 50,
            'balance_after' => 50,
            'created_by' => $admin->id,
        ]);
    }

    public function test_reward_amount_must_be_positive()
    {
        $admin = $this->makeAdminUser('administrator');
        $member = $this->makeMember();

        $response = $this->actingAs($admin)->post("/admin/members/{$member->id}/points/reward", [
            'amount' => 0,
        ]);

        $response->assertSessionHasErrors('amount');
        $this->assertSame(0, PointTransaction::count());
    }

    public function test_rewards_accumulate_the_running_balance()
    {
        $admin = $this->makeAdminUser('administrator');
        $member = $this->makeMember();

        $this->actingAs($admin)->post("/admin/members/{$member->id}/points/reward", ['amount' => 30]);
        $this->actingAs($admin)->post("/admin/members/{$member->id}/points/reward", ['amount' => 20]);

        $this->assertSame(50, (int) PointTransaction::where('member_id', $member->id)->latest('id')->value('balance_after'));
    }

    private function makeMemberWithNip(string $nip): Member
    {
        return Member::create([
            'nip' => $nip,
            'name' => 'Anggota ' . $nip,
            'email' => "anggota{$nip}@example.com",
            'agency' => 'Instansi Test',
            'nomember' => "0{$nip}/01/ASPROSDMA",
            'password' => bcrypt('password'),
        ]);
    }

    public function test_reward_group_accepts_nips_separated_by_comma_and_newline()
    {
        $admin = $this->makeAdminUser('keanggotaan');
        $memberA = $this->makeMemberWithNip('199001012020121101');
        $memberB = $this->makeMemberWithNip('199001012020121102');
        $memberC = $this->makeMemberWithNip('199001012020121103');

        $response = $this->actingAs($admin)->post('/admin/points/reward-group', [
            'nips' => "{$memberA->nip}, {$memberB->nip}\n{$memberC->nip}",
            'amount' => 25,
            'description' => 'Reward workshop Juli',
        ]);

        $response->assertRedirect(route('admin.members.index'));
        $response->assertSessionHas('success', '3 anggota berhasil diberi poin.');

        foreach ([$memberA, $memberB, $memberC] as $member) {
            $this->assertDatabaseHas('point_transactions', [
                'member_id' => $member->id,
                'type' => 'reward',
                'amount' => 25,
                'balance_after' => 25,
            ]);
        }
    }

    public function test_reward_group_reports_nips_that_are_not_found_without_failing_the_valid_ones()
    {
        $admin = $this->makeAdminUser('administrator');
        $member = $this->makeMemberWithNip('199001012020121104');

        $response = $this->actingAs($admin)->post('/admin/points/reward-group', [
            'nips' => "{$member->nip}, 000000000000000000",
            'amount' => 10,
        ]);

        $response->assertSessionHas('success', '1 anggota berhasil diberi poin.');
        $response->assertSessionHas('warning');
        $this->assertDatabaseHas('point_transactions', [
            'member_id' => $member->id,
            'amount' => 10,
        ]);
    }

    public function test_reward_group_deduplicates_repeated_nips()
    {
        $admin = $this->makeAdminUser('administrator');
        $member = $this->makeMemberWithNip('199001012020121105');

        $this->actingAs($admin)->post('/admin/points/reward-group', [
            'nips' => "{$member->nip}, {$member->nip}, {$member->nip}",
            'amount' => 10,
        ]);

        $this->assertSame(1, PointTransaction::where('member_id', $member->id)->count());
    }

    public function test_unrelated_role_cannot_use_reward_group()
    {
        $admin = $this->makeAdminUser('humas');
        $member = $this->makeMemberWithNip('199001012020121106');

        $response = $this->actingAs($admin)->post('/admin/points/reward-group', [
            'nips' => $member->nip,
            'amount' => 10,
        ]);

        $response->assertSessionHas('error');
        $this->assertSame(0, PointTransaction::count());
    }
}
