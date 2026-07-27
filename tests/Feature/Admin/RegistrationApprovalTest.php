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
}
