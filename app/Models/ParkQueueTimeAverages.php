<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkQueueTimeAverages extends Model
{
    protected $fillable = [
        'park_id',
        'ride_id',
        'ride_name',
        'land_name',
        'average_wait_time',
        'fetch_count',
    ];
}
