<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventPoint extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'point_cost',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
