<?php

namespace Tests\Feature\Admin;

use App\Models\MidtransTransaction;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransReportTest extends TestCase
{
    use RefreshDatabase;
    use CreatesAdminUsers;

    private function makeTransaction(array $overrides = []): MidtransTransaction
    {
        static $counter = 0;
        $counter++;

        $registration = Registration::create([
            'nip' => '19900101202012' . str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'name' => 'Anggota Midtrans ' . $counter,
            'email' => "midtrans{$counter}@example.com",
            'contact' => '08123456' . str_pad((string) $counter, 4, '0', STR_PAD_LEFT),
            'agency' => 'Instansi Uji',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
            'status' => 'confirm',
        ]);

        return MidtransTransaction::create(array_merge([
            'registration_id' => $registration->id,
            'order_id' => $registration->id,
            'gross_amount' => 150000,
            'transaction_status' => 'pending',
        ], $overrides));
    }

    public function test_administrator_can_view_the_midtrans_report()
    {
        $admin = $this->makeAdminUser('administrator');
        $this->makeTransaction(['transaction_status' => 'settlement', 'gross_amount' => 150000]);
        $this->makeTransaction(['transaction_status' => 'pending', 'gross_amount' => 150000]);
        $this->makeTransaction(['transaction_status' => 'expire', 'gross_amount' => 150000]);

        $response = $this->actingAs($admin)->get('/admin/midtrans-report');

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Registration/MidtransReport')
            ->where('summary.balance', 150000)
            ->where('summary.settled_count', 1)
            ->where('summary.pending_count', 1)
            ->where('summary.failed_count', 1)
        );
    }

    public function test_pendanaan_role_can_view_the_midtrans_report()
    {
        $pendanaan = $this->makeAdminUser('pendanaan');

        $this->actingAs($pendanaan)->get('/admin/midtrans-report')->assertOk();
    }

    public function test_roles_outside_administrator_and_pendanaan_are_forbidden()
    {
        $keanggotaan = $this->makeAdminUser('keanggotaan');

        $this->actingAs($keanggotaan)->get('/admin/midtrans-report')->assertForbidden();
    }

    public function test_export_downloads_an_xlsx_file()
    {
        $admin = $this->makeAdminUser('administrator');
        $this->makeTransaction();

        $response = $this->actingAs($admin)->get('/admin/midtrans-report/export');

        $response->assertOk();
        $response->assertHeader(
            'content-type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
    }
}
