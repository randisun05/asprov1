<?php

namespace App\Mail\Concerns;

use App\Models\EmailLog;
use Throwable;

/**
 * Wires a Mailable's actual send/fail outcome back to its EmailLog row.
 *
 * send()/failed() here are called by Illuminate\Mail\SendQueuedMailable
 * (the job Laravel wraps every ShouldQueue mailable in) regardless of
 * whether QUEUE_CONNECTION is "sync" (runs inline, same request) or a real
 * queue like "database" (runs later, in a worker) - so this same code path
 * keeps the log accurate either way without the call sites needing to know
 * which mode is active.
 */
trait LogsEmailStatus
{
    public ?int $emailLogId = null;

    public function withEmailLog(?int $emailLogId): static
    {
        $this->emailLogId = $emailLogId;

        return $this;
    }

    public function send($mailer)
    {
        $sent = parent::send($mailer);

        $this->emailLog()?->markSent();

        return $sent;
    }

    public function failed(Throwable $exception): void
    {
        $this->emailLog()?->markFailed($exception->getMessage());
    }

    private function emailLog(): ?EmailLog
    {
        return $this->emailLogId ? EmailLog::find($this->emailLogId) : null;
    }
}
