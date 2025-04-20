<!DOCTYPE html>
<html lang="en">

<head>
    <!-- All meta and title start-->
@include('backend.partials.head')
<!-- meta and title end-->

    <!-- css start-->
@include('backend.partials.css')
<!-- css end-->



</head>

<body>
<!-- Loader start-->
<div class="app-wrapper">
    <div class="loader-wrapper">
        <div class="loader_16"></div>
    </div>
    <!-- Loader end-->

    <!-- Menu Navigation start -->
@include('backend.partials.sidebar')
<!-- Menu Navigation end -->


    <div class="app-content">
        <!-- Header Section start -->
    @include('backend.partials.header')
    <!-- Header Section end -->

        <!-- Main Section start -->
        <main>
            {{-- main body content --}}
            @yield('main-content')
        </main>
        <!-- Main Section end -->
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
</body>

<!--customizer-->
<div id="customizer"></div>

<!-- scripts start-->
@include('backend.partials.script')
<!-- scripts end-->

</html>
