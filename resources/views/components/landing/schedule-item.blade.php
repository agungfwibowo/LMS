@props([
    'title' => '',
    'category' => '',
    'day' => '',
    'month' => '',
    'mode' => '',
    'quota' => null,
    'status' => 'Dibuka',
])

<div class="flex items-center gap-6 rounded-2xl border border-zinc-100 bg-white p-5 transition-all hover:border-brand-100 hover:shadow-lg dark:border-zinc-800 dark:bg-zinc-900">
    {{-- Date box --}}
    <div class="flex shrink-0 flex-col items-center justify-center rounded-xl bg-brand-50 px-4 py-3 text-center min-w-18 dark:bg-teal-900/40">
        <div class="font-heading text-2xl font-extrabold leading-none text-brand-800">{{ $day }}</div>
        <div class="mt-1 text-[12px] font-bold uppercase tracking-[0.03em] text-brand-600">{{ $month }}</div>
    </div>

    {{-- Info --}}
    <div class="min-w-0 flex-1">
        <div class="mb-1 flex flex-wrap items-center gap-2">
            <span class="rounded bg-brand-50 px-2 py-0.5 text-[11.5px] font-bold text-brand-800 dark:bg-teal-900/40 dark:text-teal-300">{{ $category }}</span>
            @if ($mode)
                <span class="text-sm text-zinc-500 dark:text-zinc-400">{{ $mode }}</span>
            @endif
        </div>
        <h3 class="font-heading font-bold text-brand-950 dark:text-white">{{ $title }}</h3>
    </div>

    {{-- Action --}}
    <div class="flex shrink-0 flex-col items-end gap-2">
        @if ($quota)
            <div class="text-sm text-zinc-500 dark:text-zinc-400">{{ $quota }}</div>
        @endif
        @if ($status !== 'Penuh')
            <a href="{{ route('register') }}" class="inline-block rounded-lg bg-lime px-4 py-2 text-[13.5px] font-bold text-brand-950 transition-opacity hover:opacity-90">Daftar</a>
        @else
            <span class="inline-block rounded-lg bg-zinc-100 px-4 py-2 text-[13.5px] font-bold text-zinc-400 dark:bg-zinc-800">Penuh</span>
        @endif
    </div>
</div>
