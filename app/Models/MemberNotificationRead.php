<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberNotificationRead extends Model
{
    protected $fillable = [
        'member_notification_id',
        'member_id',
        'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public $timestamps = false;

    public function notification()
    {
        return $this->belongsTo(MemberNotification::class, 'member_notification_id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
