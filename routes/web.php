<?php

use App\Livewire\Actions\UserApproval;
use Illuminate\Support\Facades\Route;

Route::view('/', 'landing-page')->name('home');

Route::redirect('admin', 'admin/dashboard');

Route::get('menunggu-persetujuan', function () {
    return auth()->user()->isApproved()
        ? redirect()->route('dashboard')
        : view('pages.auth.pending-approval');
})->middleware('auth')->name('approval.pending');

Route::middleware(['auth', 'verified', 'approved'])->prefix('admin')->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');

    Route::livewire('pengguna', UserApproval::class)
        ->middleware('admin')
        ->name('users.index');
});

require __DIR__.'/settings.php';
require __DIR__.'/berita.php';
require __DIR__.'/konten.php';
require __DIR__.'/pelatihan.php';
