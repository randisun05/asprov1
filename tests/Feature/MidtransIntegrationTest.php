<?php

namespace Tests\Feature;

use App\Models\Registration;
use App\Services\MidtransService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class MidtransIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function makeRegistration(): Registration
    {
        return Registration::create([
            'nip' => '199001012020121001',
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'contact' => '081234567890',
            'agency' => 'Kementerian Contoh',
            'position' => 'Analis SDM Aparatur',
            'level' => 'Ahli Pertama',
            'status' => 'confirm',
        ]);
    }

    public function test_snap_token_creation_fails_without_server_key()
    {
        config(['midtrans.server_key' => null]);

        $this->expectException(RuntimeException::class);
        (new MidtransService())->createSnapToken($this->makeRegistration());
    }

    public function test_snap_token_creation_fails_without_a_fee_configured_for_the_position()
    {
        config(['midtrans.server_key' => 'test-server-key', 'midtrans.registration_fees' => []]);

        $this->expectException(RuntimeException::class);
        (new MidtransService())->createSnapToken($this->makeRegistration());
    }

    public function test_snap_token_creation_succeeds_when_configured()
    {
        config([
            'midtrans.server_key' => 'test-server-key',
            'midtrans.registration_fees' => ['Analis SDM Aparatur' => 150000],
        ]);
        Http::fake([
            'app.sandbox.midtrans.com/*' => Http::response([
                'token' => 'snap-token-123',
                'redirect_url' => 'https://sandbox.midtrans.com/pay/snap-token-123',
            ]),
        ]);

        $result = (new MidtransService())->createSnapToken($this->makeRegistration());

        $this->assertSame('snap-token-123', $result['token']);
    }

    public function test_snap_token_uses_the_lainnya_fee_for_an_unlisted_position()
    {
        config([
            'midtrans.server_key' => 'test-server-key',
            'midtrans.registration_fees' => ['lainnya' => 75000],
        ]);
        Http::fake([
            'app.sandbox.midtrans.com/*' => Http::response(['token' => 'snap-token-lb', 'redirect_url' => '']),
        ]);
        $registration = $this->makeRegistration();
        $registration->update(['position' => 'Dosen']);

        $result = (new MidtransService())->createSnapToken($registration->fresh());

        $this->assertSame('snap-token-lb', $result['token']);
        Http::assertSent(function ($request) {
            return $request['transaction_details']['gross_amount'] === 75000;
        });
    }

    public function test_payment_page_falls_back_gracefully_when_not_configured()
    {
        config(['midtrans.server_key' => null]);
        $registration = $this->makeRegistration();

        $response = $this->get("/registration/paid/{$registration->id}/payment");

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_webhook_marks_registration_as_paid_on_valid_settlement()
    {
        config(['midtrans.server_key' => 'test-server-key']);
        $registration = $this->makeRegistration();

        $payload = [
            'order_id' => $registration->id,
            'status_code' => '200',
            'gross_amount' => '150000.00',
            'transaction_status' => 'settlement',
        ];
        $payload['signature_key'] = hash('sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . 'test-server-key'
        );

        $response = $this->postJson('/midtrans/notification', $payload);

        $response->assertOk();
        $this->assertSame('paid', $registration->fresh()->status);
    }

    public function test_webhook_rejects_an_invalid_signature()
    {
        config(['midtrans.server_key' => 'test-server-key']);
        $registration = $this->makeRegistration();

        $response = $this->postJson('/midtrans/notification', [
            'order_id' => $registration->id,
            'status_code' => '200',
            'gross_amount' => '150000.00',
            'transaction_status' => 'settlement',
            'signature_key' => 'not-the-real-signature',
        ]);

        $response->assertStatus(403);
        $this->assertSame('confirm', $registration->fresh()->status);
    }

    public function test_webhook_marks_registration_as_rejected_on_expire()
    {
        config(['midtrans.server_key' => 'test-server-key']);
        $registration = $this->makeRegistration();

        $payload = [
            'order_id' => $registration->id,
            'status_code' => '200',
            'gross_amount' => '150000.00',
            'transaction_status' => 'expire',
        ];
        $payload['signature_key'] = hash('sha512',
            $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . 'test-server-key'
        );

        $this->postJson('/midtrans/notification', $payload)->assertOk();

        $this->assertSame('rejected', $registration->fresh()->status);
    }
}
