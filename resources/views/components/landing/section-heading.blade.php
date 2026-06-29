@props([
    'eyebrow' => null,
    'title' => '',
    'subtitle' => null,
    'center' => true,
    'dark' => false,
])

<div class="{{ $center ? 'mx-auto max-w-2xl text-center' : 'max-w-2xl' }}">
    @if ($eyebrow)
        <div class="text-[13.5px] font-bold uppercase tracking-[0.06em] mb-3 {{ $dark ? 'text-lime' : 'text-brand-600' }}">
            {{ $eyebrow }}
        </div>
    @endif

    <h2 class="font-heading font-extrabold text-[2rem] leading-[1.1] tracking-tight {{ $dark ? 'text-white' : 'text-brand-950 dark:text-white' }}">
        {{ $title }}
    </h2>

    @if ($subtitle)
        <p class="mt-4 text-base leading-relaxed {{ $dark ? 'text-zinc-300' : 'text-zinc-600 dark:text-zinc-400' }}">
            {{ $subtitle }}
        </p>
    @endif
</div>
