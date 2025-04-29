<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicHolidays extends Model
{

    protected $fillable = [
        'date',
        'local_name',
        'name',
        'country_code',
        'global',
        'fixed',
    ];

    protected $casts = [
        'date' => 'date',
        'global' => 'boolean',
        'fixed' => 'boolean',
    ];

    public $timestamps = false;

    // Define any relationships if needed
    // For example, if you have a relationship with a country model
}
