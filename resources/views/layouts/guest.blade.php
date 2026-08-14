<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="black">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? config('app.name', 'Auletta LDR') }}</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="{{ asset('css/main.css') }}">
        <link rel="stylesheet" href="{{ asset('css/responsive.css') }}">

        <link rel="icon" type="image/webp" href="{{ asset('img/logo-prenotaldr-giallo.webp') }}">

        <!-- @vite(['resources/css/app.css', 'resources/js/app.js']) -->
        @livewireStyles
    </head>
    <body class="full-page-form-body">
        
        <main>
            {{ $slot }}
        </main>

        @livewireScripts
    </body>
</html>
