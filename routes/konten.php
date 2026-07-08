<?php

use App\Livewire\Actions\Faq;
use App\Livewire\Actions\Testimonial;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified', 'approved', 'admin'])->prefix('admin')->group(function () {
    Route::livewire('konten/faq', Faq::class)->name('faqs.index');
    Route::livewire('konten/testimoni', Testimonial::class)->name('testimonials.index');
});
