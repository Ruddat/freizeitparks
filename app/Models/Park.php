<?php

namespace App\Models;

use App\Models\ParkQueueTime;
use Illuminate\Database\Eloquent\Model;

class Park extends Model
{
    protected $fillable = [
        'name',
        'location',
        'latitude',
        'longitude',
        'status',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    public function getRouteKeyName()
    {
        return 'name';
    }
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'open' => 'Geöffnet',
            'closed' => 'Geschlossen',
            default => 'Unbekannt',
        };
    }
    public function getStatusClassAttribute()
    {
        return match ($this->status) {
            'open' => 'text-green-500',
            'closed' => 'text-red-500',
            default => 'text-gray-500',
        };
    }
    public function getStatusIconAttribute()
    {
        return match ($this->status) {
            'open' => 'check-circle',
            'closed' => 'x-circle',
            default => 'question-mark-circle',
        };
    }


    public function queueTimes()
    {
        return $this->hasMany(ParkQueueTime::class);
    }
}
