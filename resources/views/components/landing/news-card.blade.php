@props([
    'title' => '',
    'category' => 'Pengumuman',
    'date' => '',
    'excerpt' => '',
    'href' => '#',
    'icon' => 'newspaper',
    'image' => null,
])

<article class="group flex flex-col overflow-hidden rounded-2xl border border-zinc-200 bg-white transition-all hover:-translate-y-1 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900">
    <div class="relative h-40 overflow-hidden">
        @if($image)
            <img src="{{ $image }}" alt="{{ $title }}" class="h-full w-full object-cover transition-transform duration-300 group-hover:scale-105">
        @else
            <div class="flex h-full items-center justify-center bg-gradient-to-br from-brand-500 to-brand-700">
                <flux:icon name="{{ $icon }}" class="size-12 text-white/80" />
            </div>
        @endif
        <span class="absolute left-4 top-4 rounded-full bg-white/90 px-3 py-1 text-xs font-semibold text-brand-700">{{ $category }}</span>
    </div>

    <div class="flex flex-1 flex-col p-5">
        <time class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $date }}</time>
        <h3 class="mt-2 text-base font-semibold leading-snug text-zinc-900 group-hover:text-brand-700 dark:text-white dark:group-hover:text-brand-400">{{ $title }}</h3>
        <p class="mt-2 flex-1 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ $excerpt }}</p>
        <a href="{{ $href }}" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-600 dark:text-brand-400">
            Baca selengkapnya
            <flux:icon name="arrow-right" class="size-4 transition-transform group-hover:translate-x-0.5" />
        </a>
    </div>
</article>
