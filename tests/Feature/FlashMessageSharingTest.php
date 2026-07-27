<?php

namespace Tests\Feature;

use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Admin\CreatesAdminUsers;
use Tests\TestCase;

class FlashMessageSharingTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_info_and_warning_flash_keys_are_shared_to_inertia_props()
    {
        // admin.security.two-factor flashes a 'warning' key when redirecting
        // an unconfirmed administrator/sekretariat away from the rest of the
        // admin panel - this is the simplest real path that sets 'warning'.
        $admin = $this->makeAdminUser('administrator', [], twoFactorConfirmed: false);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertRedirect(route('admin.security.two-factor'));

        $follow = $this->actingAs($admin)->get(route('admin.security.two-factor'));
        $follow->assertInertia(fn ($page) => $page->where('session.warning', 'Role Anda mewajibkan aktivasi 2FA sebelum melanjutkan.'));
    }

    public function test_info_flash_from_an_already_processed_registration_is_shared_to_inertia_props()
    {
        $registration = Registration::create([
            'nip' => '199001012020121050',
            'name' => 'Anggota Flash',
            'email' => 'anggotaflash@example.com',
            'contact' => '081234567850',
            'agency' => 'Kementerian Contoh',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
            'status' => 'confirm',
        ]);
        $admin = $this->makeAdminUser('administrator');

        $this->actingAs($admin)->post("/admin/registration/{$registration->id}/approve");

        $response = $this->actingAs($admin)->post("/admin/registration/{$registration->id}/approve");
        $follow = $this->actingAs($admin)->get(route('admin.registration.index'));

        $follow->assertInertia(fn ($page) => $page->where('session.info', 'Pendaftaran ini sudah diproses sebelumnya.'));
    }
}
