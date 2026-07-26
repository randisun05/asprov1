<?php

namespace Tests\Feature\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JurnalRoleAccessTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function payload(): array
    {
        return [
            'title' => 'Iuran Anggota',
            'nominal' => 100000,
            'type' => 'debit',
            'date' => now()->toDateString(),
            'bukti' => UploadedFile::fake()->image('bukti.jpg'),
        ];
    }

    public function test_pendanaan_role_can_create_a_jurnal_entry()
    {
        Storage::fake('public');
        $pendanaan = $this->makeAdminUser('pendanaan');

        $this->actingAs($pendanaan)->post('/admin/jurnals', $this->payload());

        $this->assertDatabaseHas('jurnals', ['title' => 'Iuran Anggota']);
    }

    public function test_unrelated_role_cannot_create_a_jurnal_entry()
    {
        Storage::fake('public');
        $humas = $this->makeAdminUser('humas');

        $response = $this->actingAs($humas)->post('/admin/jurnals', $this->payload());

        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('jurnals', ['title' => 'Iuran Anggota']);
    }
}
