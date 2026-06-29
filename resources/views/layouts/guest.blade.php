<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Public+Sans:wght@400;500;600&display=swap" rel="stylesheet">
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-[#f5f8f8] text-zinc-900 antialiased dark:bg-zinc-950 dark:text-zinc-100">
        <x-landing.navbar />

        <main>
            {{ $slot }}
        </main>

        <x-landing.footer />

        @fluxScripts
    </body>
</html>
