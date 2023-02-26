<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Sermons') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    {{--    <!-- Scripts -->--}}
    {{--    @routes--}}
    {{--    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])--}}
</head>
<body>
<div class="container mx-auto m-5 ">
    <main class="w-5xl max-w-5xl mx-auto p-5">
        <h1>Sermons</h1>
        <div class="m-5 w-full">
            {{ $slot }}
        </div>
    </main>
</div>

</body>
</html>
