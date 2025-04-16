<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkCrowdForecas extends Model
{
    protected $fillable = ['park_id', 'date', 'crowd_level', 'status', 'opening_hours'];

    public function park()
    {
        return $this->belongsTo(Park::class);
    }
}
