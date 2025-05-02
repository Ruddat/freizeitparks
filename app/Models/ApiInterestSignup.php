<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiInterestSignup extends Model
{

    protected $fillable = [
        'email',
        'park_name',
        'agreed_to_privacy',
    ];

    protected $casts = [
        'agreed_to_privacy' => 'boolean',
    ];

    public function getParkNameAttribute($value)
    {
        return $value ?: 'N/A';
    }
}
