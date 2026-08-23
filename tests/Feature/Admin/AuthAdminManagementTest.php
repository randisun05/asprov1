<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthAdminManagementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_store_uses_the_submitted_password_not_the_nip()
    {
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/setting', [
            'nip' => '198001012010011001',
            'name' => 'Staff Baru',
            'email' => 'staffbaru@example.com',
            'role' => 'humas',
            'position' => 'anggota',
            'password' => 'password-rahasia',
            'password_confirmation' => 'password-rahasia',
        ]);

        $response->assertRedirect(route('admin.setting.index'));
        $response->assertSessionHas('success');

        $created = User::where('nip', '198001012010011001')->first();
        $this->assertNotNull($created);
        $this->assertTrue(Hash::check('password-rahasia', $created->password));
        $this->assertFalse(Hash::check($created->nip, $created->password));
    }

    public function test_store_rejects_a_mismatched_password_confirmation()
    {
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/setting', [
            'nip' => '198001012010011002',
            'name' => 'Staff Baru',
            'email' => 'staffbaru2@example.com',
            'role' => 'humas',
            'position' => 'anggota',
            'password' => 'password-rahasia',
            'password_confirmation' => 'password-lain',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertDatabaseMissing('users', ['nip' => '198001012010011002']);
    }

    public function test_store_rejects_a_duplicate_nip()
    {
        $admin = $this->makeAdminUser('administrator');
        $existing = $this->makeAdminUser('humas');

        $response = $this->actingAs($admin)->post('/admin/setting', [
            'nip' => $existing->nip,
            'name' => 'Staff Baru',
            'email' => 'staffbaru3@example.com',
            'role' => 'humas',
            'position' => 'anggota',
            'password' => 'password-rahasia',
            'password_confirmation' => 'password-rahasia',
        ]);

        $response->assertSessionHasErrors('nip');
    }

    public function test_update_without_a_password_keeps_the_existing_password()
    {
        $admin = $this->makeAdminUser('administrator');
        $target = $this->makeAdminUser('humas', ['password' => Hash::make('password-lama')]);

        $response = $this->actingAs($admin)->put("/admin/setting/{$target->id}", [
            'nip' => $target->nip,
            'name' => 'Nama Diubah',
            'email' => $target->email,
            'role' => 'keanggotaan',
            'position' => 'anggota',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect(route('admin.setting.index'));
        $response->assertSessionHas('success');
        $target->refresh();
        $this->assertSame('Nama Diubah', $target->name);
        $this->assertSame('keanggotaan', $target->role);
        $this->assertTrue(Hash::check('password-lama', $target->password));
    }

    public function test_update_with_a_new_password_changes_it()
    {
        $admin = $this->makeAdminUser('administrator');
        $target = $this->makeAdminUser('humas', ['password' => Hash::make('password-lama')]);

        $response = $this->actingAs($admin)->put("/admin/setting/{$target->id}", [
            'nip' => $target->nip,
            'name' => $target->name,
            'email' => $target->email,
            'role' => $target->role,
            'position' => 'anggota',
            'password' => 'password-baru',
            'password_confirmation' => 'password-baru',
        ]);

        $response->assertSessionHas('success');
        $this->assertTrue(Hash::check('password-baru', $target->fresh()->password));
    }

    public function test_administrator_cannot_delete_their_own_account()
    {
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->delete("/admin/setting/{$admin->id}");

        $response->assertRedirect(route('admin.setting.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_administrator_can_delete_another_account()
    {
        $admin = $this->makeAdminUser('administrator');
        $target = $this->makeAdminUser('humas');

        $response = $this->actingAs($admin)->delete("/admin/setting/{$target->id}");

        $response->assertRedirect(route('admin.setting.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }
}
