<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'landing-page')->name('home');

Route::redirect('admin', 'admin/dashboard');

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/berita.php';
require __DIR__.'/konten.php';
