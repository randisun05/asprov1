<?php

namespace App\Services;

use App\Jobs\SendWhatsappMessage;
use App\Models\WhatsappLog;
use App\Models\WhatsappSetting;
use Illuminate\Database\Eloquent\Model;

/**
 * The single entry point every controller/command uses to fan an email
 * notification out to WhatsApp too. Disabled feature or a missing phone
 * number are both silently skipped (not failures) - no log row is created
 * and nothing is queued, so turning the feature off is a real kill switch
 * rather than something that keeps piling up "pending" rows nobody will
 * ever process.
 */
class WhatsappNotifier
{
    public static function send(string $type, ?string $phone, string $message, ?Model $notifiable = null): void
    {
        $phone = $phone ? trim($phone) : null;

        if (!$phone || !WhatsappSetting::current()->isReady()) {
            return;
        }

        $log = WhatsappLog::start($type, $phone, $notifiable);

        SendWhatsappMessage::dispatch($log->id, $phone, $message);
    }
}
