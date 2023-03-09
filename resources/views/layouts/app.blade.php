<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.3/dist/jquery.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.2/dist/semantic.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.2/dist/semantic.min.js"></script>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{--    <!-- Styles -->--}}
    @livewireStyles
</head>
<body class="font-sans antialiased">
<x-banner/>

{{--<div class="min-h-screen bg-gray-100">--}}
{{--    --}}{{--            @livewire('navigation-menu')--}}

{{--    <!-- Page Heading -->--}}
{{--    @if (isset($header))--}}
{{--        <header class="bg-white shadow">--}}
{{--            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">--}}
{{--                {{ $header }}--}}
{{--            </div>--}}
{{--        </header>--}}
{{--    @endif--}}

{{--    <!-- Page Content -->--}}
{{--    <main>--}}
{{--        {{ $slot }}--}}
{{--    </main>--}}
{{--</div>--}}


<main>
    <div class="ui top fixed menu">
        <a href="/">
            <div class="header item">
                <img class="ui avatar image" src="{{ Storage::url("public/elizabeth.jpeg") }}">
                Elizabeth Locher's Sermons
            </div>
        </a>
    </div>
    <div class="ui main container">

        {{ $slot }}
    </div>
</main>

@stack('modals')

@livewireScripts
</body>
</html>
