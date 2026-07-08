<?php

use App\Livewire\Actions\Pelatihan;
use App\Livewire\Actions\PelatihanCategory;
use App\Livewire\Actions\PelatihanForm;
use Illuminate\Support\Facades\Route;

Route::livewire('/kalender', 'pages::public.kalender')->name('kalender.index');

Route::middleware(['auth', 'verified', 'approved', 'admin'])->prefix('admin')->group(function () {
    Route::livewire('pelatihan', Pelatihan::class)->name('pelatihan.index');
    Route::livewire('pelatihan/tambah', PelatihanForm::class)->name('pelatihan.create');
    Route::livewire('pelatihan/kategori', PelatihanCategory::class)->name('pelatihan-categories.index');
    Route::livewire('pelatihan/{pelatihan}', PelatihanForm::class)->name('pelatihan.edit');
});
