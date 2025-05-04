
@extends('frontend.layouts.app')

@section('title', 'Seite nicht gefunden')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-b from-[#0a0050] to-[#ff0055] text-white p-8 text-center">

    {{-- Freizeitpark-Illustration (ohne Rand) --}}
    <div class="w-full max-w-xl mb-8">
        <img src="{{ asset('images/404-amusement.png') }}" alt="404 Freizeitpark Illustration" class="w-full h-auto drop-shadow-2xl rounded-xl">
    </div>

    <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight drop-shadow-xl">404</h1>
    <p class="text-xl md:text-2xl mt-4 font-semibold">Diese Seite wurde leider nicht gefunden 🎢</p>
    <p class="mt-2 text-base md:text-lg opacity-80">Aber hey – vielleicht findest du stattdessen deinen nächsten Freizeitpark?</p>

    <a href="{{ url('/') }}" class="mt-8 inline-block bg-white text-[#ff0055] font-bold px-8 py-3 rounded-full shadow-xl hover:bg-gray-100 transition">
        Zur Startseite
    </a>
</div>
@endsection
