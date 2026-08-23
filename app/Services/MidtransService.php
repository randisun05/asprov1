<?php

namespace App\Services;

use App\Models\MidtransTransaction;
use App\Models\Registration;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MidtransService
{
    private function baseUrl(): string
    {
        return config('midtrans.is_production')
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    /**
     * Membership fees differ per position; anything outside the two
     * official positions falls back to the "lainnya" (anggota luar biasa) rate.
     */
    private function feeFor(string $position): int
    {
        $fees = config('midtrans.registration_fees', []);

        return (int) ($fees[$position] ?? $fees['lainnya'] ?? 0);
    }

    /**
     * Create a Snap payment token for a registration and return it along
     * with the redirect URL. Throws if Midtrans isn't configured yet or
     * the API call fails.
     */
    public function createSnapToken(Registration $registration): array
    {
        $serverKey = config('midtrans.server_key');

        if (!$serverKey) {
            throw new RuntimeException('MIDTRANS_SERVER_KEY belum diatur.');
        }

        $grossAmount = $this->feeFor($registration->position);

        if ($grossAmount <= 0) {
            throw new RuntimeException('Biaya keanggotaan untuk jabatan ini belum diatur.');
        }

        $response = Http::withBasicAuth($serverKey, '')
            ->acceptJson()
            ->post($this->baseUrl(), [
                'transaction_details' => [
                    'order_id' => $registration->id,
                    'gross_amount' => $grossAmount,
                ],
                'customer_details' => [
                    'first_name' => $registration->name,
                    'email' => $registration->email,
                    'phone' => $registration->contact,
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('Gagal membuat transaksi Midtrans: ' . $response->body());
        }

        // Freeze the gross amount actually charged at the moment the Snap
        // token was issued, so the transparency report below stays accurate
        // even if the position's fee in config changes later. firstOrCreate
        // (not updateOrCreate) so a repeat visit to the payment page before
        // completion never clobbers a record the webhook already settled.
        MidtransTransaction::firstOrCreate(
            ['order_id' => (string) $registration->id],
            [
                'registration_id' => $registration->id,
                'gross_amount' => $grossAmount,
                'transaction_status' => 'pending',
            ]
        );

        return [
            'token' => $response->json('token'),
            'redirect_url' => $response->json('redirect_url'),
        ];
    }

    /**
     * Verify the signature Midtrans sends with a payment notification.
     * See: https://docs.midtrans.com/docs/https-notification-webhooks
     */
    public function verifySignature(array $payload): bool
    {
        $serverKey = config('midtrans.server_key');

        $expected = hash('sha512',
            ($payload['order_id'] ?? '') .
            ($payload['status_code'] ?? '') .
            ($payload['gross_amount'] ?? '') .
            $serverKey
        );

        return hash_equals($expected, $payload['signature_key'] ?? '');
    }
}
