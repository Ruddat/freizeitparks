<?php

namespace App\Services;

use Carbon\CarbonPeriod;
use App\Models\ParkOpeningHour;
use App\Livewire\Backend\Parks\OpeningTimesEditor;

class OpeningHoursService
{
    /**
     * Bulk update opening hours for multiple dates
     */
    public function bulkUpdateHours(int $parkId, array $dates, ?string $open, ?string $close): void
    {
        $records = array_map(function ($date) use ($parkId, $open, $close) {
            return [
                'park_id' => $parkId,
                'date' => $date,
                'open' => $open,
                'close' => $close,
                'day' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }, $dates);

        ParkOpeningHour::upsert(
            $records,
            ['park_id', 'date'],
            ['open', 'close', 'updated_at']
        );
    }

    /**
     * Update opening hours for a single date
     */
    public function updateSingleDate(int $parkId, string $date, ?string $open, ?string $close): void
    {
        ParkOpeningHour::updateOrCreate(
            ['park_id' => $parkId, 'date' => $date],
            ['open' => $open, 'close' => $close, 'day' => null]
        );
    }

    /**
     * Apply a preset to a date range
     */
    public function applyPresetToRange(int $parkId, string $start, string $end, ?string $open, ?string $close): void
    {
        $period = CarbonPeriod::create($start, $end);
        $dates = $period->map(fn($date) => $date->format('Y-m-d'))->toArray();

        $this->bulkUpdateHours($parkId, $dates, $open, $close);
    }

    /**
     * Get the preset color based on opening hours
     */
    public function getPresetColor(?string $open, ?string $close): string
    {
        if ($open === null || $close === null) {
            return OpeningTimesEditor::PRESET_CLOSED;
        }

        $openTime = substr($open, 0, 5);
        $closeTime = substr($close, 0, 5);

        if ($openTime === OpeningTimesEditor::SUMMER_TIME_OPEN) {
            if ($closeTime === OpeningTimesEditor::SUMMER_TIME_CLOSE) {
                return OpeningTimesEditor::PRESET_SUMMER;
            }
            if ($closeTime === OpeningTimesEditor::SHORT_TIME_CLOSE) {
                return OpeningTimesEditor::PRESET_SHORT;
            }
        }

        return OpeningTimesEditor::PRESET_SUMMER;
    }
}
