@php
    $breadcrumbs = match (true) {
        request()->routeIs('posts.create') => [
            ['label' => 'Berita', 'href' => route('posts.index')],
            ['label' => 'Tambah Berita'],
        ],
        request()->routeIs('posts.edit') => [
            ['label' => 'Berita', 'href' => route('posts.index')],
            ['label' => 'Edit Berita'],
        ],
        request()->routeIs('posts.*') => [
            ['label' => 'Berita'],
        ],
        request()->routeIs('categories.*') => [
            ['label' => 'Berita', 'href' => route('posts.index')],
            ['label' => 'Kategori'],
        ],
        request()->routeIs('tags.*') => [
            ['label' => 'Berita', 'href' => route('posts.index')],
            ['label' => 'Tags'],
        ],
        request()->routeIs('pelatihan.create') => [
            ['label' => 'Pelatihan', 'href' => route('pelatihan.index')],
            ['label' => 'Tambah Pelatihan'],
        ],
        request()->routeIs('pelatihan.edit') => [
            ['label' => 'Pelatihan', 'href' => route('pelatihan.index')],
            ['label' => 'Edit Pelatihan'],
        ],
        request()->routeIs('pelatihan.*') => [
            ['label' => 'Pelatihan'],
        ],
        request()->routeIs('pelatihan-categories.*') => [
            ['label' => 'Pelatihan', 'href' => route('pelatihan.index')],
            ['label' => 'Kategori'],
        ],
        request()->routeIs('faqs.*') => [
            ['label' => 'FAQ'],
        ],
        request()->routeIs('testimonials.*') => [
            ['label' => 'Testimoni'],
        ],
        request()->routeIs('profile.*'), request()->routeIs('settings.*') => [
            ['label' => 'Pengaturan'],
        ],
        request()->routeIs('dashboard') => [
            ['label' => 'Dashboard'],
        ],
        default => [],
    };
@endphp

@if (count($breadcrumbs))
    <flux:breadcrumbs>
        @foreach ($breadcrumbs as $crumb)
            @if (isset($crumb['href']))
                <flux:breadcrumbs.item href="{{ $crumb['href'] }}" wire:navigate>{{ $crumb['label'] }}</flux:breadcrumbs.item>
            @else
                <flux:breadcrumbs.item>{{ $crumb['label'] }}</flux:breadcrumbs.item>
            @endif
        @endforeach
    </flux:breadcrumbs>
@endif
