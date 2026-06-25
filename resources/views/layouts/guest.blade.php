<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
        <x-landing.navbar />

        <main>
            {{ $slot }}
        </main>

        <x-landing.footer />

        @fluxScripts
    </body>
</html>
