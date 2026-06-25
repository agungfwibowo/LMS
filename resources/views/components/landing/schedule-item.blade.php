@props([
    'title' => '',
    'category' => '',
    'date' => '',
    'time' => '',
    'quota' => null,
    'status' => 'Dibuka', // Dibuka | Hampir Penuh | Penuh
])

@php
    $statusStyles = [
        'Dibuka' => 'bg-brand-50 text-brand-700 dark:bg-brand-500/10 dark:text-brand-400',
        'Hampir Penuh' => 'bg-amber-50 text-amber-700 dark:bg-amber-500/10 dark:text-amber-400',
        'Penuh' => 'bg-zinc-100 text-zinc-500 dark:bg-zinc-800 dark:text-zinc-400',
    ];
    $badge = $statusStyles[$status] ?? $statusStyles['Dibuka'];
@endphp

<div class="flex flex-col gap-4 rounded-2xl border border-zinc-200 bg-white p-5 transition-colors hover:border-brand-300 sm:flex-row sm:items-center sm:justify-between dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-brand-700">
    <div class="flex items-start gap-4">
        <div class="flex size-12 shrink-0 flex-col items-center justify-center rounded-xl bg-brand-600 text-white">
            <flux:icon name="calendar-days" class="size-6" />
        </div>
        <div>
            <span class="text-xs font-semibold uppercase tracking-wide text-brand-600 dark:text-brand-400">{{ $category }}</span>
            <h3 class="mt-0.5 text-base font-semibold text-zinc-900 dark:text-white">{{ $title }}</h3>
            <div class="mt-1 flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-zinc-500 dark:text-zinc-400">
                <span class="inline-flex items-center gap-1"><flux:icon name="calendar" class="size-4" /> {{ $date }}</span>
                <span class="inline-flex items-center gap-1"><flux:icon name="clock" class="size-4" /> {{ $time }}</span>
                @if (! is_null($quota))
                    <span class="inline-flex items-center gap-1"><flux:icon name="users" class="size-4" /> {{ $quota }}</span>
                @endif
            </div>
        </div>
    </div>

    <div class="flex items-center gap-3 sm:flex-col sm:items-end">
        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $badge }}">{{ $status }}</span>
        @if ($status !== 'Penuh')
            <a href="{{ route('register') }}" class="text-sm font-semibold text-brand-600 hover:text-brand-700 dark:text-brand-400">Daftar &rarr;</a>
        @endif
    </div>
</div>
