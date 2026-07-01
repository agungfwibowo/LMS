@props([
    'title' => '',
    'category' => 'Pengumuman',
    'categories' => [],
    'date' => '',
    'excerpt' => '',
    'href' => '#',
    'icon' => 'newspaper',
    'image' => null,
])

@php
$categoryIcons = [
    'berita'      => 'newspaper',
    'kebijakan'   => 'scale',
    'panduan'     => 'book-open',
    'pengumuman'  => 'megaphone',
    'prestasi'    => 'trophy',
    'sertifikasi' => 'identification',
];

$resolvedIcons = collect($categories)
    ->map(fn($cat) => $categoryIcons[strtolower($cat)] ?? $icon)
    ->unique()
    ->values();

if ($resolvedIcons->isEmpty()) {
    $resolvedIcons = collect([$icon]);
}

$visibleCategories = [];
$hiddenCategories  = [];
$charCount         = 0;

foreach ($categories as $cat) {
    if ($charCount + mb_strlen($cat) <= 30) {
        $visibleCategories[] = $cat;
        $charCount += mb_strlen($cat);
    } else {
        $hiddenCategories[] = $cat;
    }
}

$hiddenCount        = count($hiddenCategories);
$hiddenTooltipText  = implode(', ', $hiddenCategories);
@endphp

<x-ui.card as="article" hover padding="p-0" {{ $attributes->class('flex flex-col overflow-hidden') }}>
    <div class="relative h-40 overflow-hidden">
        @if($image)
            <img src="{{ $image }}" alt="{{ $title }}" class="h-full w-full object-cover transition-transform duration-300 motion-safe:group-hover:scale-105">
        @else
            <div class="flex h-full items-center justify-center gap-4 bg-linear-to-br from-brand-500 to-brand-700">
                @foreach($resolvedIcons as $ic)
                    <flux:icon name="{{ $ic }}" class="{{ $resolvedIcons->count() > 1 ? 'size-10' : 'size-12' }} text-white/80" />
                @endforeach
            </div>
        @endif
        <div class="absolute left-4 top-4 flex flex-wrap gap-1">
            @foreach($visibleCategories as $cat)
                <span class="rounded-full bg-white/70 px-3 py-1 text-xs font-semibold text-brand-700 backdrop-blur dark:bg-white/10 dark:text-brand-300">{{ $cat }}</span>
            @endforeach
            @if($hiddenCount > 0)
                <flux:tooltip content="{{ $hiddenTooltipText }}">
                    <span class="cursor-default rounded-full bg-white/70 px-3 py-1 text-xs font-semibold text-brand-700 backdrop-blur dark:bg-white/10 dark:text-brand-300">+{{ $hiddenCount }}</span>
                </flux:tooltip>
            @endif
        </div>
    </div>

    <div class="flex flex-1 flex-col p-5">
        <time class="text-xs font-medium text-zinc-500 dark:text-zinc-400">{{ $date }}</time>
        <h3 class="mt-2 text-base font-semibold leading-snug text-zinc-900 group-hover:text-brand-700 dark:text-white dark:group-hover:text-brand-400">{{ $title }}</h3>
        <p class="mt-2 flex-1 text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">{{ $excerpt }}</p>
        <a href="{{ $href }}" class="mt-4 inline-flex items-center gap-1 text-sm font-semibold text-brand-600 dark:text-brand-400">
            Baca selengkapnya
            <flux:icon name="arrow-right" class="size-4 transition-transform motion-safe:group-hover:translate-x-0.5" />
        </a>
    </div>
</x-ui.card>
