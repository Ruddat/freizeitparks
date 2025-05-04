@extends('frontend.layouts.app')

@section('title', 'Sitzung abgelaufen')

@section('content')
<div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-b from-[#0a0050] to-[#ff0055] text-white p-8 text-center">

    {{-- Illustration --}}
    <div class="w-full max-w-xs mb-8">
        <img src="{{ asset('images/419-timeout.png') }}" alt="Sitzung abgelaufen" class="w-full h-auto drop-shadow-2xl rounded-xl">
    </div>

    <h1 class="text-5xl md:text-6xl font-extrabold tracking-tight drop-shadow-xl">419</h1>
    <p class="text-xl md:text-2xl mt-4 font-semibold">Sitzung abgelaufen ⏳</p>
    <p class="mt-2 text-base md:text-lg opacity-80">Dein Formular war zu lange offen – bitte lade die Seite neu und versuch’s nochmal.</p>

    <a href="{{ url()->previous() ?? url('/') }}" class="mt-8 inline-block bg-white text-[#ff0055] font-bold px-8 py-3 rounded-full shadow-xl hover:bg-gray-100 transition">
        Zurück zur vorherigen Seite
    </a>
</div>
@endsection
