<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappLog extends Model
{
    const STATUS_PENDING = 'pending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'type',
        'to_phone',
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

    public static function start(string $type, string $toPhone, ?Model $notifiable = null): self
    {
        return static::create([
            'type' => $type,
            'to_phone' => $toPhone,
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
