<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::livewire('konten/faq', 'pages::faqs.index')->name('faqs.index');
    Route::livewire('konten/testimoni', 'pages::testimonials.index')->name('testimonials.index');
});
