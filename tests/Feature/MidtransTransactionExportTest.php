<?php

namespace Tests\Feature;

use App\Exports\MidtransTransactionExport;
use App\Models\MidtransTransaction;
use App\Models\Registration;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MidtransTransactionExportTest extends TestCase
{
    use RefreshDatabase;

    private function makeTransaction(string $name, string $agency): MidtransTransaction
    {
        $registration = Registration::create([
            'nip' => '199001012020121099',
            'name' => $name,
            'email' => 'formula@example.com',
            'contact' => '081234567899',
            'agency' => $agency,
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
            'status' => 'confirm',
        ]);

        return MidtransTransaction::create([
            'registration_id' => $registration->id,
            'order_id' => $registration->id,
            'gross_amount' => 150000,
            'transaction_status' => 'settlement',
        ]);
    }

    /**
     * The public registration form lets an anonymous submitter put anything
     * in `name`/`agency` (validated only as `required|string`), and this
     * export is the only place those values reach a spreadsheet an admin
     * opens - so a value starting with a formula-trigger character must be
     * neutralized instead of exported as a live formula.
     */
    public function test_a_formula_like_name_is_neutralized_in_the_export()
    {
        $transaction = $this->makeTransaction(
            '=HYPERLINK("http://evil.example/steal","klik")',
            '+cmd|\'/c calc\'!A0'
        );

        $row = (new MidtransTransactionExport(collect([$transaction])))->map($transaction);

        $this->assertStringStartsWith("'=", $row[0]);
        $this->assertStringStartsWith("'+", $row[2]);
    }

    public function test_an_ordinary_name_is_left_untouched()
    {
        $transaction = $this->makeTransaction('Budi Santoso', 'Kementerian Contoh');

        $row = (new MidtransTransactionExport(collect([$transaction])))->map($transaction);

        $this->assertSame('Budi Santoso', $row[0]);
        $this->assertSame('Kementerian Contoh', $row[2]);
    }
}
