<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkDailyStats extends Model
{
    protected $fillable = [
        'park_id',
        'date',
        'avg_temp_day',
        'avg_temp_night',
        'avg_crowd_level',
        'weather_code',
        'description',
    ];
}
