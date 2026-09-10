<?php

namespace Tests\Feature\Public;

use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PublicRegistrationTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'nip' => '199001012020121234',
            'name' => 'Pendaftar Baru',
            'email' => 'pendaftarbaru@example.com',
            'contact' => '081234567800',
            'agency' => 'Kementerian Contoh',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
            'document_jab' => UploadedFile::fake()->create('sk.pdf', 500, 'application/pdf'),
            'code' => 'ABCD',
            'captcha' => 'ABCD',
            'term' => 1,
        ], $overrides);
    }

    public function test_a_new_applicant_can_register()
    {
        Storage::fake('local');

        $response = $this->post('/registration/store', $this->payload());

        $response->assertRedirect(route('registration.success'));
        $this->assertDatabaseHas('registrations', [
            'nip' => '199001012020121234',
            'email' => 'pendaftarbaru@example.com',
        ]);
    }

    public function test_registering_with_a_nip_email_or_contact_already_in_a_non_rejected_registration_is_blocked()
    {
        Storage::fake('local');
        Registration::create([
            'nip' => '199001012020121234',
            'name' => 'Sudah Daftar',
            'email' => 'sudahdaftar@example.com',
            'contact' => '081234567801',
            'agency' => 'Instansi Lain',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
            'status' => 'submission',
        ]);

        $response = $this->post('/registration/store', $this->payload());

        $response->assertSessionHasErrors('nip');
        $this->assertDatabaseCount('registrations', 1);
    }

    /**
     * The core bug: a rejected applicant resubmitting with the *same*
     * email/phone (the realistic case, since it's their own contact info)
     * used to be blocked by the email/contact uniqueness rules even though
     * rejection is supposed to allow resubmission - only nip was ever freed
     * up (and only by permanently corrupting it), while other fields
     * weren't excluded from the check.
     */
    public function test_a_rejected_applicant_can_resubmit_with_the_same_nip_email_and_contact()
    {
        Storage::fake('local');
        $rejected = Registration::create([
            'nip' => '199001012020121234',
            'name' => 'Pendaftar Baru',
            'email' => 'pendaftarbaru@example.com',
            'contact' => '081234567800',
            'agency' => 'Instansi Lama',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
            'status' => 'rejected',
        ]);

        $response = $this->post('/registration/store', $this->payload());

        $response->assertSessionDoesntHaveErrors();
        $response->assertRedirect(route('registration.success'));
        $this->assertDatabaseCount('registrations', 1);

        $rejected->refresh();
        $this->assertSame('199001012020121234', $rejected->nip);
        $this->assertSame('submission', $rejected->status);
    }

    public function test_a_rejected_applicants_nip_email_and_contact_remain_available_to_other_new_applicants_once_they_change_theirs()
    {
        Storage::fake('local');
        Registration::create([
            'nip' => '199001012020121234',
            'name' => 'Pendaftar Lama',
            'email' => 'pendaftarbaru@example.com',
            'contact' => '081234567800',
            'agency' => 'Instansi Lama',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
            'status' => 'rejected',
        ]);

        // A different applicant (different nip/email/contact) registering
        // at the same time must still work normally.
        $response = $this->post('/registration/store', $this->payload([
            'nip' => '199001019999999999',
            'email' => 'lainnya@example.com',
            'contact' => '081234567999',
        ]));

        $response->assertRedirect(route('registration.success'));
        $this->assertDatabaseCount('registrations', 2);
    }

    public function test_registration_confirm_update_rejects_an_invalid_document_file_type()
    {
        Storage::fake('local');
        $registration = Registration::create([
            'nip' => '199001012020121235',
            'name' => 'Anggota Confirm',
            'email' => 'confirm@example.com',
            'contact' => '081234567802',
            'agency' => 'Kementerian Contoh',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
            'status' => 'confirm',
        ]);

        $response = $this->post("/registration/confirm/{$registration->id}", [
            'nip' => $registration->nip,
            'name' => $registration->name,
            'email' => $registration->email,
            'contact' => $registration->contact,
            'agency' => $registration->agency,
            'position' => $registration->position,
            'level' => $registration->level,
            'document_jab' => UploadedFile::fake()->create('malware.exe', 100),
        ]);

        $response->assertSessionHasErrors('document_jab');
    }

    public function test_registration_confirm_update_without_a_new_document_keeps_the_existing_one()
    {
        Storage::fake('local');
        $registration = Registration::create([
            'nip' => '199001012020121236',
            'name' => 'Anggota Confirm',
            'email' => 'confirm2@example.com',
            'contact' => '081234567803',
            'agency' => 'Kementerian Contoh',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
            'status' => 'confirm',
            'document_jab' => 'documents/existing.pdf',
        ]);

        $this->post("/registration/confirm/{$registration->id}", [
            'nip' => $registration->nip,
            'name' => 'Nama Diperbaiki',
            'email' => $registration->email,
            'contact' => $registration->contact,
            'agency' => $registration->agency,
            'position' => $registration->position,
            'level' => $registration->level,
        ]);

        $registration->refresh();
        $this->assertSame('Nama Diperbaiki', $registration->name);
        $this->assertSame('documents/existing.pdf', $registration->document_jab);
    }

    public function test_paid_rejects_an_invalid_proof_of_payment_file_type()
    {
        Storage::fake('local');
        $registration = Registration::create([
            'nip' => '199001012020121237',
            'name' => 'Anggota Bayar',
            'email' => 'bayar@example.com',
            'contact' => '081234567804',
            'agency' => 'Kementerian Contoh',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
            'status' => 'submission',
        ]);

        $response = $this->post("/registration/paid/{$registration->id}", [
            'paid' => UploadedFile::fake()->create('bukti.exe', 100),
        ]);

        $response->assertSessionHasErrors('paid');
    }

    public function test_group_registration_rejects_a_non_numeric_total()
    {
        Storage::fake('local');

        $response = $this->post('/registration/group', [
            'agency' => 'Kementerian Contoh',
            'name' => 'PIC Instansi',
            'email' => 'pic@example.com',
            'contact' => '081234567805',
            'total' => 'banyak',
            'file' => UploadedFile::fake()->create('data.xlsx', 500),
            'code' => 'ABCD',
            'captcha' => 'ABCD',
            'term' => 1,
        ]);

        $response->assertSessionHasErrors('total');
    }
}
