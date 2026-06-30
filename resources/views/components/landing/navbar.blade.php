@php
    $homeUrl = route('home');
    $isHome = request()->routeIs('home');
    $anchor = fn(string $id) => $isHome ? "#$id" : "$homeUrl#$id";
    $isCurrent = fn(string $href, ?string $routes = null) =>
        ($routes && request()->routeIs($routes)) ||
        (!str_contains($href, '#') && request()->url() === url($href));
@endphp

@props([
    'links' => [
        ['label' => 'Beranda',    'href' => $anchor('hero'),    'section' => 'hero'],
        ['label' => 'Pelatihan',  'href' => $anchor('katalog'), 'section' => 'katalog'],
        ['label' => 'Alur Daftar','href' => $anchor('alur'),    'section' => 'alur'],
        ['label' => 'Jadwal',     'href' => $anchor('jadwal'),  'section' => 'jadwal'],
        ['label' => 'Berita',     'href' => route('berita.index'), 'routes' => 'berita.*', 'section' => null],
        ['label' => 'FAQ',        'href' => $anchor('faq'),     'section' => 'faq'],
    ],
])

<header
    x-data="{
        open: false,
        scrolled: false,
        activeSection: null,
        dark: document.documentElement.classList.contains('dark'),
        init() {
            const ids = ['hero', 'katalog', 'alur', 'jadwal', 'faq'];
            this._onScroll = () => {
                this.scrolled = window.scrollY > 8;
                let active = null;
                ids.forEach(id => {
                    const el = document.getElementById(id);
                    if (el && el.getBoundingClientRect().top <= 85) active = id;
                });
                this.activeSection = active;
            };
            window.addEventListener('scroll', this._onScroll, { passive: true });
            this._onScroll();
            this._darkObserver = new MutationObserver(() => {
                this.dark = document.documentElement.classList.contains('dark');
            });
            this._darkObserver.observe(document.documentElement, { attributeFilter: ['class'] });
        },
        destroy() {
            window.removeEventListener('scroll', this._onScroll);
            this._darkObserver.disconnect();
        }
    }"
    :style="scrolled
        ? (dark ? 'background:rgba(24,24,27,0.97);box-shadow:0 1px 12px rgba(0,0,0,0.3)' : 'background:rgba(245,248,248,0.97);box-shadow:0 1px 12px rgba(14,79,77,0.08)')
        : (dark ? 'background:rgba(24,24,27,0.85)' : 'background:rgba(245,248,248,0.85)')"
    class="fixed inset-x-0 top-0 z-50 border-b border-zinc-200 backdrop-blur-md transition-[background,box-shadow] duration-200 dark:border-zinc-800"
>
    <nav class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">

        {{-- Brand --}}
        <x-landing.brand />

        {{-- Desktop links --}}
        <div class="ms-auto hidden items-center gap-2 lg:flex">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}"
                   @if($link['section'] ?? null)
                       :aria-current="activeSection === '{{ $link['section'] }}' ? 'page' : undefined"
                   @elseif($isCurrent($link['href'], $link['routes'] ?? null))
                       aria-current="page"
                   @endif
                   class="nav-link text-[14.5px] font-semibold text-zinc-700 transition-colors hover:text-brand-900 dark:text-zinc-300 dark:hover:text-teal-400">
                    {{ $link['label'] }}
                </a>
            @endforeach
        </div>

        {{-- Desktop actions --}}
        <div class="flex items-center gap-2">
            <flux:separator class="hidden lg:block" vertical />

            {{-- Dark mode toggle --}}
            <flux:button x-data x-on:click="$flux.dark = !$flux.dark" icon="moon" variant="subtle" size="sm" aria-label="Toggle dark mode" class="text-zinc-700! dark:text-zinc-300!" />
            {{-- Auth buttons --}}
            <div class="hidden items-center gap-2.5 lg:flex">
                @auth
                    <flux:dropdown>
                        <flux:profile :initials="auth()->user()->initials" />
                        <flux:navmenu class="max-w-[12rem]">
                            <div class="px-2 py-1.5">
                                <flux:text size="sm">Signed in as</flux:text>
                                <flux:heading class="mt-1! truncate">{{ auth()->user()->email }}</flux:heading>
                            </div>
                            <flux:navmenu.separator />
                            <flux:navmenu.item href="{{ route('dashboard') }}" icon="squares-2x2" class="text-zinc-800 dark:text-white">Dashboard</flux:navmenu.item>
                            <flux:navmenu.item href="{{ route('profile.edit') }}" icon="cog-6-tooth" class="text-zinc-800 dark:text-white">Settings</flux:navmenu.item>
                            <flux:navmenu.separator />
                            <flux:navmenu.item href="{{ route('logout') }}" icon="arrow-right-start-on-rectangle" class="text-zinc-800 dark:text-white"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</flux:navmenu.item>
                        </flux:navmenu>
                    </flux:dropdown>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                        <input type="hidden" name="redirect" value="{{ url()->current() }}">
                    </form>
                @else
                    <flux:button href="{{ route('login', ['redirect' => url()->current()]) }}" class="bg-lime! text-brand-950! border-transparent! shadow-[0_1px_2px_rgba(13,58,57,0.12)]! hover:opacity-90!">Masuk</flux:button>
                    @if (Route::has('register'))
                        <!-- <flux:button href="{{ route('register') }}"
                            class="bg-lime! text-brand-950! border-transparent! shadow-[0_1px_2px_rgba(13,58,57,0.12)]! hover:opacity-90!">
                            Daftar
                        </flux:button> -->
                    @endif
                @endauth
            </div>

            {{-- Mobile hamburger --}}
            <button type="button" @click="open = !open"
                    class="inline-flex size-10 items-center justify-center rounded-lg text-zinc-700 hover:bg-brand-50 lg:hidden dark:text-zinc-300 dark:hover:bg-zinc-800"
                    :aria-expanded="open" aria-label="Buka menu">
                <flux:icon name="bars-3" x-show="!open" class="size-6" />
                <flux:icon name="x-mark" x-show="open" x-cloak class="size-6" />
            </button>
        </div>
    </nav>

    {{-- Mobile menu --}}
    <div x-show="open" x-cloak x-transition.opacity @click.self="open = false"
         class="border-t border-zinc-200 bg-zinc-50 px-4 py-4 lg:hidden dark:border-zinc-800 dark:bg-zinc-900">
        <div class="flex flex-col gap-1">
            @foreach ($links as $link)
                <a href="{{ $link['href'] }}" @click="open = false"
                   @if($link['section'] ?? null)
                       :aria-current="activeSection === '{{ $link['section'] }}' ? 'page' : undefined"
                   @elseif($isCurrent($link['href'], $link['routes'] ?? null))
                       aria-current="page"
                   @endif
                   class="nav-link rounded-lg px-3 py-2.5 text-[14.5px] font-semibold transition-colors hover:bg-brand-50 hover:text-brand-900 dark:hover:bg-zinc-800 dark:hover:text-teal-400">
                    {{ $link['label'] }}
                </a>
            @endforeach

            <div class="mt-3 flex flex-col gap-2 border-t border-zinc-200 pt-3 dark:border-zinc-800">
                @auth
                    <flux:dropdown>
                        <flux:profile :name="auth()->user()->name" />
                        <flux:navmenu class="max-w-[12rem]">
                            <div class="px-2 py-1.5">
                                <flux:text size="sm">Signed in as</flux:text>
                                <flux:heading class="mt-1! truncate">{{ auth()->user()->email }}</flux:heading>
                            </div>
                            <flux:navmenu.separator />
                            <flux:navmenu.item href="{{ route('dashboard') }}" icon="squares-2x2" class="text-zinc-800 dark:text-white">Dashboard</flux:navmenu.item>
                            <flux:navmenu.item href="{{ route('profile.edit') }}" icon="cog-6-tooth" class="text-zinc-800 dark:text-white">Settings</flux:navmenu.item>
                            <flux:navmenu.separator />
                            <flux:navmenu.item href="{{ route('logout') }}" icon="arrow-right-start-on-rectangle" class="text-zinc-800 dark:text-white"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Logout</flux:navmenu.item>
                        </flux:navmenu>
                    </flux:dropdown>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                        @csrf
                        <input type="hidden" name="redirect" value="{{ url()->current() }}">
                    </form>
                @else
                    <flux:button href="{{ route('login', ['redirect' => url()->current()]) }}" variant="outline" class="w-full justify-center">Masuk</flux:button>
                    @if (Route::has('register'))
                        <flux:button href="{{ route('register') }}" variant="primary" class="w-full justify-center">Daftar Sekarang</flux:button>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</header>
