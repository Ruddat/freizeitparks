<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\ParkQueueTime;
use App\Models\ParkOpeningHour;
use Illuminate\Database\Eloquent\Model;

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

    public function openingHours()
    {
        return $this->hasMany(ParkOpeningHour::class);
    }

    public function openingHoursToday()
    {
        return $this->hasOne(\App\Models\ParkOpeningHour::class)
            ->where('date', now()->toDateString());
    }


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

}
