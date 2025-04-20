<?php

namespace App\Models;

use App\Models\Park;
use Illuminate\Database\Eloquent\Model;

class ParkCrowdReport extends Model
{

    protected $fillable = [
        'park_id',
        'crowd_level',
        'comment',
        'country',
        'city',
        'latitude',
        'longitude',
        'theming',
        'cleanliness',
        'gastronomy',
        'service',
        'attractiveness',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function park()
    {
        return $this->belongsTo(Park::class);
    }
    public function getCrowdLevelTextAttribute()
    {
        return match ($this->crowd_level) {
            1 => 'Sehr wenig los',
            2 => 'Wenig los',
            3 => 'Normal',
            4 => 'Viel los',
            5 => 'Sehr viel los',
            default => 'Unbekannt',
        };
    }
    public function getCrowdLevelClassAttribute()
    {
        return match ($this->crowd_level) {
            1 => 'bg-green-500',
            2 => 'bg-yellow-500',
            3 => 'bg-orange-500',
            4 => 'bg-red-500',
            5 => 'bg-purple-500',
            default => 'bg-gray-500',
        };
    }
    public function getCrowdLevelIconAttribute()
    {
        return match ($this->crowd_level) {
            1 => 'fa-solid fa-smile-beam',
            2 => 'fa-solid fa-smile',
            3 => 'fa-solid fa-meh',
            4 => 'fa-solid fa-frown',
            5 => 'fa-solid fa-sad-tear',
            default => 'fa-solid fa-question',
        };
    }
    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->format('d.m.Y H:i');
    }

}
