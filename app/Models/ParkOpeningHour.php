<?php

namespace App\Models;

use App\Models\Park;
use Illuminate\Database\Eloquent\Model;

class ParkOpeningHour extends Model
{
    protected $fillable = [
        'park_id',
        'day',
        'open',
        'close',
    ];

    // Entferne den Cast, da wir die Uhrzeiten manuell formatieren
    protected $casts = [];

    public function park()
    {
        return $this->belongsTo(Park::class);
    }

    // Hilfsmethode, um die Uhrzeit zu formatieren
    public function getFormattedOpenAttribute()
    {
        return $this->open ? substr($this->open, 0, 5) : null;
    }

    public function getFormattedCloseAttribute()
    {
        return $this->close ? substr($this->close, 0, 5) : null;
    }
}
