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

            <article class="prose lg:prose-lg max-w-none prose-headings:text-[#080e3c] prose-a:text-purple-600 prose-a:underline-offset-2 prose-img:rounded-md prose-pre:bg-gray-800 prose-pre:text-white text-gray-700">
                {!! $page->content !!}
            </article>
        </div>
    </section>
@endsection
