<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberNotification extends Model
{
    protected $fillable = [
        'type',
        'title',
        'body',
        'link',
        'notifiable_type',
        'notifiable_id',
    ];

    public function notifiable()
    {
        return $this->morphTo();
    }

    public function reads()
    {
        return $this->hasMany(MemberNotificationRead::class);
    }

    /**
     * Broadcast a new notification to every member. One row for everyone -
     * read state per member is tracked separately (see MemberNotificationRead)
     * so this stays a single insert regardless of member count.
     */
    public static function broadcast(string $type, string $title, ?string $body, string $link, ?Model $notifiable = null): self
    {
        return static::create([
            'type' => $type,
            'title' => $title,
            'body' => $body,
            'link' => $link,
            'notifiable_type' => $notifiable ? $notifiable->getMorphClass() : null,
            'notifiable_id' => $notifiable?->getKey(),
        ]);
    }

    public function scopeUnreadFor($query, int $memberId)
    {
        return $query->whereDoesntHave('reads', function ($reads) use ($memberId) {
            $reads->where('member_id', $memberId);
        });
    }
}
