<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Park;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ParkController extends Controller
{
    public function show($id)
    {
        $park = Park::with('queueTimes')->findOrFail($id);

        // Aktualisieren falls nötig
        $letzterEintrag = $park->queueTimes()->orderByDesc('fetched_at')->first();
        if (!$letzterEintrag || $letzterEintrag->fetched_at->lt(now()->subMinutes(10))) {
            $this->updateQueueTimesFor($park);
            $park->load('queueTimes');
        }

        // Nearby Parks (innerhalb 300km, außer sich selbst)
        $nearbyParks = Park::select('*')
            ->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance', [
                $park->latitude,
                $park->longitude,
                $park->latitude,
            ])
            ->where('id', '!=', $park->id)
            ->having('distance', '<=', 300)
            ->orderBy('distance')
            ->limit(12)
            ->get();

        return view('park_details_1', compact('park', 'nearbyParks'));
    }


    protected function updateQueueTimesFor(Park $park): void
    {

        if (!$park->queue_times_id) return;

        $url = "https://queue-times.com/parks/{$park->queue_times_id}/queue_times.json";
        $response = Http::timeout(10)->get($url);
//dd($response->json());

        if (!$response->successful()) return;

        $data = $response->json();
        $now = now();

        // Falls rides direkt auf oberster Ebene vorhanden sind
        foreach ($data['rides'] ?? [] as $ride) {
            $park->queueTimes()->updateOrCreate(
                ['ride_id' => $ride['id']],
                [
                    'ride_name'    => $ride['name'],
                    'is_open'      => $ride['is_open'],
                    'wait_time'    => $ride['wait_time'],
                    'last_updated' => Carbon::parse($ride['last_updated']),
                    'land_name'    => null,
                    'fetched_at'   => $now,
                ]
            );
        }

        // Wenn strukturierte "lands" vorhanden sind
        foreach ($data['lands'] ?? [] as $land) {
            foreach ($land['rides'] ?? [] as $ride) {
                $park->queueTimes()->updateOrCreate(
                    ['ride_id' => $ride['id']],
                    [
                        'ride_name'    => $ride['name'],
                        'is_open'      => $ride['is_open'],
                        'wait_time'    => $ride['wait_time'],
                        'last_updated' => Carbon::parse($ride['last_updated']),
                        'land_name'    => $land['name'],
                        'fetched_at'   => $now,
                    ]
                );
            }
        }
    }


}
