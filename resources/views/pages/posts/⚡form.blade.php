<?php

use App\Actions\Posts\CreatePost;
use App\Actions\Posts\UpdatePost;
use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public ?Post $post = null;

    public string $title = '';
    public string $slug = '';
    public string $content = '';
    public string $excerpt = '';
    public mixed $featured_image = null;
    public ?string $existingImage = null;
    public ?string $pendingDeleteImage = null;
    public string $status = 'draft';
    public string $publishedAt = '';
    public array $selectedCategories = [];
    public array $tags = [];
    public string $newTag = '';

    public function mount(?Post $post = null): void
    {
        if ($post) {
            $this->post = $post;
            $this->title = $post->title;
            $this->slug = $post->slug;
            $this->content = $post->content ?? '';
            $this->excerpt = $post->excerpt ?? '';
            $this->existingImage = $post->featured_image;
            $this->status = $post->status->value;
            $this->publishedAt = $post->published_at?->format('Y-m-d\TH:i') ?? '';
            $this->selectedCategories = $post->categories->pluck('id')->map(fn ($id) => (string) $id)->toArray();
            $this->tags = $post->tags->pluck('name')->toArray();
        }
    }

    #[Computed]
    public function tagSuggestions(): array
    {
        $query = trim($this->newTag);
        if (strlen($query) < 1) {
            return [];
        }

        return Tag::where('name', 'like', $query . '%')
            ->whereNotIn('name', $this->tags)
            ->orderBy('name')
            ->limit(5)
            ->pluck('name')
            ->toArray();
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    public function updatedTitle(): void
    {
        if (! $this->post) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function addTag(): void
    {
        $tag = trim($this->newTag, " \t\n\r\0\x0B,");
        if ($tag !== '' && ! in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }
        $this->newTag = '';
    }

    public function removeTag(int $index): void
    {
        array_splice($this->tags, $index, 1);
    }

    public function selectTag(string $name): void
    {
        if ($name !== '' && ! in_array($name, $this->tags)) {
            $this->tags[] = $name;
        }
        $this->newTag = '';
    }

    public function openPreview(): void
    {
        $this->modal('post-preview')->show();
    }

    public function removeExistingImage(): void
    {
        $this->pendingDeleteImage = $this->existingImage;
        $this->existingImage = null;
        $this->modal('confirm-remove-image')->close();
    }

    public function save(CreatePost $createPost, UpdatePost $updatePost): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('posts', 'slug')->ignore($this->post?->id)],
            'content' => ['nullable', 'string'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'featured_image' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', Rule::in(array_column(PostStatus::cases(), 'value'))],
            'publishedAt' => ['nullable', 'date'],
            'selectedCategories' => ['nullable', 'array'],
            'selectedCategories.*' => ['integer', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
        ]);

        if ($this->pendingDeleteImage) {
            Storage::disk('public')->delete($this->pendingDeleteImage);
            $this->pendingDeleteImage = null;
        }

        $input = [
            'title' => $this->title,
            'slug' => $this->slug,
            'content' => $this->content,
            'excerpt' => $this->excerpt,
            'featured_image' => $this->featured_image,
            'existing_image' => $this->existingImage,
            'status' => $this->status,
            'published_at' => $this->publishedAt,
            'selected_categories' => $this->selectedCategories,
            'tag_input' => implode(', ', $this->tags),
        ];

        if ($this->post) {
            $updatePost->handle($this->post, $input);
            Flux::toast(variant: 'success', text: 'Berita berhasil diperbarui.');
        } else {
            $post = $createPost->handle($input, Auth::id());
            Flux::toast(variant: 'success', text: 'Berita berhasil disimpan.');
            $this->post = $post;
        }

        $this->redirectRoute('posts.edit', ['post' => $this->post], navigate: true);
    }
    
    public function render()
    {
        return $this->view()->title(
            $this->post
                ? 'Edit Berita - ' . $this->post->title
                : 'Tambah Berita'
        );
    }
}; ?>

<div
    class="flex h-full w-full flex-1 flex-col gap-6"
    x-data="leaveGuard"
    x-effect="showLeaveModal && ($flux.modal('confirm-leave').show(), showLeaveModal = false)"
    @quill-change.window="isDirty = true"
>
    <div class="flex items-center gap-3">
        <flux:button href="{{ route('posts.index') }}" wire:navigate variant="ghost" icon="arrow-left" size="sm" />
        <flux:heading size="xl" level="1">{{ $post ? 'Edit Berita' : 'Tambah Berita' }} 
        </flux:heading>
        <flux:spacer />
        <flux:button
            type="button"
            x-on:click="$wire.openPreview()"
            variant="ghost"
            icon="magnifying-glass"
            size="sm"
        >Preview</flux:button>
    </div>

    <form wire:submit="save" @submit="submitting = true; if (window._quillInstance) { $wire.content = window._quillInstance.root.innerHTML }" @input="isDirty = true" @change="isDirty = true" class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Main content --}}
        <div class="space-y-5 lg:col-span-2">
            <flux:input
                wire:model.live="title"
                label="Judul Berita"
                placeholder="Masukkan judul berita..."
                required
            />
            <div class="space-y-1">
                <flux:label class="pb-2">Slug URL 
                    <flux:link href="{{ url('/berita/' . $slug) }}"
                        target="_blank"
                        class="inline-flex items-center text-xs"
                    >
                        <flux:icon.arrow-up-right variant="mini"/>
                    </flux:link>
                </flux:label>
                <flux:input
                    wire:model="slug"
                    placeholder="judul-berita"
                    required
                />
            </div>

            <div>
                <flux:label>Konten</flux:label>
                <div class="mt-2" wire:ignore>
                    <div
                        x-data="quillEditor"
                        data-content="{{ $content }}"
                        data-upload-url="{{ route('posts.upload') }}"
                    ></div>
                </div>
                @error('content')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </div>

            <flux:textarea
                wire:model="excerpt"
                label="Ringkasan"
                placeholder="Ringkasan singkat berita (maks. 500 karakter)..."
                rows="3"
            />
        </div>

        {{-- Sidebar options --}}
        <div class="space-y-5">
            <flux:fieldset>
                <flux:legend>Status & Publikasi</flux:legend>
                <flux:select wire:model.live="status" label="Status">
                    @foreach (PostStatus::cases() as $s)
                        <flux:select.option value="{{ $s->value }}">{{ $s->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input
                    wire:model="publishedAt"
                    label="Tanggal Publikasi"
                    type="datetime-local"
                />
            </flux:fieldset>

            <flux:fieldset>
                <flux:legend>Gambar Utama</flux:legend>
                @if ($existingImage && ! $featured_image)
                    <div class="relative mb-3">
                        <img
                            src="{{ Storage::url($existingImage) }}"
                            alt="Gambar saat ini"
                            class="h-40 w-full rounded-lg object-cover"
                        >
                        <flux:button
                            x-on:click="$flux.modal('confirm-remove-image').show()"
                            size="xs"
                            variant="danger"
                            icon="x-mark"
                            class="absolute right-1 top-1"
                        />
                    </div>
                @elseif ($featured_image)
                    <img
                        src="{{ $featured_image->temporaryUrl() }}"
                        alt="Preview"
                        class="mb-3 h-40 w-full rounded-lg object-cover"
                    >
                @endif
                <flux:input
                    wire:model="featured_image"
                    type="file"
                    accept="image/*"
                    :label="$post ? ($existingImage ? 'Ganti Gambar' : 'Upload Gambar') : null"
                />
                @error('featured_image')
                    <flux:error>{{ $message }}</flux:error>
                @enderror
            </flux:fieldset>

            <flux:fieldset>
                <flux:legend>Kategori</flux:legend>
                <div class="space-y-2">
                    @forelse ($this->categories as $category)
                        <flux:checkbox
                            wire:model="selectedCategories"
                            :value="(string) $category->id"
                            :label="$category->name"
                        />
                    @empty
                        <flux:text class="text-sm text-zinc-500">
                            Belum ada kategori. <flux:link href="{{ route('categories.index') }}" wire:navigate>Tambah kategori.</flux:link>
                        </flux:text>
                    @endforelse
                </div>
            </flux:fieldset>

            <flux:fieldset>
                <flux:legend>Tags</flux:legend>
                @if (count($tags) > 0)
                    <div class="mb-2 flex flex-wrap gap-1.5">
                        @foreach ($tags as $index => $tag)
                            <span class="inline-flex items-center gap-1 rounded bg-zinc-100 px-2 py-0.5 text-xs font-medium text-zinc-600 ring-1 ring-zinc-200 dark:bg-zinc-700 dark:text-zinc-300 dark:ring-zinc-600">
                                {{ $tag }}
                                <button
                                    wire:click="removeTag({{ $index }})"
                                    type="button"
                                    class="text-zinc-400 transition-colors hover:text-zinc-700 dark:hover:text-zinc-100"
                                >
                                    <flux:icon.x-mark class="size-3" />
                                </button>
                            </span>
                        @endforeach
                    </div>
                @endif
                <div
                    class="relative"
                    x-data="{ open: false, activeIndex: -1 }"
                    x-on:keydown.arrow-down.prevent="if (open) { const max = $el.querySelectorAll('[data-suggestion]').length - 1; if (activeIndex < max) activeIndex++ }"
                    x-on:keydown.arrow-up.prevent="if (open && activeIndex > 0) activeIndex--"
                    x-on:keydown.enter.prevent="open && activeIndex >= 0 ? $el.querySelectorAll('[data-suggestion]')[activeIndex]?.click() : ($wire.addTag(), open = false, activeIndex = -1)"
                    x-on:keydown.comma.prevent="$wire.addTag(); open = false; activeIndex = -1"
                    x-on:keydown.escape="open = false; activeIndex = -1"
                    x-on:click.outside="open = false; activeIndex = -1"
                >
                    <flux:input
                        wire:model.live.debounce.200ms="newTag"
                        x-on:input="open = true; activeIndex = -1"
                        placeholder="Ketik tag lalu tekan Enter atau koma..."
                        autocomplete="off"
                    />
                    @if (count($this->tagSuggestions) > 0)
                        <div
                            x-show="open"
                            class="absolute z-10 mt-1 w-full overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-lg dark:border-zinc-700 dark:bg-zinc-800"
                        >
                            @foreach ($this->tagSuggestions as $i => $suggestion)
                                <button
                                    data-suggestion
                                    type="button"
                                    class="w-full px-3 py-2 text-left text-sm text-zinc-700 transition-colors dark:text-zinc-300"
                                    :class="{ 'bg-zinc-100 dark:bg-zinc-700': activeIndex === {{ $i }} }"
                                    x-on:mouseenter="activeIndex = {{ $i }}"
                                    x-on:click="$wire.selectTag('{{ $suggestion }}'); open = false; activeIndex = -1"
                                >{{ $suggestion }}</button>
                            @endforeach
                        </div>
                    @endif
                </div>
                <flux:description>Tekan Enter atau koma untuk menambah tag baru.</flux:description>
            </flux:fieldset>

            <div class="flex gap-2">
                <flux:button type="submit" variant="primary" class="flex-1">
                    {{ $post ? 'Perbarui Berita' : 'Simpan Berita' }}
                </flux:button>
                <flux:button href="{{ route('posts.index') }}" wire:navigate variant="ghost">
                    Batal
                </flux:button>
            </div>
        </div>
    </form>

    <flux:modal name="post-preview" class="w-full max-w-6xl !p-0">
        <div class="flex max-h-[85vh] flex-col">
            <div class="flex shrink-0 items-center gap-2 border-b border-zinc-200 px-4 py-4 text-sm font-medium text-zinc-500 dark:border-zinc-800">
                <flux:icon.magnifying-glass class="size-5" />
                <h4 class="text-lg">Preview Berita</h4>
                <flux:badge x-show="isDirty" color="yellow" size="sm">Belum disimpan</flux:badge>
            </div>

            <div class="min-h-0 flex-1 overflow-y-auto">
                {{-- Hero --}}
                <div class="px-8 pb-4 pt-8">
                    @if ($selectedCategories)
                        <div class="mb-4 flex flex-wrap gap-2">
                            @foreach ($this->categories->filter(fn ($c) => in_array((string) $c->id, $selectedCategories)) as $cat)
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 ring-1 ring-brand-200 dark:bg-brand-500/10 dark:text-brand-400 dark:ring-brand-500/20">
                                    <flux:icon name="tag" class="size-3.5" />
                                    {{ $cat->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif

                    <h1 class="text-3xl font-bold leading-tight tracking-tight text-zinc-900 sm:text-4xl dark:text-white">
                        {{ $title ?: 'Judul Berita' }}
                    </h1>

                    <div class="mt-5 flex flex-wrap items-center gap-4 text-sm text-zinc-500 dark:text-zinc-400">
                        <span class="flex items-center gap-1.5">
                            <span class="flex size-6 items-center justify-center rounded-full bg-brand-600 text-xs font-semibold text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </span>
                            {{ Auth::user()->name }}
                        </span>
                        <span class="hidden sm:inline">·</span>
                        <span class="flex items-center gap-1.5">
                            <flux:icon name="calendar" class="size-4" />
                            {{ now()->translatedFormat('d F Y') }}
                        </span>
                        @if ($tags)
                            <span class="hidden sm:inline">·</span>
                            <span class="flex items-center gap-1.5">
                                <flux:icon name="hashtag" class="size-4" />
                                {{ implode(', ', $tags) }}
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Featured image --}}
                @php
                    $previewImageUrl = $featured_image
                        ? $featured_image->temporaryUrl()
                        : ($existingImage ? Storage::url($existingImage) : null);
                @endphp
                @if ($previewImageUrl)
                    <div class="px-8 pt-4">
                        <div class="overflow-hidden rounded-2xl border border-zinc-200 shadow-md dark:border-zinc-800">
                            <img src="{{ $previewImageUrl }}" alt="{{ $title }}" class="aspect-video w-full object-cover">
                        </div>
                    </div>
                @endif

                {{-- Body --}}
                <div class="px-8 py-8">
                    @if ($excerpt)
                        <!-- <p class="mb-8 text-lg font-medium leading-relaxed text-zinc-600 dark:text-zinc-300">
                            {{ $excerpt }}
                        </p>
                        <hr class="mb-8 border-zinc-200 dark:border-zinc-800"> -->
                    @endif

                    <div class="prose-berita text-zinc-700 dark:text-zinc-300">
                        {!! $content ?: '<p class="italic text-zinc-400">Belum ada konten...</p>' !!}
                    </div>
                </div>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="confirm-leave" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Tinggalkan Halaman?</flux:heading>
                <flux:text class="mt-2">Ada perubahan yang belum disimpan. Yakin ingin meninggalkan halaman ini? Perubahan Anda akan hilang.</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Tetap di Sini</flux:button>
                </flux:modal.close>
                <flux:button x-on:click="confirmLeave()" variant="danger">Tinggalkan</flux:button>
            </div>
        </div>
    </flux:modal>

    <flux:modal name="confirm-remove-image" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus Gambar?</flux:heading>
                <flux:text class="mt-2">Yakin ingin menghapus gambar ini? Tindakan ini tidak dapat dibatalkan.</flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="removeExistingImage" variant="danger">Hapus</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
