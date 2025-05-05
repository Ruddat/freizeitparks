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
        'temp_mean',
        'apparent_temp_max',
        'apparent_temp_min',
        'apparent_temp_mean',
        'precipitation',
        'rain_sum',
        'showers_sum',
        'snowfall_sum',
        'precipitation_hours',
        'precip_prob_mean',
        'precip_prob_min',
        'sunrise',
        'sunset',
        'sunshine_duration',
        'daylight_duration',
        'wind_gusts',
        'wind_direction',
        'radiation_sum',
        'evapotranspiration',
        'uv_index_clear_sky',


    ];

    public function park()
    {
        return $this->belongsTo(Park::class);
    }


}
