<?php

namespace Tests\Feature;

use App\Jobs\SendWhatsappMessage;
use App\Models\WhatsappLog;
use App\Models\WhatsappSetting;
use App\Services\WhatsappNotifier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class WhatsappNotifierTest extends TestCase
{
    use RefreshDatabase;

    public function test_nothing_is_queued_or_logged_when_the_feature_is_disabled()
    {
        Bus::fake();
        WhatsappSetting::current()->update(['enabled' => false, 'api_token' => 'token-123']);

        WhatsappNotifier::send('registration_approved', '081234567890', 'Halo');

        Bus::assertNothingDispatched();
        $this->assertDatabaseCount('whatsapp_logs', 0);
    }

    public function test_nothing_is_queued_or_logged_when_no_token_is_configured()
    {
        Bus::fake();
        WhatsappSetting::current()->update(['enabled' => true, 'api_token' => null]);

        WhatsappNotifier::send('registration_approved', '081234567890', 'Halo');

        Bus::assertNothingDispatched();
        $this->assertDatabaseCount('whatsapp_logs', 0);
    }

    public function test_nothing_is_queued_or_logged_when_no_phone_number_is_available()
    {
        Bus::fake();
        WhatsappSetting::current()->update(['enabled' => true, 'api_token' => 'token-123']);

        WhatsappNotifier::send('registration_approved', null, 'Halo');

        Bus::assertNothingDispatched();
        $this->assertDatabaseCount('whatsapp_logs', 0);
    }

    public function test_a_pending_log_is_created_and_the_job_is_queued_when_enabled_with_a_token_and_phone()
    {
        Bus::fake();
        WhatsappSetting::current()->update(['enabled' => true, 'api_token' => 'token-123']);

        WhatsappNotifier::send('registration_approved', '081234567890', 'Halo dunia');

        $this->assertDatabaseHas('whatsapp_logs', [
            'type' => 'registration_approved',
            'to_phone' => '081234567890',
            'status' => WhatsappLog::STATUS_PENDING,
        ]);
        Bus::assertDispatched(SendWhatsappMessage::class, function ($job) {
            return $job->phone === '081234567890' && $job->message === 'Halo dunia';
        });
    }

    public function test_job_marks_the_log_as_sent_on_a_successful_fonnte_response()
    {
        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => true, 'detail' => 'success! message in queue'], 200),
        ]);
        WhatsappSetting::current()->update(['enabled' => true, 'api_token' => 'token-123']);
        $log = WhatsappLog::start('registration_approved', '081234567890');

        (new SendWhatsappMessage($log->id, '081234567890', 'Halo dunia'))->handle();

        $log->refresh();
        $this->assertSame(WhatsappLog::STATUS_SENT, $log->status);
        $this->assertNotNull($log->sent_at);
        Http::assertSent(function ($request) {
            return $request->url() === 'https://api.fonnte.com/send'
                && $request['target'] === '081234567890'
                && $request->hasHeader('Authorization', 'token-123');
        });
    }

    public function test_job_marks_the_log_as_failed_on_an_unsuccessful_fonnte_response()
    {
        Http::fake([
            'api.fonnte.com/*' => Http::response(['status' => false, 'reason' => 'nomor tidak valid'], 200),
        ]);
        WhatsappSetting::current()->update(['enabled' => true, 'api_token' => 'token-123']);
        $log = WhatsappLog::start('registration_approved', '081234567890');

        (new SendWhatsappMessage($log->id, '081234567890', 'Halo dunia'))->handle();

        $log->refresh();
        $this->assertSame(WhatsappLog::STATUS_FAILED, $log->status);
        $this->assertSame('nomor tidak valid', $log->error);
    }

    public function test_job_marks_the_log_as_failed_when_the_http_call_throws()
    {
        Http::fake(function () {
            throw new \Illuminate\Http\Client\ConnectionException('Connection timed out');
        });
        WhatsappSetting::current()->update(['enabled' => true, 'api_token' => 'token-123']);
        $log = WhatsappLog::start('registration_approved', '081234567890');

        (new SendWhatsappMessage($log->id, '081234567890', 'Halo dunia'))->handle();

        $log->refresh();
        $this->assertSame(WhatsappLog::STATUS_FAILED, $log->status);
        $this->assertNotNull($log->error);
    }

    public function test_job_leaves_the_log_pending_if_the_feature_was_turned_off_before_it_ran()
    {
        Http::fake();
        $log = WhatsappLog::start('registration_approved', '081234567890');
        WhatsappSetting::current()->update(['enabled' => false]);

        (new SendWhatsappMessage($log->id, '081234567890', 'Halo dunia'))->handle();

        $log->refresh();
        $this->assertSame(WhatsappLog::STATUS_PENDING, $log->status);
        Http::assertNothingSent();
    }
}
