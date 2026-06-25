@props([
    'links' => [
        ['label' => 'Beranda', 'href' => '#hero'],
        ['label' => 'Pelatihan', 'href' => '#katalog'],
        ['label' => 'Alur Daftar', 'href' => '#alur'],
        ['label' => 'Jadwal', 'href' => '#jadwal'],
        ['label' => 'Berita', 'href' => '#berita'],
        ['label' => 'FAQ', 'href' => '#faq'],
    ],
])

<header
    x-data="{ open: false, scrolled: false }"
    @scroll.window="scrolled = window.scrollY > 8"
    :class="scrolled ? 'border-zinc-200 bg-white/90 shadow-sm dark:border-zinc-800 dark:bg-zinc-900/90' : 'border-transparent bg-white/60 dark:bg-zinc-900/60'"
    class="fixed inset-x-0 top-0 z-50 border-b backdrop-blur transition-colors"
>
    <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
        <x-landing.brand />

        {{-- Desktop nav --}}
        <div class="hidden items-center gap-1 lg:flex">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}"
                   class="rounded-lg px-3 py-2 text-sm font-medium text-zinc-600 transition-colors hover:bg-brand-50 hover:text-brand-700 dark:text-zinc-300 dark:hover:bg-zinc-800 dark:hover:text-brand-400">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        {{-- Desktop actions --}}
        <div class="hidden items-center gap-2 lg:flex">
            @auth
                <a href="{{ route('dashboard') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700">
                    Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-zinc-700 transition-colors hover:bg-zinc-100 dark:text-zinc-200 dark:hover:bg-zinc-800">
                    Masuk
                </a>
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700">
                        Daftar Pelatihan
                    </a>
                @endif
            @endauth
        </div>

        {{-- Mobile toggle --}}
        <button type="button" @click="open = !open"
                class="inline-flex size-10 items-center justify-center rounded-lg text-zinc-700 hover:bg-zinc-100 lg:hidden dark:text-zinc-200 dark:hover:bg-zinc-800"
                :aria-expanded="open" aria-label="Buka menu">
            <flux:icon name="bars-3" x-show="!open" class="size-6" />
            <flux:icon name="x-mark" x-show="open" x-cloak class="size-6" />
        </button>
    </nav>

    {{-- Mobile menu --}}
    <div x-show="open" x-cloak x-transition.opacity @click.self="open = false"
         class="border-t border-zinc-200 bg-white px-4 py-4 lg:hidden dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-1">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}" @click="open = false"
                   class="rounded-lg px-3 py-2.5 text-sm font-medium text-zinc-700 hover:bg-brand-50 hover:text-brand-700 dark:text-zinc-200 dark:hover:bg-zinc-800">
                    {{ $link['label'] }}
                </a>
            @endforeach
            <div class="mt-3 flex flex-col gap-2 border-t border-zinc-200 pt-3 dark:border-zinc-800">
                @auth
                    <a href="{{ route('dashboard') }}" class="rounded-lg bg-brand-600 px-4 py-2.5 text-center text-sm font-semibold text-white">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg border border-zinc-300 px-4 py-2.5 text-center text-sm font-semibold text-zinc-700 dark:border-zinc-700 dark:text-zinc-200">Masuk</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="rounded-lg bg-brand-600 px-4 py-2.5 text-center text-sm font-semibold text-white">Daftar Pelatihan</a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</header>
