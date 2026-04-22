<!DOCTYPE html>
<html data-theme="gobmx" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Seguimiento programas') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

        @livewireStyles
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @stack('css')
    </head>
    <body>
           <div class="min-h-screen bg-white">
                <livewire:layout.navbar />
                <div class="bg-secondary h-2"></div>

                <main class="min-h-screen">
                    {{ $slot }}
                </main>

                <div class="bg-secondary h-2"></div>
                @include('fragments.footer')
            </div>

        @livewireScripts  
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
        @stack('scripts')
    </body>
</html>
