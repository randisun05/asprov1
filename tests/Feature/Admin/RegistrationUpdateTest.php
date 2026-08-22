<?php

namespace Tests\Feature\Admin;

use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationUpdateTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function makeRegistration(array $overrides = []): Registration
    {
        return Registration::create(array_merge([
            'nip' => '199001012020121002',
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'contact' => '081234567890',
            'agency' => 'Kementerian Contoh',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
            'status' => 'submission',
        ], $overrides));
    }

    public function test_administrator_can_correct_registration_data_before_approval()
    {
        $admin = $this->makeAdminUser('administrator');
        $registration = $this->makeRegistration();

        $response = $this->actingAs($admin)->put("/admin/registration/{$registration->id}", [
            'nip' => '199001012020121099',
            'name' => 'Budi Santoso Correct',
            'email' => 'budi@example.com',
            'contact' => '081234567890',
            'agency' => 'Kementerian Contoh',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Muda',
        ]);

        $response->assertRedirect(route('admin.registration.index'));
        $response->assertSessionHas('success');

        $registration->refresh();
        $this->assertSame('199001012020121099', $registration->nip);
        $this->assertSame('Budi Santoso Correct', $registration->name);
        $this->assertSame('Ahli Muda', $registration->level);
    }

    public function test_guest_cannot_update_a_registration()
    {
        $registration = $this->makeRegistration();

        $response = $this->put("/admin/registration/{$registration->id}", [
            'nip' => '199001012020121099',
            'name' => 'Hacked',
            'email' => 'budi@example.com',
            'contact' => '081234567890',
            'agency' => 'Kementerian Contoh',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Muda',
        ]);

        $response->assertRedirect('/login');
        $this->assertSame('Budi Santoso', $registration->fresh()->name);
    }
}
