<?php

namespace App\Livewire\Frontend;

use Http;
use App\Models\Park;
use Livewire\Component;
use GeoIp2\Database\Reader;
use App\Models\ParkCrowdReport;
use Illuminate\Support\Facades\Cache;

class ParkAndrangComponent extends Component
{
    public Park $park;
    public bool $openRatingModal = false;

    public int $crowd_level = 3;
    public string $comment = '';
    public int $theming = 3;
    public int $cleanliness = 3;
    public int $gastronomy = 3;
    public int $service = 3;
    public int $attractiveness = 3;

    public bool $alreadyVoted = false;

    public function submit()
    {
        \Log::info('Submit-Methode gestartet');
        $ip = request()->ip();
        if (in_array($ip, ['127.0.0.1', '::1'])) {
            $ip = '8.8.8.8';
        }

        $key = 'crowd_report_' . $this->park->id . '_' . $ip;
        if (Cache::has($key)) {
            $this->dispatch('show-toast', type: 'error', message: 'Du hast heute bereits abgestimmt.');

            $this->alreadyVoted = true;
        } elseif (! $this->isCommentSafeWithAI($this->comment)) {
            session()->flash('error', 'Dein Kommentar enthält unangemessene Inhalte. Bitte überarbeite ihn.');
            $this->dispatch('show-toast', type: 'error', message: 'Dein Kommentar enthält unangemessene Inhalte. Bitte überarbeite ihn.');
        } else {
            $country = $city = null;
            $lat = $lon = null;
            try {
                $reader = new Reader(storage_path('app/geo/GeoLite2-City.mmdb'));
                $record = $reader->city($ip);
                $country = $record->country->name;
                $city    = $record->city->name;
                $lat     = $record->location->latitude;
                $lon     = $record->location->longitude;
            } catch (\Exception $e) {
                logger('Geo-Fehler: ' . $e->getMessage());
            }

            ParkCrowdReport::create([
                'park_id'        => $this->park->id,
                'crowd_level'    => $this->crowd_level,
                'comment'        => $this->comment,
                'country'        => $country,
                'city'           => $city,
                'latitude'       => $lat,
                'longitude'      => $lon,
                'theming'        => $this->theming,
                'cleanliness'    => $this->cleanliness,
                'gastronomy'     => $this->gastronomy,
                'service'        => $this->service,
                'attractiveness' => $this->attractiveness,
            ]);

            Cache::put($key, true, now()->addHours(24));
            $this->dispatch('show-toast', type: 'success', message: 'Danke für deine Bewertung! Dein Feedback wurde gespeichert.');
        }

        // Modal immer schließen
        $this->openRatingModal = false;

        // Form zurücksetzen
        $this->reset([
            'crowd_level',
            'comment',
            'theming',
            'cleanliness',
            'gastronomy',
            'service',
            'attractiveness',
            'alreadyVoted'
        ]);
    }

    public function testToast()
    {
        \Log::info('Test-Toast ausgelöst');
        session()->flash('success', 'Testnachricht');
        $this->dispatch('show-toast', type: 'success', message: 'Testnachricht');
    }

    protected function isCommentSafeWithAI(string $comment): bool
    {
        if (trim($comment) === '') {
            return true;
        }

        try {
            $prompt = "Analysiere diesen Kommentar auf Hass, Hetze oder unangemessene Sprache. Antworte mit 'OK' wenn alles in Ordnung ist, sonst mit 'BAD'. Kommentar: \"{$comment}\"";
            $response = Http::timeout(15)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . config('services.deepinfra.token'),
                    'Content-Type'  => 'application/json',
                ])->post('https://api.deepinfra.com/v1/inference/mistralai/Mixtral-8x7B-Instruct-v0.1', [
                    'input'            => "[INST] {$prompt} [/INST]",
                    'max_new_tokens'   => 50,
                    'temperature'      => 0.3,
                    'top_p'            => 0.9,
                ]);

            $result = strtolower(trim($response->json('results.0.generated_text') ?? ''));
            return str_contains($result, 'ok');
        } catch (\Exception $e) {
            logger()->warning("AI-Filter-Fehler: " . $e->getMessage());
            return true;
        }
    }

    public function render()
    {
        $stats = ParkCrowdReport::where('park_id', $this->park->id)
            ->whereDate('created_at', today())
            ->selectRaw('crowd_level, COUNT(*) as total')
            ->groupBy('crowd_level')
            ->orderBy('crowd_level')
            ->get()
            ->pluck('total', 'crowd_level');

        $averages = ParkCrowdReport::where('park_id', $this->park->id)
            ->selectRaw('
                AVG(theming) as theming_avg,
                AVG(cleanliness) as cleanliness_avg,
                AVG(gastronomy) as gastronomy_avg,
                AVG(service) as service_avg,
                AVG(attractiveness) as attractiveness_avg
            ')->first();

        return view('livewire.frontend.park-andrang-component', compact('stats', 'averages'));
    }
}
