@props([
    'subtitle' => 'Sistem Pelatihan RS Adam Malik',
])

<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'flex items-center gap-3']) }} wire:navigate>
    <span class="flex size-10.5 shrink-0 items-center justify-center">
        <img src="{{ asset('logo.png') }}" alt="SIPAHAM" class="size-full object-contain">
    </span>
    <span class="flex flex-col leading-[1.05]">
        <span class="font-heading text-[20px] font-extrabold tracking-[-0.02em] text-brand-900 dark:text-teal-400">SIPAHAM</span>
        @if ($subtitle)
            <span class="text-[11px] font-medium tracking-[0.02em] text-zinc-500 dark:text-zinc-400">{{ $subtitle }}</span>
        @endif
    </span>
</a>
