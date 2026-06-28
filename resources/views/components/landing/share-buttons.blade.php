@props([
    'url'   => '',
    'title' => '',
])

@php
    $encodedUrl   = urlencode($url);
    $encodedTitle = urlencode($title);

    $shares = [
        [
            'label'   => 'Facebook',
            'id'      => 'fb',
            'icon'    => 'M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z',
            'href'    => "https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}",
            'color'   => 'hover:bg-[#1877F2] hover:text-white hover:border-[#1877F2]',
        ],
        [
            'label'   => 'X (Twitter)',
            'id'      => 'x',
            'icon'    => 'M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.736l7.737-8.835L1.254 2.25H8.08l4.259 5.631zm-1.161 17.52h1.833L7.084 4.126H5.117z',
            'href'    => "https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedTitle}",
            'color'   => 'hover:bg-black hover:text-white hover:border-black',
        ],
        [
            'label'   => 'LinkedIn',
            'id'      => 'li',
            'icon'    => 'M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6zM2 9h4v12H2z M4 6a2 2 0 1 0 0-4 2 2 0 0 0 0 4z',
            'href'    => "https://www.linkedin.com/sharing/share-offsite/?url={$encodedUrl}",
            'color'   => 'hover:bg-[#0A66C2] hover:text-white hover:border-[#0A66C2]',
        ],
        [
            'label'   => 'Email',
            'id'      => 'email',
            'icon'    => 'M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z M22 6l-10 7L2 6',
            'href'    => "mailto:?subject={$encodedTitle}&body={$encodedUrl}",
            'color'   => 'hover:bg-zinc-800 hover:text-white hover:border-zinc-800',
        ],
    ];
@endphp

<div class="flex flex-wrap items-center gap-3">
    <span class="text-sm font-semibold text-zinc-600 dark:text-zinc-400">Bagikan:</span>

    {{-- Shareable links --}}
    @foreach ($shares as $share)
        <a href="{{ $share['href'] }}"
           target="{{ $share['id'] !== 'email' ? '_blank' : '_self' }}"
           rel="noopener noreferrer"
           title="Bagikan ke {{ $share['label'] }}"
           class="inline-flex size-9 items-center justify-center rounded-xl border border-zinc-300 bg-white text-zinc-600 transition-all {{ $share['color'] }} dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="{{ $share['icon'] }}" />
            </svg>
            <span class="sr-only">{{ $share['label'] }}</span>
        </a>
    @endforeach

    {{-- Instagram: copy-to-clipboard --}}
    <button
        x-data="{ copied: false }"
        @click="navigator.clipboard.writeText(@js($url)).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
        title="Salin tautan untuk dibagikan ke Instagram"
        class="inline-flex size-9 items-center justify-center rounded-xl border border-zinc-300 bg-white text-zinc-600 transition-all hover:border-[#E1306C] hover:bg-[#E1306C] hover:text-white dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300"
        x-tooltip.raw="Salin tautan (untuk Instagram)"
    >
        {{-- Instagram icon --}}
        <svg xmlns="http://www.w3.org/2000/svg" class="size-4" fill="none" viewBox="0 0 24 24"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect width="20" height="20" x="2" y="2" rx="5" ry="5"/>
            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
            <line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/>
        </svg>
        <span class="sr-only">Instagram (salin tautan)</span>
    </button>

    {{-- Copy link --}}
    <button
        x-data="{ copied: false }"
        @click="navigator.clipboard.writeText(@js($url)).then(() => { copied = true; setTimeout(() => copied = false, 2000) })"
        class="inline-flex h-9 items-center gap-2 rounded-xl border border-zinc-300 bg-white px-3 text-xs font-semibold text-zinc-600 transition-all hover:border-brand-500 hover:bg-brand-50 hover:text-brand-700 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300 dark:hover:border-brand-500 dark:hover:bg-brand-900/30 dark:hover:text-brand-400"
    >
        <template x-if="!copied">
            <flux:icon name="link" class="size-3.5" />
        </template>
        <template x-if="copied">
            <flux:icon name="check" class="size-3.5 text-brand-600" />
        </template>
        <span x-text="copied ? 'Tersalin!' : 'Salin tautan'"></span>
    </button>
</div>
