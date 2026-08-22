<?php

namespace Tests\Feature\Admin;

use App\Models\Member;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MemberNumberSequenceTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function makeRegistration(array $overrides): Registration
    {
        return Registration::create(array_merge([
            'contact' => '081234560000',
            'agency' => 'Kementerian Contoh',
            'level' => 'Ahli Pertama',
            'status' => 'confirm',
        ], $overrides));
    }

    public function test_numbering_continues_from_the_seeded_sequence_instead_of_restarting_at_one()
    {
        Mail::fake();

        // Simulates what the migration's backfill does for a suffix that
        // already had members before this counter table existed.
        DB::table('member_number_sequences')
            ->where('suffix', '/01/ASPROSDMA')
            ->update(['last_number' => 5]);

        $admin = $this->makeAdminUser('administrator');
        $registration = $this->makeRegistration([
            'nip' => '199001012020121020',
            'name' => 'Anggota Lanjutan',
            'email' => 'lanjutan@example.com',
            'position' => 'Analis SDM Aparatur',
        ]);

        $this->actingAs($admin)->post("/admin/registration/{$registration->id}/approve")
            ->assertSessionHas('success');

        $member = Member::where('nip', '199001012020121020')->first();
        $this->assertSame('00006/01/ASPROSDMA', $member->nomember);
    }

    public function test_different_positions_draw_from_independent_counters()
    {
        Mail::fake();
        $admin = $this->makeAdminUser('administrator');

        $analis = $this->makeRegistration([
            'nip' => '199001012020121021',
            'name' => 'Analis',
            'email' => 'analis@example.com',
            'contact' => '081234560021',
            'position' => 'Analis SDM Aparatur',
        ]);
        $pranata = $this->makeRegistration([
            'nip' => '199001012020121022',
            'name' => 'Pranata',
            'email' => 'pranata@example.com',
            'contact' => '081234560022',
            'position' => 'Pranata SDM Aparatur',
        ]);

        $this->actingAs($admin)->post("/admin/registration/{$analis->id}/approve");
        $this->actingAs($admin)->post("/admin/registration/{$pranata->id}/approve");

        // Both are the first member of their own suffix - neither should
        // be bumped by the other's approval.
        $this->assertSame('00001/01/ASPROSDMA', Member::where('nip', '199001012020121021')->value('nomember'));
        $this->assertSame('0001/02/ASPROSDMA', Member::where('nip', '199001012020121022')->value('nomember'));
    }

    public function test_luar_biasa_approval_uses_its_own_counter()
    {
        $admin = $this->makeAdminUser('administrator');
        $registration = $this->makeRegistration([
            'nip' => '199001012020121023',
            'name' => 'Anggota LB',
            'email' => 'lb@example.com',
            'position' => 'Dosen',
        ]);

        $this->actingAs($admin)->post("/admin/registration/{$registration->id}/approve-lb", [
            'position' => 'Dosen Tamu',
        ]);

        $this->assertSame('0001/LB/ASPROSDMA', Member::where('nip', '199001012020121023')->value('nomember'));
    }
}
