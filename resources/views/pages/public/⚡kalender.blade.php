<?php

use App\Models\Pelatihan;
use App\Models\PelatihanCategory;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

new #[Layout('layouts.guest'), Title('Kalender Pelatihan')] class extends Component {
    #[Url]
    public int $month = 0;

    #[Url]
    public int $year = 0;

    #[Url]
    public string $category = '';

    #[Url]
    public string $view = 'kalender';

    public ?int $selectedDay = null;

    public function mount(): void
    {
        $this->month = $this->month ?: (int) now()->month;
        $this->year = $this->year ?: (int) now()->year;
    }

    public function previousMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->subMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function nextMonth(): void
    {
        $date = Carbon::create($this->year, $this->month, 1)->addMonth();
        $this->month = $date->month;
        $this->year = $date->year;
    }

    public function goToToday(): void
    {
        $this->month = (int) now()->month;
        $this->year = (int) now()->year;
    }

    #[Computed]
    public function categories()
    {
        return PelatihanCategory::whereHas('pelatihans', fn ($query) => $query->active()->published())
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function pelatihans()
    {
        return Pelatihan::with('category')
            ->active()
            ->published()
            ->whereYear('start_date', $this->year)
            ->whereMonth('start_date', $this->month)
            ->when($this->category, fn ($query) => $query->whereHas('category', fn ($query) => $query->where('slug', $this->category)))
            ->orderBy('start_date')
            ->get();
    }

    #[Computed]
    public function calendarWeeks(): array
    {
        $firstOfMonth = Carbon::create($this->year, $this->month, 1);
        $daysInMonth = $firstOfMonth->daysInMonth;
        $leadingBlanks = $firstOfMonth->dayOfWeekIso - 1;

        $eventsByDay = $this->pelatihans->groupBy(fn ($pelatihan) => $pelatihan->start_date->day);

        $cells = array_fill(0, $leadingBlanks, null);

        for ($day = 1; $day <= $daysInMonth; $day++) {
            $cells[] = ['day' => $day, 'events' => $eventsByDay->get($day, collect())];
        }

        while (count($cells) % 7 !== 0) {
            $cells[] = null;
        }

        return array_chunk($cells, 7);
    }

    #[Computed]
    public function monthLabel(): string
    {
        return Carbon::create($this->year, $this->month, 1)->translatedFormat('F Y');
    }

    public function showDay(int $day): void
    {
        $this->selectedDay = $day;
        $this->modal('day-agenda')->show();
    }

    #[Computed]
    public function selectedDayEvents()
    {
        if (! $this->selectedDay) {
            return collect();
        }

        return $this->pelatihans->filter(fn ($pelatihan) => $pelatihan->start_date->day === $this->selectedDay)->values();
    }

    #[Computed]
    public function selectedDayLabel(): string
    {
        if (! $this->selectedDay) {
            return '';
        }

        return Carbon::create($this->year, $this->month, $this->selectedDay)->translatedFormat('d F Y');
    }

    public function modeLabel(Pelatihan $pelatihan): string
    {
        $label = match ($pelatihan->mode) {
            'offline' => 'Luring',
            'online' => 'Daring',
            default => 'Hybrid',
        };

        return $pelatihan->location ? "{$label} · {$pelatihan->location}" : $label;
    }
}; ?>

<div>
{{-- ================= PAGE HERO ================= --}}
<section class="relative overflow-hidden border-b border-zinc-200 pt-16 dark:border-zinc-800">
    <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
        <div class="absolute inset-0 bg-gradient-to-b from-brand-50/80 to-white dark:from-brand-950/30 dark:to-zinc-950"></div>
        <div class="absolute -right-24 -top-24 size-96 rounded-full bg-brand-200/40 blur-3xl dark:bg-brand-800/20"></div>
    </div>

    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <nav
            x-data="{ shown: false }" x-intersect.once="shown = true"
            data-reveal="from-left" x-bind:class="shown && 'revealed'"
            class="mb-4 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400"
        >
            <a href="{{ route('home') }}" class="hover:text-brand-600 dark:hover:text-brand-400">Beranda</a>
            <flux:icon name="chevron-right" class="size-4" />
            <span class="font-medium text-zinc-900 dark:text-white">Kalender Pelatihan</span>
        </nav>

        <x-landing.section-heading
            :center="false"
            eyebrow="Jadwal Mendatang"
            title="Kalender Pelatihan"
            subtitle="Telusuri jadwal pelatihan per bulan dan daftar sebelum kuota penuh."
            x-data="{ shown: false }" x-intersect.once="shown = true"
            data-reveal x-bind:class="shown && 'revealed'"
            style="transition-delay:100ms" />
    </div>
</section>

{{-- ================= FILTER & NAVIGASI BULAN ================= --}}
<section class="sticky top-16 z-40 border-b border-zinc-200 bg-white/90 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/90">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <flux:button wire:click="previousMonth" variant="ghost" size="sm" icon="chevron-left" />
                <h2 class="min-w-40 text-center font-heading text-lg font-bold text-brand-950 dark:text-white">
                    {{ $this->monthLabel }}
                </h2>
                <flux:button wire:click="nextMonth" variant="ghost" size="sm" icon="chevron-right" />
                <flux:button wire:click="goToToday" variant="ghost" size="sm">Hari Ini</flux:button>
            </div>

            <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                <button
                    wire:click="$set('category', '')"
                    wire:loading.attr="disabled"
                    class="group relative shrink-0 rounded-full border px-4 py-1.5 text-xs font-semibold transition-colors {{ $category === '' ? 'border-brand-600 bg-brand-600 text-white' : 'border-zinc-300 bg-white text-zinc-600 hover:border-brand-400 hover:text-brand-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300' }}"
                >
                    <span class="transition-opacity group-data-loading:opacity-30">Semua</span>
                    <flux:icon.loading class="absolute left-1/2 top-1/2 hidden size-4 -translate-x-1/2 -translate-y-1/2 group-data-loading:block" />
                </button>
                @foreach ($this->categories as $cat)
                    <button
                        wire:click="$set('category', '{{ $cat->slug }}')"
                        wire:loading.attr="disabled"
                        class="group relative shrink-0 rounded-full border px-4 py-1.5 text-xs font-semibold transition-colors {{ $category === $cat->slug ? 'border-brand-600 bg-brand-600 text-white' : 'border-zinc-300 bg-white text-zinc-600 hover:border-brand-400 hover:text-brand-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300' }}"
                    >
                        <span class="transition-opacity group-data-loading:opacity-30">{{ $cat->name }}</span>
                        <flux:icon.loading class="absolute left-1/2 top-1/2 hidden size-4 -translate-x-1/2 -translate-y-1/2 group-data-loading:block" />
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ================= TAB: KALENDER / AGENDA ================= --}}
{{-- Perpindahan tab murni client-side (Alpine) supaya instan tanpa round-trip ke server. --}}
{{-- URL ?view= tetap disinkronkan lewat replaceState agar reload/bookmark konsisten. --}}
<section
    class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8"
    x-data="{ view: @js($view) }"
    x-init="$watch('view', v => { const u = new URL(location); u.searchParams.set('view', v); history.replaceState({}, '', u); })"
>
    <div
        role="tablist"
        aria-label="Tampilan kalender"
        class="mb-8 inline-flex rounded-full border border-zinc-200 bg-zinc-100 p-1 dark:border-zinc-800 dark:bg-zinc-900"
    >
        <button
            type="button"
            role="tab"
            :aria-selected="view === 'kalender'"
            @click="view = 'kalender'"
            class="inline-flex items-center gap-2 rounded-full px-5 py-2 text-sm font-semibold transition-colors"
            :class="view === 'kalender' ? 'bg-white text-brand-700 shadow-sm dark:bg-zinc-800 dark:text-teal-300' : 'text-zinc-500 hover:text-brand-600 dark:text-zinc-400 dark:hover:text-teal-400'"
        >
            <flux:icon name="calendar-days" class="size-4" />
            Kalender
        </button>
        <button
            type="button"
            role="tab"
            :aria-selected="view === 'agenda'"
            @click="view = 'agenda'"
            class="inline-flex items-center gap-2 rounded-full px-5 py-2 text-sm font-semibold transition-colors"
            :class="view === 'agenda' ? 'bg-white text-brand-700 shadow-sm dark:bg-zinc-800 dark:text-teal-300' : 'text-zinc-500 hover:text-brand-600 dark:text-zinc-400 dark:hover:text-teal-400'"
        >
            <flux:icon name="list-bullet" class="size-4" />
            Agenda
        </button>
    </div>

    {{-- ================= PANEL: GRID KALENDER ================= --}}
    <div role="tabpanel" x-show="view === 'kalender'" x-cloak>
    <div
        x-data="{ shown: false }" x-intersect.once="shown = true"
        data-reveal x-bind:class="shown && 'revealed'"
        class="overflow-x-auto rounded-2xl border border-zinc-200 dark:border-zinc-800"
    >
        <div class="min-w-[720px]">
            {{-- Header hari --}}
            <div class="grid grid-cols-7 border-b border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-900">
                @foreach (['Sen', 'Sel', 'Rab', 'Kam', 'Jum', 'Sab', 'Min'] as $label)
                    <div class="px-3 py-2.5 text-center text-xs font-bold uppercase tracking-wide text-zinc-500 dark:text-zinc-400">
                        {{ $label }}
                    </div>
                @endforeach
            </div>

            {{-- Kotak tanggal — ukuran selalu konsisten, konten dipotong (overflow-hidden), tidak ikut melar --}}
            <div class="grid grid-cols-7">
                @foreach ($this->calendarWeeks as $week)
                    @foreach ($week as $cell)
                        <div class="h-28 overflow-hidden border-b border-r border-zinc-100 p-1.5 [&:nth-child(7n)]:border-r-0 dark:border-zinc-800">
                            @if ($cell)
                                @php $isToday = Carbon::create($year, $month, $cell['day'])->isToday(); @endphp
                                <div class="flex h-full flex-col gap-1">
                                    <span class="inline-flex size-6 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $isToday ? 'bg-brand-600 text-white' : 'text-zinc-500 dark:text-zinc-400' }}">
                                        {{ $cell['day'] }}
                                    </span>
                                    <div class="flex min-w-0 flex-1 flex-col gap-1 overflow-hidden">
                                        @foreach ($cell['events']->take(2) as $event)
                                            <flux:tooltip :content="$event->title">
                                                <button
                                                    type="button"
                                                    wire:click="showDay({{ $cell['day'] }})"
                                                    class="w-full truncate rounded bg-brand-50 px-1.5 py-0.5 text-left text-[11px] font-semibold text-brand-700 transition-colors hover:bg-brand-100 dark:bg-teal-900/40 dark:text-teal-300 dark:hover:bg-teal-900/60"
                                                >
                                                    {{ $event->title }}
                                                </button>
                                            </flux:tooltip>
                                        @endforeach
                                        @if ($cell['events']->count() > 2)
                                            <button
                                                type="button"
                                                wire:click="showDay({{ $cell['day'] }})"
                                                class="w-full truncate text-left text-[11px] font-medium text-zinc-400 hover:text-brand-600 dark:hover:text-teal-400"
                                            >
                                                +{{ $cell['events']->count() - 2 }} lainnya
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @endforeach
            </div>
        </div>
    </div>
    </div>

    {{-- ================= PANEL: AGENDA BULAN INI ================= --}}
    <div role="tabpanel" x-show="view === 'agenda'" x-cloak>
    <div x-data="{ shown: false }" x-intersect.once="shown = true">
        <h3 data-reveal x-bind:class="shown && 'revealed'" class="mb-5 font-heading text-xl font-bold text-brand-950 dark:text-white">
            Agenda {{ $this->monthLabel }}
        </h3>

        <div class="flex flex-col gap-3.5">
            @forelse ($this->pelatihans as $index => $sesi)
                <x-landing.schedule-item
                    :title="$sesi->title"
                    :category="$sesi->category?->name ?? 'Umum'"
                    :day="$sesi->start_date->format('d')"
                    :month="$sesi->start_date->translatedFormat('M')"
                    :mode="$this->modeLabel($sesi)"
                    :quota="$sesi->quota ? $sesi->quota.' kuota tersedia' : null"
                    :status="$sesi->quota === 0 ? 'Penuh' : 'Dibuka'"
                    data-reveal
                    x-bind:class="shown && 'revealed'"
                    style="transition-delay:{{ $index * 60 }}ms"
                />
            @empty
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="flex size-16 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800">
                        <flux:icon name="calendar-days" class="size-8 text-zinc-400" />
                    </div>
                    <p class="mt-4 text-base font-semibold text-zinc-700 dark:text-zinc-300">
                        Belum ada pelatihan dijadwalkan di bulan ini.
                    </p>
                </div>
            @endforelse
        </div>
    </div>
    </div>
</section>

{{-- ================= MODAL DETAIL HARI ================= --}}
<flux:modal name="day-agenda" class="w-full max-w-lg !p-0">
    <div class="flex max-h-[80vh] flex-col">
        <x-modal.header :title="$this->selectedDayLabel" />
        <div class="flex flex-1 flex-col gap-3.5 overflow-y-auto p-4">
            @forelse ($this->selectedDayEvents as $sesi)
                <x-landing.schedule-item
                    :title="$sesi->title"
                    :category="$sesi->category?->name ?? 'Umum'"
                    :day="$sesi->start_date->format('d')"
                    :month="$sesi->start_date->translatedFormat('M')"
                    :mode="$this->modeLabel($sesi)"
                    :quota="$sesi->quota ? $sesi->quota.' kuota tersedia' : null"
                    :status="$sesi->quota === 0 ? 'Penuh' : 'Dibuka'"
                />
            @empty
                <p class="py-6 text-center text-sm text-zinc-500 dark:text-zinc-400">
                    Tidak ada pelatihan pada tanggal ini.
                </p>
            @endforelse
        </div>
    </div>
</flux:modal>
</div>
