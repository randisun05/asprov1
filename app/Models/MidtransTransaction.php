<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MidtransTransaction extends Model
{
    protected $fillable = [
        'registration_id',
        'order_id',
        'transaction_id',
        'gross_amount',
        'payment_type',
        'transaction_status',
        'fraud_status',
        'transaction_time',
        'raw_payload',
    ];

    protected $casts = [
        'transaction_time' => 'datetime',
        'raw_payload' => 'array',
        'gross_amount' => 'integer',
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}
