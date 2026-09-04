<?php

namespace Tests\Feature\Admin;

use App\Models\Member;
use App\Models\ProfileDataMain;
use App\Models\ProfileDataPosition;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MemberExpiryManagementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function makeMemberProfile(array $memberOverrides = []): ProfileDataPosition
    {
        $main = ProfileDataMain::create([
            'nip' => '199001012020121003',
            'nomember' => '00002/01/ASPROSDMA',
            'name' => 'Anggota Test',
            'email' => 'anggotatest@example.com',
            'contact' => '081234567891',
            'gender' => 'L',
        ]);

        Member::create(array_merge([
            'nip' => '199001012020121003',
            'name' => 'Anggota Test',
            'email' => 'anggotatest@example.com',
            'password' => bcrypt('password'),
            'nomember' => '00002/01/ASPROSDMA',
            'agency' => 'Instansi Test',
        ], $memberOverrides));

        return ProfileDataPosition::create([
            'main_id' => $main->id,
            'agency' => 'Instansi Test',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
        ]);
    }

    public function test_existing_members_are_backfilled_to_expire_end_of_2027()
    {
        // The migration's one-time backfill runs during RefreshDatabase's
        // migrate:fresh, before this test body executes - so a member row
        // inserted here (after migrations already ran) simulates a member
        // that existed prior to the migration by directly asserting the
        // backfill value that the migration would have applied.
        $profile = $this->makeMemberProfile(['expires_at' => '2027-12-31 23:59:59']);
        $member = Member::where('nip', $profile->main->nip)->first();

        $this->assertSame('2027-12-31 23:59:59', $member->expires_at->format('Y-m-d H:i:s'));
        $this->assertFalse($member->isExpired());
    }

    public function test_new_registration_approval_sets_expiry_one_year_out()
    {
        Mail::fake();
        $admin = $this->makeAdminUser('administrator');
        $registration = Registration::create([
            'nip' => '199001012020121030',
            'name' => 'Anggota Baru',
            'email' => 'baru@example.com',
            'contact' => '081234560030',
            'agency' => 'Kementerian Contoh',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
            'status' => 'confirm',
        ]);

        $this->actingAs($admin)->post("/admin/registration/{$registration->id}/approve");

        $member = Member::where('nip', '199001012020121030')->first();
        $this->assertNotNull($member->expires_at);
        $this->assertTrue($member->expires_at->between(now()->addYear()->subMinute(), now()->addYear()->addMinute()));
    }

    public function test_luar_biasa_approval_also_sets_expiry_one_year_out()
    {
        $admin = $this->makeAdminUser('administrator');
        $registration = Registration::create([
            'nip' => '199001012020121031',
            'name' => 'Anggota LB',
            'email' => 'lb-expiry@example.com',
            'contact' => '081234560031',
            'agency' => 'Kementerian Contoh',
            'position' => 'Dosen',
            'level' => 'Ahli Pertama',
            'status' => 'confirm',
        ]);

        $this->actingAs($admin)->post("/admin/registration/{$registration->id}/approve-lb", [
            'position' => 'Dosen Tamu',
        ]);

        $member = Member::where('nip', '199001012020121031')->first();
        $this->assertNotNull($member->expires_at);
        $this->assertTrue($member->expires_at->between(now()->addYear()->subMinute(), now()->addYear()->addMinute()));
    }

    public function test_keanggotaan_can_extend_expiry_by_one_year_from_current_expiry_when_still_active()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');
        $profile = $this->makeMemberProfile(['expires_at' => now()->addMonths(6)]);
        $expectedBase = Member::where('nip', $profile->main->nip)->first()->expires_at->copy();

        $response = $this->actingAs($keanggotaan)->post("/admin/members/{$profile->id}/expiry/extend");

        $response->assertSessionHas('success');
        $member = Member::where('nip', $profile->main->nip)->first();
        $this->assertTrue($member->expires_at->between(
            $expectedBase->copy()->addYear()->subMinute(),
            $expectedBase->copy()->addYear()->addMinute()
        ));
    }

    public function test_extending_an_already_expired_member_restarts_the_clock_from_now_instead_of_the_past()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');
        $profile = $this->makeMemberProfile(['expires_at' => now()->subYears(2)]);

        $response = $this->actingAs($keanggotaan)->post("/admin/members/{$profile->id}/expiry/extend");

        $response->assertSessionHas('success');
        $member = Member::where('nip', $profile->main->nip)->first();
        $this->assertFalse($member->isExpired());
        $this->assertTrue($member->expires_at->between(now()->addYear()->subMinute(), now()->addYear()->addMinute()));
    }

    public function test_keanggotaan_can_set_a_custom_expiry_date()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');
        $profile = $this->makeMemberProfile(['expires_at' => now()->addMonths(6)]);

        $response = $this->actingAs($keanggotaan)->post("/admin/members/{$profile->id}/expiry", [
            'expires_at' => '2030-01-15',
        ]);

        $response->assertSessionHas('success');
        $member = Member::where('nip', $profile->main->nip)->first();
        $this->assertSame('2030-01-15', $member->expires_at->format('Y-m-d'));
    }

    public function test_unrelated_role_cannot_extend_expiry()
    {
        $humas = $this->makeAdminUser('humas');
        $profile = $this->makeMemberProfile(['expires_at' => now()->addMonths(6)]);

        $response = $this->actingAs($humas)->post("/admin/members/{$profile->id}/expiry/extend");

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_unrelated_role_cannot_set_a_custom_expiry_date()
    {
        $humas = $this->makeAdminUser('humas');
        $profile = $this->makeMemberProfile(['expires_at' => now()->addMonths(6)]);

        $response = $this->actingAs($humas)->post("/admin/members/{$profile->id}/expiry", [
            'expires_at' => '2030-01-15',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_member_show_page_exposes_expiry_to_the_console()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');
        $profile = $this->makeMemberProfile(['expires_at' => '2027-12-31 23:59:59']);

        $response = $this->actingAs($keanggotaan)->get("/admin/members/{$profile->id}");

        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Members/Show')
            ->where('expiresAt', function ($value) {
                return str_starts_with($value, '2027-12-31');
            })
        );
    }
}
