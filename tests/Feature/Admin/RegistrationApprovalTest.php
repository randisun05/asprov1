<?php

namespace Tests\Feature\Admin;

use App\Mail\SendEmailAprrove;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationApprovalTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function makeRegistration(): Registration
    {
        return Registration::create([
            'nip' => '199001012020121001',
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'contact' => '081234567890',
            'agency' => 'Kementerian Contoh',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
            'status' => 'confirm',
        ]);
    }

    public function test_guest_cannot_approve_registration()
    {
        $registration = $this->makeRegistration();

        $response = $this->post("/admin/registration/{$registration->id}/approve");

        $response->assertRedirect('/login');
        $this->assertSame('confirm', $registration->fresh()->status);
    }

    public function test_get_request_to_approve_route_is_no_longer_allowed()
    {
        $registration = $this->makeRegistration();
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->get("/admin/registration/{$registration->id}/approve");

        $response->assertStatus(405);
    }

    public function test_administrator_can_approve_a_registration()
    {
        Mail::fake();

        $registration = $this->makeRegistration();
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post("/admin/registration/{$registration->id}/approve", [
            'info' => 'Disetujui saat pengujian',
        ]);

        $response->assertRedirect(route('admin.registration.index'));
        $this->assertSame('approved', $registration->fresh()->status);
        $this->assertDatabaseHas('members', [
            'nip' => $registration->nip,
            'email' => $registration->email,
        ]);
        Mail::assertSent(SendEmailAprrove::class);
    }

    public function test_approving_an_already_approved_registration_is_idempotent()
    {
        Mail::fake();

        $registration = $this->makeRegistration();
        $admin = $this->makeAdminUser('administrator');

        $this->actingAs($admin)->post("/admin/registration/{$registration->id}/approve");
        $this->assertSame(1, \App\Models\Member::where('nip', $registration->nip)->count());

        // Double click / retried request on the same registration.
        $response = $this->actingAs($admin)->post("/admin/registration/{$registration->id}/approve");

        $response->assertSessionHas('info');
        $this->assertSame(1, \App\Models\Member::where('nip', $registration->nip)->count());
    }

    public function test_a_failed_approval_email_does_not_undo_the_approval()
    {
        Mail::shouldReceive('to->send')->andThrow(new \Exception('SMTP connection failed'));

        $registration = $this->makeRegistration();
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post("/admin/registration/{$registration->id}/approve");

        $response->assertSessionHas('success');
        $this->assertSame('approved', $registration->fresh()->status);
        $this->assertDatabaseHas('members', ['nip' => $registration->nip]);
    }

    public function test_bulk_approval_assigns_distinct_sequential_member_numbers()
    {
        Mail::fake();

        $registrations = collect(range(1, 4))->map(function ($i) {
            return Registration::create([
                'nip' => "19900101202012100{$i}",
                'name' => "Anggota Bulk {$i}",
                'email' => "anggotabulk{$i}@example.com",
                'contact' => "08123456780{$i}",
                'agency' => 'Kementerian Contoh',
                'position' => 'Analis SDM Aparatur',
                'level' => 'Ahli Pertama',
                'status' => 'confirm',
            ]);
        });
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/registration/group/approve', [
            'registration_ids' => $registrations->pluck('id')->all(),
        ]);

        $response->assertSessionHas('success');
        $numbers = \App\Models\Member::whereIn('nip', $registrations->pluck('nip'))->pluck('nomember')->sort()->values();
        $this->assertCount(4, $numbers);
        $this->assertSame($numbers->all(), $numbers->unique()->values()->all());
        $this->assertSame(
            ['00001/01/ASPROSDMA', '00002/01/ASPROSDMA', '00003/01/ASPROSDMA', '00004/01/ASPROSDMA'],
            $numbers->all()
        );
    }

    public function test_approving_a_luar_biasa_registration_is_idempotent()
    {
        $registration = Registration::create([
            'nip' => '199001012020121099',
            'name' => 'Anggota LB',
            'email' => 'anggotalb@example.com',
            'contact' => '081234567899',
            'agency' => 'Kementerian Contoh',
            'position' => 'Dosen',
            'level' => '-',
            'status' => 'confirm',
        ]);
        $admin = $this->makeAdminUser('administrator');

        $this->actingAs($admin)->post("/admin/registration/{$registration->id}/approve-lb", [
            'position' => 'Praktisi',
        ]);
        $this->assertSame(1, \App\Models\Member::where('nip', $registration->nip)->count());

        $response = $this->actingAs($admin)->post("/admin/registration/{$registration->id}/approve-lb", [
            'position' => 'Praktisi',
        ]);

        $response->assertSessionHas('info');
        $this->assertSame(1, \App\Models\Member::where('nip', $registration->nip)->count());
    }
}
