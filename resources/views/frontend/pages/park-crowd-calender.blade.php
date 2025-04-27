@extends('frontend.layouts.app')

@section('title', $park->title)

@section('content')

<div class="container mx-auto px-4 py-8 flex min-h-screen">
    <!-- Sidebar Navigation -->
    <aside class="w-64 flex-shrink-0 sticky top-0 max-h-screen overflow-y-auto z-10 md:block hidden">
        <nav class="bg-white p-4 rounded-lg shadow-md flex flex-col gap-4">
            <div class="text-gray-800 font-bold mb-2">{{ $park->title }}</div>
            <a href="#" class="text-gray-600 hover:text-blue-600 font-medium">Dieser Park</a>
            <a href="#" class="text-gray-600 hover:text-blue-600 font-medium">Live-Wartezeiten</a>
            <a href="#" class="text-gray-600 hover:text-blue-600 font-medium">Andrangskalender</a>
            <a href="#" class="text-gray-600 hover:text-blue-600 font-medium">Statistiken</a>
            <a href="#" class="text-gray-600 hover:text-blue-600 font-medium">Besucherzahlen</a>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 ml-8">
        {{-- Kalender --}}
        <section id="calendar" class="mb-12">
            <div class="bg-[#10163A] rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-white mb-4">📅 Kalender (Crowd Calendar)</h2>
                <livewire:frontend.statistic.crowd-calendar :park="$park" />
            </div>
        </section>

        {{-- Besucherzahlen --}}
        <section id="chart" class="mb-12">
            <div class="bg-[#10163A] rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-white mb-4">📈 Besuchertrend</h2>
                <livewire:frontend.statistic.crowd-chart :park="$park" :year="now()->year" :month="now()->month" />
            </div>
        </section>
    </main>
</div>

@endsection
