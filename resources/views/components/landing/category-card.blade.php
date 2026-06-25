@props([
    'icon' => 'academic-cap',
    'title' => '',
    'count' => null,
    'description' => '',
    'href' => '#',
])

<a href="{{ $href }}"
   {{ $attributes->merge(['class' => 'group relative flex flex-col rounded-2xl border border-zinc-200 bg-white p-6 transition-all hover:-translate-y-1 hover:border-brand-300 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-brand-700']) }}>
    <span class="mb-4 flex size-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition-colors group-hover:bg-brand-600 group-hover:text-white dark:bg-brand-500/10 dark:text-brand-400">
        <flux:icon name="{{ $icon }}" class="size-6" />
    </span>

    <h3 class="text-base font-semibold text-zinc-900 dark:text-white">{{ $title }}</h3>
    <p class="mt-2 flex-1 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ $description }}</p>

    <div class="mt-4 flex items-center justify-between">
        @if (! is_null($count))
            <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $count }} modul</span>
        @else
            <span></span>
        @endif
        <span class="inline-flex items-center gap-1 text-sm font-semibold text-brand-600 dark:text-brand-400">
            Lihat
            <flux:icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" />
        </span>
    </div>
</a>
