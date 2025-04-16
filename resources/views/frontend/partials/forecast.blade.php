<section class="my-12">
    <h2 class="text-2xl font-semibold mb-4">Wettervorhersage (7 Tage)</h2>
    <div class="grid grid-cols-2 md:grid-cols-7 gap-4">
        @foreach($forecast as $day)
            <div class="bg-white shadow rounded-lg p-4 text-center">
                <div class="font-semibold">{{ $day['date'] }}</div>
                <img src="{{ $day['icon'] }}" alt="Icon" class="mx-auto w-12 h-12">
                <div class="mt-1 text-sm">
                    <span class="text-red-600">{{ $day['temp_day'] }}°</span> /
                    <span class="text-blue-600">{{ $day['temp_night'] }}°</span>
                </div>
            </div>
        @endforeach
    </div>
</section>
