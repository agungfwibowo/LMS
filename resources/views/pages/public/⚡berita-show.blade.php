<?php

use App\Enums\PostStatus;
use App\Models\Post;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts.guest')] class extends Component {
    public Post $post;

    public function mount(string $slug): void
    {
        $this->post = Post::with(['categories', 'tags', 'author'])
            ->where('slug', $slug)
            ->where('status', PostStatus::Published)
            ->firstOrFail();
    }

    public function title(): string
    {
        return $this->post->title;
    }

    #[Computed]
    public function prevPost(): ?Post
    {
        $date = $this->post->published_at ?? $this->post->created_at;

        return Post::with('categories')
            ->where('status', PostStatus::Published)
            ->where('id', '!=', $this->post->id)
            ->where(fn ($q) => $q
                ->where('published_at', '<', $date)
                ->orWhere(fn ($q2) => $q2->whereNull('published_at')->where('created_at', '<', $date))
            )
            ->latest('published_at')
            ->latest('created_at')
            ->first();
    }

    #[Computed]
    public function nextPost(): ?Post
    {
        $date = $this->post->published_at ?? $this->post->created_at;

        return Post::with('categories')
            ->where('status', PostStatus::Published)
            ->where('id', '!=', $this->post->id)
            ->where(fn ($q) => $q
                ->where('published_at', '>', $date)
                ->orWhere(fn ($q2) => $q2->whereNull('published_at')->where('created_at', '>', $date))
            )
            ->oldest('published_at')
            ->oldest('created_at')
            ->first();
    }
}; ?>

<div>
{{-- ================= HERO ================= --}}
<div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10 max-w-full overflow-hidden">
    <div class="absolute inset-0 bg-gradient-to-b from-brand-50/80 to-white dark:from-brand-950/30 dark:to-zinc-950"></div>
    <div class="absolute -right-24 -top-24 size-96 rounded-full bg-brand-200/40 blur-3xl dark:bg-brand-800/20"></div>
</div>
<section class="relative overflow-hidden pt-16 dark:border-zinc-800">
    <div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        {{-- Breadcrumb --}}
        <nav class="mb-6 flex items-center gap-2 text-sm text-zinc-500 dark:text-zinc-400">
            <a href="{{ route('home') }}" class="hover:text-brand-600 dark:hover:text-brand-400">Beranda</a>
            <flux:icon name="chevron-right" class="size-4" />
            <a href="{{ route('berita.index') }}" class="hover:text-brand-600 dark:hover:text-brand-400">Berita</a>
            <flux:icon name="chevron-right" class="size-4" />
            <span class="max-w-xs truncate font-medium text-zinc-900 dark:text-white">{{ Str::limit($post->title, 40) }}</span>
        </nav>

        {{-- Category + tags row --}}
        <div class="mb-4 flex flex-wrap items-center gap-2">
            @foreach ($post->categories as $cat)
                <a href="{{ route('berita.index', ['category' => $cat->slug]) }}"
                   class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 ring-1 ring-brand-200 transition-colors hover:bg-brand-100 dark:bg-brand-500/10 dark:text-brand-400 dark:ring-brand-500/20">
                    <flux:icon name="tag" class="size-3.5" />
                    {{ $cat->name }}
                </a>
            @endforeach
        </div>

        {{-- Title --}}
        <h1 class="text-3xl font-bold leading-tight tracking-tight text-zinc-900 sm:text-4xl dark:text-white">
            {{ $post->title }}
        </h1>

        {{-- Meta --}}
        <div class="mt-5 flex flex-wrap items-center gap-4 text-sm text-zinc-500 dark:text-zinc-400">
            @if ($post->author)
                <span class="flex items-center gap-1.5">
                    <span class="flex size-6 items-center justify-center rounded-full bg-brand-600 text-xs font-semibold text-white">
                        {{ $post->author->initials ?? strtoupper(substr($post->author->name, 0, 1)) }}
                    </span>
                    {{ $post->author->name }}
                </span>
                <span class="hidden sm:inline">·</span>
            @endif
            <span class="flex items-center gap-1.5">
                <flux:icon name="calendar" class="size-4" />
                {{ ($post->published_at ?? $post->created_at)->translatedFormat('d F Y') }}
            </span>
            @if ($post->tags->isNotEmpty())
                <span class="hidden sm:inline">·</span>
                <span class="flex items-center gap-1.5">
                    <flux:icon name="hashtag" class="size-4" />
                    {{ $post->tags->pluck('name')->join(', ') }}
                </span>
            @endif
        </div>

    </div>
</section>

{{-- ================= FEATURED IMAGE ================= --}}
@if ($post->featured_image)
    <div class="mx-auto max-w-4xl px-4 pt-8 sm:px-6 lg:px-8">
        <div class="overflow-hidden rounded-2xl border border-zinc-200 shadow-md dark:border-zinc-800">
            <img
                src="{{ Storage::url($post->featured_image) }}"
                alt="{{ $post->title }}"
                class="aspect-video w-full object-cover"
            >
        </div>
    </div>
@endif

{{-- ================= CONTENT ================= --}}
<article
    class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8"
    x-data="{ lightbox: null }"
>

    {{-- Excerpt / lead --}}
    @if ($post->excerpt)
        <!-- <p class="mb-8 text-lg font-medium leading-relaxed text-zinc-600 dark:text-zinc-300">
            {{ $post->excerpt }}
        </p>
        <hr class="mb-8 border-zinc-200 dark:border-zinc-800"> -->
    @endif

    {{-- Body --}}
    <div
        class="prose-berita text-zinc-700 dark:text-zinc-300"
        @click.capture="
            const a = $event.target.closest('a');
            if (a && a.querySelector('img')) {
                $event.preventDefault();
                lightbox = a.href;
            }
        "
    >
        {!! $post->content !!}
    </div>

    {{-- Lightbox --}}
    <div
        x-show="lightbox"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="lightbox = null"
        @keydown.escape.window="lightbox = null"
        class="fixed inset-0 z-50 flex cursor-zoom-out items-center justify-center bg-black/80 p-4"
        style="display: none"
    >
        <img
            :src="lightbox"
            class="max-h-[90vh] max-w-[90vw] cursor-default rounded-lg object-contain shadow-2xl"
            @click.stop
        >
    </div>

    {{-- ================= SHARE ================= --}}
    <div class="mt-10 border-t border-zinc-200 pt-8 dark:border-zinc-800">
        <x-landing.share-buttons
            :url="route('berita.show', $post->slug)"
            :title="$post->title"
        />
    </div>
</article>

{{-- ================= PREV / NEXT ================= --}}
@if ($this->prevPost || $this->nextPost)
    <nav>
        <div class="mx-auto grid max-w-4xl grid-cols-1 gap-4 px-4 py-8 sm:grid-cols-2 sm:px-6 lg:px-8">

            {{-- Next --}}
            @if ($this->nextPost)
                <a href="{{ route('berita.show', $this->nextPost->slug) }}"
                   class="group flex flex-row items-start gap-4 rounded-2xl border border-zinc-200 bg-white p-5 transition-all hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-brand-700">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 transition-colors group-hover:bg-brand-50 group-hover:text-brand-600 dark:bg-zinc-800 dark:text-zinc-400 dark:group-hover:bg-brand-900/40 dark:group-hover:text-brand-400">
                        <flux:icon name="arrow-left" class="size-5" />
                    </div>
                    <div class="min-w-0 flex-1 text-left">
                        <!-- <p class="mb-1 text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Berikutnya</p> -->
                        @if ($this->nextPost->categories->first())
                            <span class="mb-1 inline-flex items-center justify-end gap-1 text-xs font-semibold text-brand-600 dark:text-brand-400">
                                <flux:icon name="tag" class="size-3" />
                                {{ $this->nextPost->categories->first()->name }}
                            </span>
                        @endif
                        <p class="text-sm font-semibold leading-snug text-zinc-800 group-hover:text-brand-700 dark:text-zinc-100 dark:group-hover:text-brand-400">
                            {{ Str::limit($this->nextPost->title, 65) }}
                        </p>
                    </div>
                </a>
            @else
                <div></div>
            @endif

            {{-- Prev --}}
            @if ($this->prevPost)
                <a href="{{ route('berita.show', $this->prevPost->slug) }}"
                   class="group flex flex-row-reverse items-start gap-4 rounded-2xl border border-zinc-200 bg-white p-5 transition-all hover:-translate-y-0.5 hover:border-brand-300 hover:shadow-md dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-brand-700">
                    <div class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-zinc-100 text-zinc-500 transition-colors group-hover:bg-brand-50 group-hover:text-brand-600 dark:bg-zinc-800 dark:text-zinc-400 dark:group-hover:bg-brand-900/40 dark:group-hover:text-brand-400">
                        <flux:icon name="arrow-right" class="size-5" />
                    </div>
                    <div class="min-w-0 flex-1 text-right">
                        <!-- <p class="mb-1 text-xs font-medium uppercase tracking-wider text-zinc-400 dark:text-zinc-500">Sebelumnya</p> -->
                        @if ($this->prevPost->categories->first())
                            <span class="mb-1 inline-flex items-center gap-1 text-xs font-semibold text-brand-600 dark:text-brand-400">
                                <flux:icon name="tag" class="size-3" />
                                {{ $this->prevPost->categories->first()->name }}
                            </span>
                        @endif
                        <p class="text-sm font-semibold leading-snug text-zinc-800 group-hover:text-brand-700 dark:text-zinc-100 dark:group-hover:text-brand-400">
                            {{ Str::limit($this->prevPost->title, 65) }}
                        </p>
                    </div>
                </a>
            @endif

        </div>
    </nav>
@endif

</div>
