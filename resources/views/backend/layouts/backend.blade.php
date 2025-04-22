<!DOCTYPE html>
<html lang="{{ config('site.language', 'de') }}">

<head>
    <!-- All meta and title start-->
    @include('backend.partials.head')
    <!-- meta and title end-->
    <!-- All CSS files start -->
    @include('backend.partials.css')

    <style>
 .app-pagination {
    display: flex;
    gap: 0.25rem;
    justify-content: center;
}

.app-pagination .page-link {
    border-radius: 0;
    padding: 0.4rem 0.75rem;
}

.app-pagination .page-item.active .page-link {
    background-color: #3490dc;
    color: white;
    border-color: #3490dc;
}
    </style>



@stack('styles')
@vite(['resources/backend/css/app.css'])
<!-- Livewire Styles -->
@livewireStyles
{{-- All CSS files end --}}
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta name="session-id" content="{{ Session::getId() }}">
</head>
<body>
    <div class="app-wrapper">
        <!-- Loader start-->
        <div class="loader-wrapper">
            <div class="loader_16"></div>
        </div>
        @include('backend.partials.sidebar')
        <div class="app-content">
            <div class="">
                @include('backend.partials.header')


            <!-- Main Section start -->
            <main>
                {{-- main body content --}}
                @if(request()->header('X-Livewire') || isset($slot))
                    {{ $slot }}
                @else
                    @yield('main-content')
                @endif
            </main>
            <!-- Main Section end -->
        </div>
        </div>

        <!-- tap on top -->
        <div class="go-top">
        <span class="progress-value">
          <i class="ti ti-arrow-up"></i>
        </span>
       </div>

        <!-- Footer Section start -->
         @include('backend.partials.footer')
        <!-- Footer Section end -->
    </div>

    {{--
    @include('backend.partials.modal')
    --}}
      <!--customizer-->
      <div id="customizer"></div>
      @include('backend.partials.script')

 <!-- Seiten-spezifische Skripte -->
 @livewireScripts
 @vite(['resources/backend/js/app.js'])
 @stack('scripts')
</body>
</html>
