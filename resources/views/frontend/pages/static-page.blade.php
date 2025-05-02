@extends('frontend.layouts.app')

@section('title', $page->title)

@section('content')
    <section class="relative z-10 bg-[#080e3c] py-16 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto bg-white shadow-2xl rounded-xl p-8 sm:p-12">
            <div class="text-center mb-10 relative">
                <h1 class="text-3xl sm:text-4xl font-extrabold text-[#080e3c] tracking-tight inline-block">
                    {{ $page->title }}

                    {{-- ✏️ Animierte Unterstreichung --}}
                    <svg class="absolute left-1/2 -translate-x-1/2 -bottom-2 w-36 h-6"
                         viewBox="0 0 500 150" preserveAspectRatio="none"
                         fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M7.7,145.6C109,125,299.9,116.2,401,121.3c42.1,2.2,87.6,11.8,87.3,25.7"
                              stroke="#8b5cf6" stroke-width="6"
                              stroke-linecap="round"
                              stroke-dasharray="600"
                              stroke-dashoffset="600">
                            <animate attributeName="stroke-dashoffset"
                                     from="600" to="0"
                                     dur="1.3s"
                                     fill="freeze" />
                        </path>
                    </svg>
                </h1>


            </div>

            <article class="space-y-6 text-gray-700 text-base leading-relaxed">
                {{-- Inhalte mit zusätzlichen Formatierungen --}}
                {!! str_replace(
                    ['<h2>', '<h3>', '<p>', '<ul>', '<li>', '<pre>', '<code>', '<a>'],
                    [
                        '<h2 class="text-2xl font-bold text-[#080e3c] mt-8 mb-2">',
                        '<h3 class="text-xl font-semibold text-[#080e3c] mt-6 mb-2">',
                        '<p class="mb-4">',
                        '<ul class="list-disc list-inside space-y-2">',
                        '<li class="ml-4">',
                        '<pre class="bg-gray-900 text-white p-4 rounded overflow-auto text-sm">',
                        '<code class="bg-gray-100 text-purple-700 px-1 py-0.5 rounded">',
                        '<a class="text-purple-600 underline underline-offset-2 hover:text-purple-800"'
                    ],
                    $page->content
                ) !!}
            </article>
        </div>
    </section>

    {{-- Kontakt Overlay --}}

    <livewire:frontend.office.contact-overlay />

    @livewire('frontend.api-register.api-signup-form')

    @endsection
