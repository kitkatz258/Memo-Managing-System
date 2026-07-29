<!DOCTYPE html>
<html lang="en" class="light scroll-smooth" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Memo Management System')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{ asset('vendor/techwind/images/favicon.ico') }}">

    <link href="{{ asset('vendor/techwind/libs/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/techwind/libs/tiny-slider/tiny-slider.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/techwind/libs/tobii/css/tobii.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/techwind/libs/simplebar/simplebar.min.css') }}" rel="stylesheet">
    <link href="{{ asset('vendor/techwind/libs/remixicon/fonts/remixicon.css') }}" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('vendor/techwind/css/output.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/techwind/css/tailwind.css') }}">
</head>

<body class="font-nunito text-base text-slate-900 dark:text-white dark:bg-slate-900">
    <div class="page-wrapper toggled">
        @include('layouts.partials.sidebar')

        <main class="page-content bg-gray-50 dark:bg-slate-800">
            @include('layouts.partials.navbar')

            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>

    <script src="{{ asset('vendor/techwind/libs/gumshoejs/gumshoe.polyfills.min.js') }}"></script>
    <script src="{{ asset('vendor/techwind/libs/shufflejs/shuffle.min.js') }}"></script>
    <script src="{{ asset('vendor/techwind/libs/tobii/js/tobii.min.js') }}"></script>
    <script src="{{ asset('vendor/techwind/libs/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('vendor/techwind/js/jsvectormap.init.js') }}"></script>
    <script src="{{ asset('vendor/techwind/libs/tiny-slider/min/tiny-slider.js') }}"></script>
    <script src="{{ asset('vendor/techwind/libs/simplebar/simplebar.min.js') }}"></script>
    <script src="{{ asset('vendor/techwind/js/plugins.init.js') }}"></script>
    <script src="{{ asset('vendor/techwind/js/app.js') }}"></script>

    @stack('scripts')
</body>
</html>