<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Member extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'nip',
        'name',
        'email',
        'password',
        'nomember',
        'agency',
        'code-password',
        'qr_link'
    ];

    protected $hidden = [
        'email',
        'password',
    ];

     public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function profileMain()
    {
        // Parameter: ModelTarget, FK_di_target, PK_di_lokal
        return $this->hasOne(ProfileDataMain::class, 'nip', 'nip');
    }

    public function pointTransactions()
    {
        return $this->hasMany(PointTransaction::class)->latest();
    }

}
