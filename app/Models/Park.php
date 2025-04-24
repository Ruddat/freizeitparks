<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use App\Models\ParkQueueTime;
use App\Models\ParkOpeningHour;
use Carbon\Carbon;

class Park extends Model
{
    protected $fillable = [
        'external_id',
        'queue_times_id',
        'group_id',
        'name',
        'group_name',
        'location',
        'street',
        'zip',
        'city',
        'slug',
        'country',
        'continent',
        'timezone',
        'status',
        'image',
        'latitude',
        'longitude',
        'url',
        'video_embed_code',
        'video_url',
        'logo',
        'description',
        'opening_hours',
        'type',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    // ================= Relationships =================

    public function queueTimes()
    {
        return $this->hasMany(ParkQueueTime::class);
    }

    public function openingHours()
    {
        return $this->hasMany(ParkOpeningHour::class);
    }

    // ================= Slug Boot =================

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($park) {
            if (empty($park->slug) && !empty($park->name)) {
                $baseSlug = Str::slug($park->name);
                $slug = $baseSlug;
                $counter = 1;

                while (Park::where('slug', $slug)->where('id', '!=', $park->id)->exists()) {
                    $slug = $baseSlug . '-' . $counter++;
                }

                $park->slug = $slug;
            }
        });
    }

    // ================= Öffnungszeiten: Heute =================

    public function getOpeningHoursTodayAttribute()
    {
        $timezone = in_array($this->timezone, \DateTimeZone::listIdentifiers())
            ? $this->timezone
            : config('app.timezone');

        $heute = Carbon::now($timezone)->toDateString();

        $oeffnung = $this->openingHours()
            ->where('date', $heute)
            ->first();

        \Log::debug('Öffnungszeiten geprüft', [
            'park' => $this->name,
            'timezone' => $timezone,
            'date' => $heute,
            'open' => $oeffnung->open ?? null,
            'close' => $oeffnung->close ?? null,
        ]);

        return $oeffnung;
    }

    // ================= Optional: Dynamisch berechneter Status =================

    public function getOpeningStatusAttribute(): string
    {
        $oeffnung = $this->openingHoursToday;

        if (!$oeffnung || !$oeffnung->open || !$oeffnung->close) {
            return 'unknown';
        }

        // Aktuelle Uhrzeit im Park (Zeitzone)
        $now = \Carbon\Carbon::now($this->timezone ?? config('app.timezone'));
        $start = \Carbon\Carbon::createFromFormat('H:i:s', $oeffnung->open, $this->timezone)->setDateFrom($now);
        $end = \Carbon\Carbon::createFromFormat('H:i:s', $oeffnung->close, $this->timezone)->setDateFrom($now);

        if ($now->between($start, $end)) {
            return 'open';
        }

        return 'closed';
    }

    public function getStatusLabelAttribute()
    {
        return match ($this->opening_status ?? $this->status) {
            'open' => 'Geöffnet',
            'closed' => 'Geschlossen',
            default => 'Unbekannt',
        };
    }

    public function getStatusClassAttribute()
    {
        return match ($this->opening_status ?? $this->status) {
            'open' => 'text-green-500',
            'closed' => 'text-red-500',
            default => 'text-gray-500',
        };
    }

    public function getStatusIconAttribute()
    {
        return match ($this->opening_status ?? $this->status) {
            'open' => 'check-circle',
            'closed' => 'x-circle',
            default => 'question-mark-circle',
        };
    }

    // ================= Sonstiges =================

    public function getRouteKeyName()
    {
        return 'name';
    }
}
