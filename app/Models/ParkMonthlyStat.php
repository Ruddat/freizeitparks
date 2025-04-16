<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ParkMonthlyStat extends Model
{
    protected $fillable = ['park_id', 'year', 'month', 'avg_crowd_level'];

    public function park()
    {
        return $this->belongsTo(Park::class);
    }
}
