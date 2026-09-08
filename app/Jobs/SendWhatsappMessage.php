<?php

namespace App\Jobs;

use App\Models\WhatsappLog;
use App\Models\WhatsappSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;

class SendWhatsappMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $whatsappLogId,
        public string $phone,
        public string $message,
    ) {
    }

    /**
     * Failures here are recorded on the WhatsappLog row directly rather
     * than left to bubble up and be retried by the queue - a bad number or
     * an expired token won't fix itself on retry, so one attempt is
     * enough and keeps this consistent with how email delivery is logged.
     */
    public function handle(): void
    {
        $log = WhatsappLog::find($this->whatsappLogId);

        if (!$log) {
            return;
        }

        $settings = WhatsappSetting::current();

        if (!$settings->isReady()) {
            // Turned off between dispatch and processing - leave it
            // pending rather than reporting a false failure.
            return;
        }

        try {
            $response = Http::withHeaders(['Authorization' => $settings->api_token])
                ->asForm()
                ->post('https://api.fonnte.com/send', [
                    'target' => $this->phone,
                    'message' => $this->message,
                    'countryCode' => '62',
                ]);

            if ($response->successful() && $response->json('status') === true) {
                $log->markSent();
            } else {
                $log->markFailed($response->json('reason') ?? $response->json('detail') ?? 'Gagal mengirim WhatsApp (respons tidak berhasil dari Fonnte).');
            }
        } catch (\Throwable $e) {
            $log->markFailed($e->getMessage());
        }
    }
}
