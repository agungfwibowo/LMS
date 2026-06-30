@props([
    'subtitle' => 'RS Adam Malik',
])

<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'flex items-center gap-3']) }} wire:navigate>
    <span class="flex size-10 items-center justify-center p-1 rounded-lg bg-zinc-100/50 backdrop-blur-2xl">
        <img src="{{ asset('logo.png') }}" alt="SIPAHAM" class="">
    </span>
    <span class="flex flex-col">
        <span class="font-heading text-lg leading-[1.2em] font-extrabold text-brand-900 dark:text-teal-400">SIPAHAM</span>
        @if ($subtitle)
            <span class="text-[12px] leading-[1em] font-medium text-zinc-500 dark:text-zinc-400">{{ $subtitle }}</span>
        @endif
    </span>
</a>
