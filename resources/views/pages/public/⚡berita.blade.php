<?php

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.guest'), Title('Berita & Pengumuman')] class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $category = '';

    public function updated(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function posts()
    {
        return Post::query()
            ->where('status', PostStatus::Published)
            ->with(['categories'])
            ->when($this->search, fn($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->category, fn($q) => $q->whereHas('categories', fn($q) => $q->where('categories.slug', $this->category)))
            ->latest('published_at')
            ->paginate(12);
    }

    #[Computed]
    public function categories()
    {
        return Category::whereHas('posts', fn($q) => $q->where('status', PostStatus::Published))
            ->orderBy('name')
            ->get();
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
        <nav class="mb-4 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
            <a href="{{ route('home') }}" class="hover:text-brand-600 dark:hover:text-brand-400">Beranda</a>
            <flux:icon name="chevron-right" class="size-4" />
            <span class="font-medium text-zinc-900 dark:text-white">Berita</span>
        </nav>

        <x-landing.section-heading
            :center="false"
            eyebrow="Berita & Pengumuman"
            title="Kabar Terbaru Diklat"
            subtitle="Informasi pembukaan pelatihan, pengumuman, dan capaian terbaru RS Adam Malik." />
    </div>
</section>

{{-- ================= FILTER ================= --}}
<section class="sticky top-16 z-40 border-b border-zinc-200 bg-white/90 backdrop-blur dark:border-zinc-800 dark:bg-zinc-950/90">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-4 py-4 sm:flex-row sm:items-center sm:justify-between">
            {{-- Search --}}
            <div class="relative w-full sm:max-w-xs">
                <div class="pointer-events-none absolute inset-y-0 left-3 flex items-center">
                    <flux:icon name="magnifying-glass" class="size-4 text-zinc-400" />
                </div>
                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari berita..."
                    class="w-full rounded-xl border border-zinc-300 bg-white py-2 pl-9 pr-4 text-sm text-zinc-900 placeholder-zinc-400 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20 dark:border-zinc-700 dark:bg-zinc-900 dark:text-white dark:placeholder-zinc-500"
                />
            </div>

            {{-- Category pills --}}
            <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                <button
                    wire:click="$set('category', '')"
                    class="shrink-0 rounded-full border px-4 py-1.5 text-xs font-semibold transition-colors {{ $category === '' ? 'border-brand-600 bg-brand-600 text-white' : 'border-zinc-300 bg-white text-zinc-600 hover:border-brand-400 hover:text-brand-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300' }}"
                >
                    Semua
                </button>
                @foreach ($this->categories as $cat)
                    <button
                        wire:click="$set('category', '{{ $cat->slug }}')"
                        class="shrink-0 rounded-full border px-4 py-1.5 text-xs font-semibold transition-colors {{ $category === $cat->slug ? 'border-brand-600 bg-brand-600 text-white' : 'border-zinc-300 bg-white text-zinc-600 hover:border-brand-400 hover:text-brand-600 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-300' }}"
                    >
                        {{ $cat->name }}
                    </button>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ================= POSTS GRID ================= --}}
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
    @if ($this->posts->isEmpty())
        <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="flex size-16 items-center justify-center rounded-2xl bg-zinc-100 dark:bg-zinc-800">
                <flux:icon name="newspaper" class="size-8 text-zinc-400" />
            </div>
            <p class="mt-4 text-base font-semibold text-zinc-700 dark:text-zinc-300">
                {{ $search || $category ? 'Tidak ada berita yang cocok.' : 'Belum ada berita.' }}
            </p>
            @if ($search || $category)
                <button wire:click="$set('search', ''); $set('category', '')" class="mt-3 text-sm text-brand-600 hover:underline dark:text-brand-400">
                    Hapus filter
                </button>
            @endif
        </div>
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($this->posts as $post)
                <x-landing.news-card
                    :title="$post->title"
                    :category="$post->categories->first()?->name ?? 'Berita'"
                    :date="($post->published_at ?? $post->created_at)->translatedFormat('d M Y')"
                    :excerpt="$post->excerpt ?? Str::limit(strip_tags($post->content), 120)"
                    :image="$post->featured_image ? Storage::url($post->featured_image) : null"
                    :href="route('berita.show', $post->slug)"
                />
            @endforeach
        </div>

        <div class="mt-10">
            {{ $this->posts->links() }}
        </div>
    @endif
</section>
</div>
