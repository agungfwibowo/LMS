@props([
    'glass' => false,       // true = measured glassmorphism (accent surfaces only)
    'hover' => false,       // true = subtle lift on hover
    'padding' => 'p-6',     // override e.g. padding="p-0" for edge-to-edge media
    'as' => 'div',          // root tag; ignored when href is set
    'href' => null,         // when set, renders an <a> (whole card clickable)
])

@php
    $tag = $href ? 'a' : $as;

    $base = 'group rounded-2xl border transition-all duration-300 ease-spring';

    $surface = $glass
        ? 'border-white/60 bg-white/70 shadow-sm backdrop-blur-md dark:border-white/10 dark:bg-zinc-900/60'
        : 'border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900';

    $lift = $hover
        ? 'will-change-transform motion-safe:hover:-translate-y-2 hover:shadow-xl hover:shadow-brand-900/10'
        : '';
@endphp

<{{ $tag }}
    @if ($href) href="{{ $href }}" @endif
    {{ $attributes->class([$base, $surface, $lift, $padding]) }}
>
    @isset($header)
        <div class="mb-4">{{ $header }}</div>
    @endisset

    {{ $slot }}

    @isset($footer)
        <div class="mt-5 flex flex-wrap items-center gap-2">{{ $footer }}</div>
    @endisset
</{{ $tag }}>
