<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfileDataMain extends Model
{
    use HasFactory;

    protected $fillable = [
            'nip',
            'name',
            'fname',
            'lname',
            'email',
            'place',
            'dob',
            'docid',
            'nodocid',
            'contact',
            'gender',
            'address',
            'province',
            'regency',
            'district',
            'villages',
            'tmt-cpns',
            'tmt-pns',
            'leveledu',
            'lastedu',
            'nomember',
            'statusmember',
            'image',
            'active_at',
            'religion',
    ];

    public function position()
    {
        return $this->hasOne(ProfileDataPosition::class, 'main_id', 'id');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'nip', 'nip');
    }

}
