@props([
    'eyebrow' => null,
    'title' => '',
    'subtitle' => null,
    'center' => true,
])

<div class="{{ $center ? 'mx-auto max-w-2xl text-center' : 'max-w-2xl' }}">
    @if ($eyebrow)
        <span class="inline-block rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold uppercase tracking-wider text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">
            {{ $eyebrow }}
        </span>
    @endif

    <h2 class="mt-4 text-3xl font-bold tracking-tight text-zinc-900 sm:text-4xl dark:text-white">
        {{ $title }}
    </h2>

    @if ($subtitle)
        <p class="mt-4 text-base leading-relaxed text-zinc-600 dark:text-zinc-400">
            {{ $subtitle }}
        </p>
    @endif
</div>
