@props([
    'number' => '1',
    'icon' => null,
    'title' => '',
    'description' => '',
])

<div class="relative flex flex-col rounded-2xl border border-zinc-200 bg-white p-6 dark:border-zinc-800 dark:bg-zinc-900">
    <div class="mb-4 flex items-center gap-3">
        <span class="flex size-10 items-center justify-center rounded-full bg-brand-600 text-sm font-bold text-white">{{ $number }}</span>
        @if ($icon)
            <flux:icon name="{{ $icon }}" class="size-5 text-brand-600 dark:text-brand-400" />
        @endif
    </div>
    <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ $title }}</h3>
    <p class="mt-2 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ $description }}</p>
</div>
