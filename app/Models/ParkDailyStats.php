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
        'avg_precip_prob',
        'precipitation_sum',
        'sunshine_duration',
        'wind_speed_max',
        'wind_gusts_max',
        'uv_index_max',
        'crowd_sample_count',
        'avg_wait_time',
        'max_wait_time',
        'rides_open_ratio',
    ];
}
