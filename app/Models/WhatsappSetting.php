<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsappSetting extends Model
{
    protected $fillable = [
        'enabled',
        'api_token',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * Single-row settings, created on first access rather than seeded, so a
     * fresh install works without a dedicated seeder step.
     */
    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }

    public function isReady(): bool
    {
        return $this->enabled && filled($this->api_token);
    }
}
