<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkWeather extends Model
{

    protected $table = 'park_weather';

    protected $fillable = [
        'park_id',
        'date',
        'temp_day',
        'temp_night',
        'weather_code',
        'description',
        'icon',
        'fetched_at',
        'wind_speed',
        'uv_index',
        'rain_chance',

    ];

    public function park()
    {
        return $this->belongsTo(Park::class);
    }


}
