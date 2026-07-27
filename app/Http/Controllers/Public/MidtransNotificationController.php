<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Services\MidtransService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class MidtransNotificationController extends Controller
{
    public function __invoke(Request $request, MidtransService $midtrans)
    {
        $payload = $request->all();

        if (!$midtrans->verifySignature($payload)) {
            return response()->json(['message' => 'Invalid signature'], Response::HTTP_FORBIDDEN);
        }

        $registration = Registration::find($payload['order_id'] ?? null);

        if (!$registration) {
            return response()->json(['message' => 'Registration not found'], Response::HTTP_NOT_FOUND);
        }

        $status = $payload['transaction_status'] ?? null;

        if (in_array($status, ['settlement', 'capture'], true)) {
            $registration->update(['status' => 'paid']);
        } elseif (in_array($status, ['deny', 'cancel', 'expire'], true)) {
            $registration->update(['status' => 'rejected']);
        }

        return response()->json(['message' => 'OK']);
    }
}
