<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'name',
        'email',
        'contact',
        'agency',
        'position',
        'level',
        'document_jab',
        'status',
        'paid',
        'info',
        'emailstatus',
        'admin',
        'from',
    ];

    protected $keyType = 'string'; // Menetapkan tipe kunci ke string
    protected $primaryKey = 'id'; // Menetapkan nama primary key
    public $incrementing = false; // Menonaktifkan auto increment

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Menggunakan ULID saat membuat instance baru
            $model->id = (string) Str::orderedUuid();
        });
    }

    public function midtransTransaction()
    {
        return $this->hasOne(MidtransTransaction::class);
    }

}
