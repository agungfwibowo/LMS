<?php

use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->prefix('admin')->group(function () {
    Route::redirect('settings', '/admin/settings/profile');

    Route::livewire('settings/profile', 'pages::settings.profile')->name('profile.edit');
});

Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::livewire('settings/appearance', 'pages::settings.appearance')->name('appearance.edit');

    Route::livewire('settings/security', 'pages::settings.security')
        ->middleware([
            'password.confirm',
        ])
        ->name('security.edit');
});
