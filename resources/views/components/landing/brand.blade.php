@props([
    'subtitle' => 'RS Adam Malik',
])

{{-- Logo lockup SIPAHAM: mark gradien + wordmark. Dipakai di navbar & footer. --}}
<a href="{{ route('home') }}" {{ $attributes->merge(['class' => 'flex items-center gap-3']) }} wire:navigate>
    <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 text-white shadow-sm">
        <!-- <flux:icon name="academic-cap" variant="solid" class="size-6" /> -->
         <img src="{{ asset('logo.png') }}" alt="Application Logo" class="bg-white p-0.5 rounded-sm">
    </span>
    <span class="flex flex-col leading-none">
        <span class="text-lg font-bold tracking-tight text-zinc-900 dark:text-white">SIPAHAM</span>
        @if ($subtitle)
            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $subtitle }}</span>
        @endif
    </span>
</a>
