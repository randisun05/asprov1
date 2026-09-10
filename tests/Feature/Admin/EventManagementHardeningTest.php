<?php

namespace Tests\Feature\Admin;

use App\Models\Certificate;
use App\Models\DetailEvent;
use App\Models\Event;
use App\Models\Member;
use App\Models\TemplateCertificate;
use App\Services\CertificateGenerator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class EventManagementHardeningTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function makeMember(array $overrides = []): Member
    {
        static $counter = 0;
        $counter++;

        return Member::create(array_merge([
            'nip' => '19900101202066' . str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'name' => 'Anggota ' . $counter,
            'email' => "anggotahardening{$counter}@example.com",
            'agency' => 'Instansi Test',
            'nomember' => '0006' . $counter . '/01/ASPROSDMA',
            'password' => bcrypt('password'),
        ], $overrides));
    }

    private function makeCertificate(array $overrides = []): Certificate
    {
        return Certificate::create(array_merge([
            'event_id' => Event::factory()->create()->id,
            'no_certificate' => '0001/Test/PP Aspro SDMA/01/2026',
            'category' => 'Test',
            'nip' => '199001012020121099',
            'name' => 'Peserta Test',
            'body' => 'Kegiatan Test',
            'date' => now()->toDateString(),
            'template' => (string) Str::uuid(),
            'status' => '1',
            'qr_code' => 'https://asprosdma.id/certificates/' . Str::uuid(),
            'link' => (string) Str::uuid(),
            'doc' => '',
        ], $overrides));
    }

    // --- update(): image validation ---

    public function test_updating_an_event_rejects_an_invalid_image_file_type()
    {
        Storage::fake('local');
        $admin = $this->makeAdminUser('administrator');
        $event = Event::factory()->create();

        $response = $this->actingAs($admin)->post("/admin/events/{$event->id}", [
            'title' => 'Judul Baru',
            'body' => 'Deskripsi',
            'date' => now()->toDateString(),
            'participant' => 50,
            'enddate' => now()->addDay()->toDateString(),
            'place' => 'Jakarta',
            'link' => '-',
            'file' => 'N',
            'category' => 'Kombel',
            'template' => (string) Str::uuid(),
            'duration' => 60,
            'start_at' => now()->toDateTimeString(),
            'end_at' => now()->addHour()->toDateTimeString(),
            'image' => UploadedFile::fake()->create('virus.exe', 100),
        ]);

        $response->assertSessionHasErrors('image');
    }

    public function test_updating_an_event_with_a_new_image_deletes_the_old_one()
    {
        Storage::fake('local');
        $admin = $this->makeAdminUser('administrator');
        $oldImage = UploadedFile::fake()->image('old.jpg')->store('/images');
        $event = Event::factory()->create(['image' => $oldImage]);

        $response = $this->actingAs($admin)->post("/admin/events/{$event->id}", [
            'title' => 'Judul Baru',
            'body' => 'Deskripsi',
            'date' => now()->toDateString(),
            'participant' => 50,
            'enddate' => now()->addDay()->toDateString(),
            'place' => 'Jakarta',
            'link' => '-',
            'file' => 'N',
            'category' => 'Kombel',
            'template' => (string) Str::uuid(),
            'duration' => 60,
            'start_at' => now()->toDateTimeString(),
            'end_at' => now()->addHour()->toDateTimeString(),
            'image' => UploadedFile::fake()->image('new.jpg'),
        ]);

        $response->assertSessionHas('success');
        Storage::disk('local')->assertMissing($oldImage);
        $this->assertNotSame($oldImage, $event->fresh()->image);
    }

    // --- destroy(): FK violation handling + image cleanup ---

    public function test_deleting_an_event_with_certificates_shows_a_friendly_error_instead_of_crashing()
    {
        $admin = $this->makeAdminUser('administrator');
        $certificate = $this->makeCertificate();
        $event = Event::find($certificate->event_id);

        $response = $this->actingAs($admin)->delete("/admin/events/{$event->id}");

        $response->assertRedirect(route('admin.events.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('events', ['id' => $event->id]);
    }

    public function test_deleting_an_event_with_participants_shows_a_friendly_error_instead_of_crashing()
    {
        $admin = $this->makeAdminUser('administrator');
        $member = $this->makeMember();
        $event = Event::factory()->create();
        DetailEvent::create(['event_id' => $event->id, 'member_id' => $member->id, 'status' => 'approved']);

        $response = $this->actingAs($admin)->delete("/admin/events/{$event->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('events', ['id' => $event->id]);
    }

    public function test_deleting_an_event_with_no_participants_or_certificates_succeeds_and_removes_its_image()
    {
        Storage::fake('local');
        $admin = $this->makeAdminUser('administrator');
        $image = UploadedFile::fake()->image('event.jpg')->store('/images');
        $event = Event::factory()->create(['image' => $image]);

        $response = $this->actingAs($admin)->delete("/admin/events/{$event->id}");

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
        Storage::disk('local')->assertMissing($image);
    }

    // --- absenAll(): batch update ---

    public function test_absen_all_marks_every_participant_present()
    {
        $admin = $this->makeAdminUser('administrator');
        $event = Event::factory()->create();
        $memberA = $this->makeMember();
        $memberB = $this->makeMember();
        DetailEvent::create(['event_id' => $event->id, 'member_id' => $memberA->id, 'status' => 'approved']);
        DetailEvent::create(['event_id' => $event->id, 'member_id' => $memberB->id, 'status' => 'approved']);

        $this->actingAs($admin)->post("/admin/events/{$event->id}/absenall");

        $this->assertSame(2, DetailEvent::where('event_id', $event->id)->where('status', 'hadir')->count());
    }

    // --- certificatesTemplateStore(): file type validation ---

    public function test_uploading_a_certificate_template_rejects_a_non_pdf_file()
    {
        Storage::fake('local');
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/events/certificates/templates/store', [
            'title' => 'Template Baru',
            'image' => UploadedFile::fake()->image('template.jpg'),
        ]);

        $response->assertSessionHasErrors('image');
        $this->assertDatabaseCount('template_certificates', 0);
    }

    public function test_uploading_a_valid_pdf_certificate_template_succeeds()
    {
        Storage::fake('local');
        $admin = $this->makeAdminUser('administrator');

        $response = $this->actingAs($admin)->post('/admin/events/certificates/templates/store', [
            'title' => 'Template PDF',
            'image' => UploadedFile::fake()->create('template.pdf', 200, 'application/pdf'),
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('template_certificates', ['title' => 'Template PDF']);
    }

    public function test_deleting_a_certificate_template_removes_its_file_from_storage()
    {
        Storage::fake('local');
        $admin = $this->makeAdminUser('administrator');
        $path = UploadedFile::fake()->create('template.pdf', 200)->store('/template');
        $template = TemplateCertificate::create(['title' => 'Template Lama', 'image' => $path, 'status' => '1']);

        $this->actingAs($admin)->delete("/admin/events/certificates/templates/{$template->id}");

        Storage::disk('local')->assertMissing($path);
    }

    public function test_deleting_a_certificate_template_still_used_by_an_event_is_blocked()
    {
        Storage::fake('local');
        $admin = $this->makeAdminUser('administrator');
        $path = UploadedFile::fake()->create('template.pdf', 200)->store('/template');
        $template = TemplateCertificate::create(['title' => 'Template Dipakai', 'image' => $path, 'status' => '1']);
        Event::factory()->create(['template_id' => (string) $template->id]);

        $response = $this->actingAs($admin)->delete("/admin/events/certificates/templates/{$template->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('template_certificates', ['id' => $template->id]);
        Storage::disk('local')->assertExists($path);
    }

    public function test_deleting_a_certificate_template_still_used_by_a_certificate_is_blocked()
    {
        Storage::fake('local');
        $admin = $this->makeAdminUser('administrator');
        $path = UploadedFile::fake()->create('template.pdf', 200)->store('/template');
        $template = TemplateCertificate::create(['title' => 'Template Dipakai Sertifikat', 'image' => $path, 'status' => '1']);
        $this->makeCertificate(['template' => (string) $template->id]);

        $response = $this->actingAs($admin)->delete("/admin/events/certificates/templates/{$template->id}");

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('template_certificates', ['id' => $template->id]);
    }

    // --- exportParticipant(): search filter fix ---

    public function test_export_participant_search_filters_by_member_name()
    {
        $admin = $this->makeAdminUser('administrator');
        $event = Event::factory()->create();
        $target = $this->makeMember(['name' => 'Budi Santoso']);
        $other = $this->makeMember(['name' => 'Siti Aminah']);
        DetailEvent::create(['event_id' => $event->id, 'member_id' => $target->id, 'title' => 'peserta', 'status' => 'approved']);
        DetailEvent::create(['event_id' => $event->id, 'member_id' => $other->id, 'title' => 'peserta', 'status' => 'approved']);

        $response = $this->actingAs($admin)->get("/admin/events/{$event->id}/export?q=Budi");

        $response->assertOk();
    }

    // --- certificatesStore()/certificatesImportStore(): doc field bug ---

    public function test_manually_creating_a_certificate_does_not_store_the_agency_name_in_the_doc_field()
    {
        $admin = $this->makeAdminUser('administrator');
        $event = Event::factory()->create();

        $this->actingAs($admin)->post("/admin/events/{$event->id}/certificates/store", [
            'category' => 'Seminar',
            'nip' => '199001012020121050',
            'name' => 'Anggota Sertifikat',
            'date' => now()->toDateString(),
            'template' => (string) Str::uuid(),
            'agency' => 'Instansi Test',
        ]);

        $certificate = Certificate::where('nip', '199001012020121050')->first();
        $this->assertNotNull($certificate);
        $this->assertSame('', $certificate->doc);
    }

    // --- certificatesExcelStore(): no longer hardcoded ---

    public function test_excel_import_uses_the_submitted_category_date_and_template_instead_of_hardcoded_values()
    {
        $admin = $this->makeAdminUser('administrator');
        $event = Event::factory()->create();
        $templateId = (string) Str::uuid();

        $file = UploadedFile::fake()->createWithContent(
            'sertifikat.csv',
            "nip,name,instansi\n199001012020121077,Peserta Excel,Instansi X\n"
        );

        $response = $this->actingAs($admin)->post("/admin/events/{$event->id}/certificates-import", [
            'file' => $file,
            'category' => 'Workshop',
            'date' => '2026-03-15',
            'template' => $templateId,
        ]);

        $certificate = Certificate::where('nip', '199001012020121077')->first();
        $this->assertNotNull($certificate);
        $this->assertSame('Workshop', $certificate->category);
        $this->assertSame($templateId, $certificate->template);
        $this->assertSame('2026-03-15', $certificate->date);
        $this->assertStringContainsString('/Workshop/', $certificate->no_certificate);
        $this->assertSame('', $certificate->doc);
    }

    // --- CertificateGenerator: failure paths ---

    public function test_certificate_generator_throws_when_the_template_record_is_missing()
    {
        $certificate = $this->makeCertificate(['template' => (string) Str::uuid()]);

        $this->expectException(\RuntimeException::class);

        app(CertificateGenerator::class)->generate($certificate);
    }

    public function test_certificate_generator_throws_when_the_template_file_is_missing_from_disk()
    {
        Storage::fake('local');
        $template = TemplateCertificate::create([
            'title' => 'Template Tanpa File',
            'image' => 'template/nonexistent.pdf',
            'status' => '1',
        ]);
        $certificate = $this->makeCertificate(['template' => $template->id]);

        $this->expectException(\RuntimeException::class);

        app(CertificateGenerator::class)->generate($certificate);
    }
}
