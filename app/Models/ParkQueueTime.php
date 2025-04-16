<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkQueueTime extends Model
{
    protected $fillable = ['park_id', 'ride_id', 'ride_name', 'wait_time', 'fetched_at', 'land_name', 'is_open', 'last_updated'];

    public function park()
    {
        return $this->belongsTo(Park::class);
    }

    protected $casts = [
        'fetched_at' => 'datetime',
        'last_updated' => 'datetime',
    ];

}
