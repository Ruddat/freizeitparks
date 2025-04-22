<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSignup extends Model
{
    protected $fillable = [
        'email', 'name', 'city', 'interests', 'confirmation_token', 'confirmed_at'
    ];

    protected $casts = [
        'interests' => 'array',
        'confirmed_at' => 'datetime',
    ];
}
