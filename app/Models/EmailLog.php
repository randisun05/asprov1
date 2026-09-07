<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailLog extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'mailable',
        'type',
        'to_email',
        'status',
        'error',
        'notifiable_type',
        'notifiable_id',
        'sent_at',
        'failed_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Creates the "pending" row before the mail is dispatched to the queue -
     * this is what lets an email that never gets processed (worker down,
     * stuck job) show up as stuck in "pending" instead of just not existing.
     */
    public static function start(string $mailable, string $type, string $toEmail, ?Model $notifiable = null): self
    {
        return static::create([
            'mailable' => $mailable,
            'type' => $type,
            'to_email' => $toEmail,
            'status' => self::STATUS_PENDING,
            'notifiable_type' => $notifiable?->getMorphClass(),
            'notifiable_id' => $notifiable?->getKey(),
        ]);
    }

    public function markSent(): void
    {
        $this->update(['status' => self::STATUS_SENT, 'sent_at' => now()]);
    }

    public function markFailed(string $error): void
    {
        $this->update(['status' => self::STATUS_FAILED, 'failed_at' => now(), 'error' => $error]);
    }
}
