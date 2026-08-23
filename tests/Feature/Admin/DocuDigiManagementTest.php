<?php

namespace Tests\Feature\Admin;

use App\Models\DocumentDigital;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DocuDigiManagementTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'perihal' => 'Surat Undangan Rapat',
            'speciment' => 'Contoh tanda tangan',
            'nipttd' => '199001012020121001',
            'anchor' => '[TTD]',
            'nipparaf' => '199001012020121002',
            'tujuan' => 'Seluruh Anggota',
            'jenis' => 'Biasa',
            'document' => UploadedFile::fake()->create('surat.pdf', 100, 'application/pdf'),
            'description' => 'Deskripsi surat',
            'no_surat' => '001/SDMA/2026',
            'kategori' => '1',
        ], $overrides);
    }

    public function test_store_rejects_a_request_without_a_document_file()
    {
        Storage::fake('public');
        $admin = $this->makeAdminUser('administrator');

        $payload = $this->payload();
        unset($payload['document']);

        $response = $this->actingAs($admin)->post('/admin/docudigi', $payload);

        $response->assertSessionHasErrors('document');
        $this->assertDatabaseCount('document_digitals', 0);
    }

    public function test_store_creates_a_document_with_valid_data()
    {
        Storage::fake('public');
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/docudigi', $this->payload());

        $response->assertRedirect(route('admin.docudigi.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('document_digitals', [
            'perihal' => 'Surat Undangan Rapat',
            'status' => 'submitted',
        ]);
    }

    public function test_edit_renders_the_edit_page_with_a_single_document()
    {
        Storage::fake('public');
        $admin = $this->makeAdminUser('administrator');
        $docu = DocumentDigital::create(array_merge($this->payload(['document' => null]), [
            'document' => 'documents/surat.pdf',
        ]));

        $response = $this->actingAs($admin)->get("/admin/docudigi/{$docu->id}/edit");

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/DocumentDigital/Edit')
            ->where('docu.id', $docu->id)
        );
    }

    public function test_update_changes_fields_and_keeps_existing_document_when_no_new_file_uploaded()
    {
        Storage::fake('public');
        $admin = $this->makeAdminUser('administrator');
        $docu = DocumentDigital::create(array_merge($this->payload(['document' => null]), [
            'document' => 'documents/surat-asli.pdf',
        ]));

        $response = $this->actingAs($admin)->put("/admin/docudigi/{$docu->id}", array_merge(
            $this->payload(['document' => null]),
            ['perihal' => 'Perihal Sudah Diperbarui']
        ));

        $response->assertRedirect(route('admin.docudigi.index'));
        $response->assertSessionHas('success');
        $docu->refresh();
        $this->assertSame('Perihal Sudah Diperbarui', $docu->perihal);
        $this->assertSame('documents/surat-asli.pdf', $docu->document);
    }

    public function test_update_does_not_allow_directly_overwriting_status_via_mass_assignment()
    {
        Storage::fake('public');
        $admin = $this->makeAdminUser('administrator');
        $docu = DocumentDigital::create(array_merge($this->payload(['document' => null]), [
            'document' => 'documents/surat-asli.pdf',
            'status' => 'submitted',
        ]));

        $this->actingAs($admin)->put("/admin/docudigi/{$docu->id}", array_merge(
            $this->payload(['document' => null]),
            ['status' => 'approved']
        ));

        $this->assertSame('submitted', $docu->fresh()->status);
    }

    public function test_paraf_is_rejected_for_a_user_who_is_not_the_target_signer_or_an_overseer()
    {
        $humas = $this->makeAdminUser('humas', ['nip' => '199001012020129999']);
        $docu = DocumentDigital::create(array_merge($this->payload(['document' => null]), [
            'document' => 'documents/surat.pdf',
            'nipparaf' => '199001012020121002',
            'status' => 'submitted',
        ]));

        $response = $this->actingAs($humas)->post("/admin/docudigi/{$docu->id}/paraf");

        $response->assertSessionHas('error');
        $this->assertSame('submitted', $docu->fresh()->status);
    }

    public function test_paraf_succeeds_for_the_target_signer()
    {
        $signer = $this->makeAdminUser('humas', ['nip' => '199001012020121002']);
        $docu = DocumentDigital::create(array_merge($this->payload(['document' => null]), [
            'document' => 'documents/surat.pdf',
            'nipparaf' => '199001012020121002',
            'status' => 'submitted',
        ]));

        $response = $this->actingAs($signer)->post("/admin/docudigi/{$docu->id}/paraf");

        $response->assertSessionHas('success');
        $this->assertSame('paraf', $docu->fresh()->status);
    }

    public function test_approve_is_rejected_for_a_user_who_is_not_the_target_signer_or_an_overseer()
    {
        $humas = $this->makeAdminUser('humas', ['nip' => '199001012020129999']);
        $docu = DocumentDigital::create(array_merge($this->payload(['document' => null]), [
            'document' => 'documents/surat.pdf',
            'nipttd' => '199001012020121001',
            'status' => 'paraf',
        ]));

        $response = $this->actingAs($humas)->post("/admin/docudigi/{$docu->id}/approve");

        $response->assertSessionHas('error');
        $this->assertSame('paraf', $docu->fresh()->status);
    }

    public function test_cancel_rejects_an_incorrect_password()
    {
        $admin = $this->makeAdminUser('administrator', ['password' => bcrypt('correct-password')]);
        $docu = DocumentDigital::create(array_merge($this->payload(['document' => null]), [
            'document' => 'documents/surat.pdf',
            'status' => 'submitted',
        ]));

        $response = $this->actingAs($admin)->post("/admin/docudigi/{$docu->id}/cancel", [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHas('error');
        $this->assertSame('submitted', $docu->fresh()->status);
    }

    public function test_cancel_succeeds_with_the_correct_password()
    {
        $admin = $this->makeAdminUser('administrator', ['password' => bcrypt('correct-password')]);
        $docu = DocumentDigital::create(array_merge($this->payload(['document' => null]), [
            'document' => 'documents/surat.pdf',
            'status' => 'submitted',
        ]));

        $response = $this->actingAs($admin)->post("/admin/docudigi/{$docu->id}/cancel", [
            'password' => 'correct-password',
        ]);

        $response->assertSessionHas('success');
        $this->assertSame('cancelled', $docu->fresh()->status);
    }

    public function test_destroy_removes_the_document()
    {
        $admin = $this->makeAdminUser('administrator');
        $docu = DocumentDigital::create(array_merge($this->payload(['document' => null]), [
            'document' => 'documents/surat.pdf',
        ]));

        $response = $this->actingAs($admin)->delete("/admin/docudigi/{$docu->id}");

        $response->assertRedirect(route('admin.docudigi.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('document_digitals', ['id' => $docu->id]);
    }
}
