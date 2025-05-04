<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParkQueueTimeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'park_id',
        'ride_id',
        'ride_name',
        'land_name',
        'wait_time',
        'is_open',
        'fetched_at',
    ];

    public $timestamps = true;
}
