<?php

namespace Tests\Feature\Mail;

use App\Mail\Concerns\LogsEmailStatus;
use App\Models\EmailLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LogsEmailStatusTest extends TestCase
{
    use RefreshDatabase;

    private function makeTrackedMailable(): Mailable
    {
        return new class extends Mailable {
            use LogsEmailStatus;

            public function build()
            {
                return $this->subject('Test')->html('<p>Test</p>');
            }
        };
    }

    public function test_sending_a_mailable_marks_its_email_log_as_sent()
    {
        config(['mail.default' => 'array']);
        $log = EmailLog::start('App\\Mail\\Dummy', 'test_type', 'someone@example.com');

        Mail::to('someone@example.com')->send($this->makeTrackedMailable()->withEmailLog($log->id));

        $log->refresh();
        $this->assertSame(EmailLog::STATUS_SENT, $log->status);
        $this->assertNotNull($log->sent_at);
    }

    public function test_a_failed_mailable_marks_its_email_log_as_failed_with_the_error_message()
    {
        $log = EmailLog::start('App\\Mail\\Dummy', 'test_type', 'someone@example.com');
        $mailable = $this->makeTrackedMailable()->withEmailLog($log->id);

        $mailable->failed(new \Exception('SMTP connection refused'));

        $log->refresh();
        $this->assertSame(EmailLog::STATUS_FAILED, $log->status);
        $this->assertNotNull($log->failed_at);
        $this->assertSame('SMTP connection refused', $log->error);
    }

    public function test_a_mailable_with_no_email_log_id_does_not_error_on_send_or_failure()
    {
        config(['mail.default' => 'array']);

        Mail::to('someone@example.com')->send($this->makeTrackedMailable());
        $this->makeTrackedMailable()->failed(new \Exception('boom'));

        $this->assertDatabaseCount('email_logs', 0);
    }
}
