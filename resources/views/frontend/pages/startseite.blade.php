@extends('frontend.layouts.app')

@section('content')

    {{-- Park-Map --}}
    <section class="mb-16">
        <h2 class="text-2xl font-semibold mb-6">Entdecke Parks in deiner Nähe</h2>
        <div class="w-full rounded-lg">
            <livewire:frontend.parks.park-map />
        </div>
    </section>

    {{-- Park-Liste --}}
    <section id="park-liste" class="mb-16">
        <h2 class="text-2xl font-semibold mb-6">Beliebte Freizeitparks</h2>
        <livewire:frontend.parks.park-liste />

        <div class="mt-6">
            <a href="{{ route('parks.show', 1) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                Testlink zu Testpark 1
            </a>
        </div>
    </section>

    {{-- Beispielkarten (optional) --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Beispiel-Karte -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <img src="{{ asset('images/park1.jpg') }}" class="w-full h-48 object-cover" alt="Park 1">
            <div class="p-4">
                <h3 class="text-xl font-bold">Europa Park</h3>
                <p class="text-gray-600 text-sm">Einer der größten Freizeitparks Europas mit über 100 Attraktionen.</p>
            </div>
        </div>
    </div>

@endsection
