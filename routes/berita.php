<?php

use App\Livewire\Actions\Category;
use App\Livewire\Actions\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::livewire('/berita', 'pages::public.berita')->name('berita.index');
Route::livewire('/berita/{slug}', 'pages::public.berita-show')->name('berita.show');

Route::middleware(['auth', 'verified', 'approved', 'admin'])->prefix('admin')->group(function () {
    Route::post('berita/upload-gambar', function (Request $request) {
        $request->validate(['file' => ['required', 'image', 'max:5120']]);
        $path = $request->file('file')->store('uploads', 'public');

        return response()->json(['url' => asset('storage/'.$path)]);
    })->name('posts.upload');
    Route::livewire('berita', 'pages::posts.index')->name('posts.index');
    Route::livewire('berita/tambah', 'pages::posts.form')->name('posts.create');
    Route::livewire('berita/kategori', Category::class)->name('categories.index');
    Route::livewire('berita/tags', Tag::class)->name('tags.index');
    Route::livewire('berita/{post}', 'pages::posts.form')->name('posts.edit');
});
