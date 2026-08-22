<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'body',
        'date',
        'enddate',
        'place',
        'link',
        'participant',
        'image',
        'status',
        'file',
        'absen',
        'category',
        'template_id',
        'random_question',
        'random_answer',
        'show_answer',
        'start_at',
        'end_at',
        'duration',
    ];

    public function getRouteKeyName()
    {
        return 'slug'; // Menggunakan kolom 'slug' sebagai kunci rute
    }

    public function resolveRouteBinding($value, $field = null)
    {
        return $this->where('slug', $value)->firstOrFail(); // Mencari model berdasarkan 'slug'
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'event_question')->withTimestamps();
    }
    public function detailEvents()
    {
        return $this->hasMany(DetailEvent::class);
    }

    public function eventPoint()
    {
        return $this->hasOne(EventPoint::class);
    }

    public function getPointCostAttribute(): int
    {
        return $this->eventPoint?->point_cost ?? 0;
    }

}
