<?php

namespace Tests\Feature\Admin;

use App\Models\Jurnal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JurnalManagementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    public function test_guest_is_redirected_to_login()
    {
        $this->get('/admin/jurnals')->assertRedirect('/login');
    }

    public function test_role_without_jurnal_access_is_forbidden()
    {
        $humas = $this->makeAdminUser('humas');

        $this->actingAs($humas)->get('/admin/jurnals')->assertForbidden();
    }

    public function test_pendanaan_can_view_jurnal_index()
    {
        $pendanaan = $this->makeAdminUser('pendanaan');

        $this->actingAs($pendanaan)->get('/admin/jurnals')->assertOk();
    }

    public function test_pendanaan_can_create_a_jurnal_entry_and_saldo_is_computed()
    {
        Storage::fake('public');
        $pendanaan = $this->makeAdminUser('pendanaan');

        $response = $this->actingAs($pendanaan)->post('/admin/jurnals', [
            'title' => 'Iuran Anggota',
            'nominal' => 100000,
            'type' => 'debit',
            'date' => '2026-08-01',
            'bukti' => UploadedFile::fake()->image('bukti.jpg'),
        ]);

        $response->assertRedirect(route('admin.jurnals.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('jurnals', [
            'title' => 'Iuran Anggota',
            'nomor' => 1,
            'saldo' => 100000,
        ]);
    }

    public function test_second_entry_continues_the_nomor_sequence_and_saldo()
    {
        Storage::fake('public');
        $pendanaan = $this->makeAdminUser('pendanaan');

        Jurnal::create([
            'title' => 'Entry Pertama',
            'nominal' => 100000,
            'type' => 'debit',
            'saldo' => 100000,
            'nomor' => 1,
            'date' => '2026-08-01',
        ]);

        $response = $this->actingAs($pendanaan)->post('/admin/jurnals', [
            'title' => 'Pengeluaran ATK',
            'nominal' => 30000,
            'type' => 'kredit',
            'date' => '2026-08-02',
            'bukti' => UploadedFile::fake()->image('bukti2.jpg'),
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('jurnals', [
            'title' => 'Pengeluaran ATK',
            'nomor' => 2,
            'saldo' => 70000,
        ]);
    }

    public function test_updating_an_entry_rejects_a_non_document_file_as_bukti()
    {
        Storage::fake('public');
        $pendanaan = $this->makeAdminUser('pendanaan');

        $jurnal = Jurnal::create([
            'title' => 'Entry Awal',
            'nominal' => 50000,
            'type' => 'debit',
            'saldo' => 50000,
            'nomor' => 1,
            'date' => '2026-08-01',
        ]);

        $response = $this->actingAs($pendanaan)->put("/admin/jurnals/{$jurnal->id}", [
            'title' => 'Entry Awal',
            'nominal' => 50000,
            'type' => 'debit',
            'date' => '2026-08-01',
            'bukti' => UploadedFile::fake()->create('malicious.php', 10),
        ]);

        $response->assertSessionHasErrors('bukti');
    }

    public function test_deleting_an_entry_recalculates_subsequent_saldo()
    {
        $pendanaan = $this->makeAdminUser('pendanaan');

        $first = Jurnal::create([
            'title' => 'Entry 1', 'nominal' => 100000, 'type' => 'debit',
            'saldo' => 100000, 'nomor' => 1, 'date' => '2026-08-01',
        ]);
        $second = Jurnal::create([
            'title' => 'Entry 2', 'nominal' => 20000, 'type' => 'kredit',
            'saldo' => 80000, 'nomor' => 2, 'date' => '2026-08-02',
        ]);
        $third = Jurnal::create([
            'title' => 'Entry 3', 'nominal' => 10000, 'type' => 'kredit',
            'saldo' => 70000, 'nomor' => 3, 'date' => '2026-08-03',
        ]);

        $response = $this->actingAs($pendanaan)->delete("/admin/jurnals/{$second->id}");

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('jurnals', ['id' => $second->id]);
        // saldo for entry 3 should now chain directly off entry 1's saldo
        $this->assertSame(90000, $third->fresh()->saldo);
    }
}
