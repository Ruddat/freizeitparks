@extends('frontend.layouts.app')

@section('title', 'Serverfehler')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-b from-[#0a0050] to-[#ff0055] text-white p-8 text-center">

    {{-- Illustration --}}
    <div class="w-full max-w-xl mb-8">
        <img src="{{ asset('images/500-error.png') }}" alt="Serverfehler" class="w-full h-auto drop-shadow-2xl rounded-xl">
    </div>

    <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight drop-shadow-xl">500</h1>
    <p class="text-xl md:text-2xl mt-4 font-semibold">Hoppla! Da ist was schiefgelaufen 😵</p>
    <p class="mt-2 text-base md:text-lg opacity-80">Der Server hat einen Looping zu viel gedreht.</p>

    <a href="{{ url('/') }}" class="mt-8 inline-block bg-white text-[#ff0055] font-bold px-8 py-3 rounded-full shadow-xl hover:bg-gray-100 transition">
        Zur Startseite
    </a>
</div>
@endsection
