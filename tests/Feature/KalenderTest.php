<?php

use App\Enums\PelatihanStatus;
use App\Models\Pelatihan;
use App\Models\PelatihanCategory;
use Livewire\Livewire;

test('guests can visit the calendar page', function () {
    $this->get(route('kalender.index'))->assertOk();
});

test('calendar shows a published and active pelatihan scheduled this month', function () {
    Pelatihan::factory()->create([
        'title' => 'Pelatihan Bulan Ini',
        'status' => PelatihanStatus::Published,
        'is_active' => true,
        'start_date' => now()->startOfMonth()->addDays(5),
    ]);

    $this->get(route('kalender.index'))->assertSee('Pelatihan Bulan Ini');
});

test('calendar hides draft pelatihan', function () {
    Pelatihan::factory()->create([
        'title' => 'Pelatihan Draft Tersembunyi',
        'status' => PelatihanStatus::Draft,
        'is_active' => true,
        'start_date' => now()->startOfMonth()->addDays(5),
    ]);

    $this->get(route('kalender.index'))->assertDontSee('Pelatihan Draft Tersembunyi');
});

test('calendar hides inactive pelatihan', function () {
    Pelatihan::factory()->create([
        'title' => 'Pelatihan Nonaktif Tersembunyi',
        'status' => PelatihanStatus::Published,
        'is_active' => false,
        'start_date' => now()->startOfMonth()->addDays(5),
    ]);

    $this->get(route('kalender.index'))->assertDontSee('Pelatihan Nonaktif Tersembunyi');
});

test('calendar hides pelatihan from a different month', function () {
    Pelatihan::factory()->create([
        'title' => 'Pelatihan Bulan Lain',
        'status' => PelatihanStatus::Published,
        'is_active' => true,
        'start_date' => now()->addMonths(3),
    ]);

    $this->get(route('kalender.index'))->assertDontSee('Pelatihan Bulan Lain');
});

test('can navigate to the next and previous month', function () {
    $component = Livewire::test('pages::public.kalender');

    $currentMonth = (int) now()->month;
    $currentYear = (int) now()->year;

    $component->call('nextMonth');
    $expectedNext = now()->addMonth();
    $component->assertSet('month', $expectedNext->month)
        ->assertSet('year', $expectedNext->year);

    $component->call('previousMonth')->call('previousMonth');
    $expectedPrev = now()->subMonth();
    $component->assertSet('month', $expectedPrev->month)
        ->assertSet('year', $expectedPrev->year);

    $component->call('goToToday')
        ->assertSet('month', $currentMonth)
        ->assertSet('year', $currentYear);
});

test('can filter the calendar by category', function () {
    $categoryA = PelatihanCategory::factory()->create(['name' => 'Kategori A', 'slug' => 'kategori-a']);
    $categoryB = PelatihanCategory::factory()->create(['name' => 'Kategori B', 'slug' => 'kategori-b']);

    Pelatihan::factory()->create([
        'title' => 'Pelatihan Kategori A',
        'pelatihan_category_id' => $categoryA->id,
        'status' => PelatihanStatus::Published,
        'is_active' => true,
        'start_date' => now()->startOfMonth()->addDays(5),
    ]);

    Pelatihan::factory()->create([
        'title' => 'Pelatihan Kategori B',
        'pelatihan_category_id' => $categoryB->id,
        'status' => PelatihanStatus::Published,
        'is_active' => true,
        'start_date' => now()->startOfMonth()->addDays(6),
    ]);

    Livewire::test('pages::public.kalender')
        ->set('category', 'kategori-a')
        ->assertSee('Pelatihan Kategori A')
        ->assertDontSee('Pelatihan Kategori B');
});

test('defaults to the calendar tab and can switch to the agenda tab', function () {
    Pelatihan::factory()->create([
        'title' => 'Pelatihan Tab Agenda',
        'status' => PelatihanStatus::Published,
        'is_active' => true,
        'start_date' => now()->startOfMonth()->addDays(5),
    ]);

    Livewire::test('pages::public.kalender')
        ->assertSet('view', 'kalender')
        ->assertSee('Pelatihan Tab Agenda')
        ->set('view', 'agenda')
        ->assertSee('Pelatihan Tab Agenda');
});

test('shows empty state on the agenda tab when no pelatihan is scheduled this month', function () {
    Livewire::test('pages::public.kalender')
        ->set('view', 'agenda')
        ->assertSee('Belum ada pelatihan dijadwalkan di bulan ini.');
});
