<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkDailyRideStats extends Model
{

    protected $fillable = [
        'park_id',
        'ride_id',
        'ride_name',
        'date',
        'avg_wait_time',
    ];

    public $timestamps = true;

    protected $casts = [
        'date' => 'date',
        'avg_wait_time' => 'float',
    ];

    public function park()
    {
        return $this->belongsTo(Park::class);
    }
}
