<?php

namespace Tests\Feature\Admin;

use App\Models\Archive;
use App\Models\DetailArchive;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArchiveUpdateTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function makeArchive(array $overrides = []): Archive
    {
        return Archive::create(array_merge([
            'noticket' => '260822' . rand(1000, 9999),
            'nip' => '199001012020121040',
            'name' => 'Pengirim Arsip',
            'email' => 'pengirim@example.com',
            'contact' => '081234560040',
            'agency' => 'Instansi Test',
            'position' => 'Staff',
            'category' => 'Pertanyaan',
            'title' => 'Judul',
            'status' => '1',
            'document' => 'documents/existing-file.pdf',
        ], $overrides));
    }

    public function test_updating_an_archive_without_a_new_file_keeps_the_existing_document()
    {
        $admin = $this->makeAdminUser('administrator');
        $archive = $this->makeArchive();

        $response = $this->actingAs($admin)->put("/admin/archives/{$archive->id}", [
            'nip' => $archive->nip,
            'name' => 'Nama Diperbaiki',
            'email' => $archive->email,
            'contact' => $archive->contact,
            'agency' => $archive->agency,
            'position' => $archive->position,
            'category' => $archive->category,
            'title' => $archive->title,
        ]);

        $response->assertRedirect(route('admin.archives.index'));
        $response->assertSessionHas('success');

        $archive->refresh();
        $this->assertSame('Nama Diperbaiki', $archive->name);
        $this->assertSame('documents/existing-file.pdf', $archive->document);
    }

    public function test_updating_inbox_detail_cannot_reassign_ownership_fields()
    {
        $admin = $this->makeAdminUser('administrator');
        $archive = $this->makeArchive();
        $otherArchive = $this->makeArchive(['noticket' => '260822' . rand(1000, 9999), 'nip' => '199001012020121041']);

        $detail = DetailArchive::create([
            'archive_id' => $archive->id,
            'noticket' => $archive->noticket,
            'user_id' => $admin->id,
            'title' => $archive->title,
            'detail' => '',
            'status' => '1',
            'from' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->post("/admin/archives/inbox/{$detail->id}/update", [
            'detail' => 'Sudah ditindaklanjuti',
            'status' => '2',
            'archive_id' => $otherArchive->id,
            'user_id' => 999999,
            'from' => 999999,
        ]);

        $response->assertRedirect(route('admin.archives.inbox'));
        $response->assertSessionHas('success');

        $detail->refresh();
        $this->assertSame('Sudah ditindaklanjuti', $detail->detail);
        $this->assertSame('2', $detail->status);
        // Ownership/relation fields must not be overwritten by the request.
        $this->assertEquals($archive->id, $detail->archive_id);
        $this->assertEquals($admin->id, $detail->user_id);
        $this->assertEquals($admin->id, $detail->from);
    }
}
