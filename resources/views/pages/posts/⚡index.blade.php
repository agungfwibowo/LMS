<?php

use App\Actions\Posts\CopyPost;
use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Berita')] class extends Component {
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $statusFilter = '';

    #[Url]
    public string $categoryFilter = '';

    public function updated(): void
    {
        $this->resetPage();
    }

    #[Computed]
    public function posts()
    {
        return Post::query()
            ->with(['author', 'categories'])
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%"))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->categoryFilter, fn ($q) => $q->whereHas('categories', fn ($q) => $q->where('categories.id', $this->categoryFilter)))
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function categories()
    {
        return Category::orderBy('name')->get();
    }

    #[Computed]
    public function authors()
    {
        return User::orderBy('name')->get();
    }

    // --- Quick Edit ---
    public ?int $quickEditId = null;

    public string $qeTitle = '';

    public string $qeSlug = '';

    public string $qeStatus = 'draft';

    public ?int $qeAuthorId = null;

    public array $qeCategories = [];

    public string $qePublishedAt = '';

    public function startQuickEdit(int $id): void
    {
        $post = Post::with('categories')->findOrFail($id);

        $this->quickEditId = $post->id;
        $this->qeTitle = $post->title;
        $this->qeSlug = $post->slug;
        $this->qeStatus = $post->status->value;
        $this->qeAuthorId = $post->author_id;
        $this->qeCategories = $post->categories->pluck('id')->map(fn ($id) => (string) $id)->toArray();
        $this->qePublishedAt = $post->published_at?->format('Y-m-d\TH:i') ?? '';

        $this->resetValidation();
    }

    public function cancelQuickEdit(): void
    {
        $this->reset(['quickEditId', 'qeTitle', 'qeSlug', 'qeStatus', 'qeAuthorId', 'qeCategories', 'qePublishedAt']);
        $this->resetValidation();
    }

    public function saveQuickEdit(): void
    {
        if (! $this->quickEditId) {
            return;
        }

        $validated = $this->validate([
            'qeTitle' => ['required', 'string', 'max:255'],
            'qeSlug' => ['required', 'string', 'max:255', Rule::unique('posts', 'slug')->ignore($this->quickEditId)],
            'qeStatus' => ['required', Rule::in(array_column(PostStatus::cases(), 'value'))],
            'qeAuthorId' => ['required', 'integer', 'exists:users,id'],
            'qeCategories' => ['nullable', 'array'],
            'qeCategories.*' => ['integer', 'exists:categories,id'],
            'qePublishedAt' => ['nullable', 'date'],
        ]);

        $post = Post::findOrFail($this->quickEditId);

        $publishedAt = match (true) {
            (bool) $validated['qePublishedAt'] => $validated['qePublishedAt'],
            $validated['qeStatus'] === PostStatus::Published->value && ! $post->published_at => now(),
            default => $post->published_at,
        };

        $post->update([
            'title' => $validated['qeTitle'],
            'slug' => $validated['qeSlug'],
            'status' => $validated['qeStatus'],
            'author_id' => $validated['qeAuthorId'],
            'published_at' => $publishedAt,
        ]);

        $post->categories()->sync($validated['qeCategories']);

        $this->cancelQuickEdit();
        unset($this->posts);
        Flux::toast(variant: 'success', text: 'Berita berhasil diperbarui.');
    }

    public function copy(int $id, CopyPost $copyPost): void
    {
        $post = Post::findOrFail($id);
        $copy = $copyPost->handle($post);
        unset($this->posts);
        Flux::toast(variant: 'success', text: "Berita berhasil disalin sebagai \"{$copy->title}\".");
    }

    public ?int $deletingId = null;

    public string $deletingName = '';

    public function confirmDelete(int $id): void
    {
        $post = Post::findOrFail($id);
        $this->deletingId = $id;
        $this->deletingName = $post->title;
        $this->modal('confirm-delete-post')->show();
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        $post = Post::findOrFail($this->deletingId);

        if ($post->featured_image) {
            Storage::disk('public')->delete($post->featured_image);
        }

        $post->delete();
        $this->deletingId = null;
        $this->deletingName = '';
        unset($this->posts);
        $this->modal('confirm-delete-post')->close();
        Flux::toast(variant: 'success', text: 'Berita berhasil dihapus.');
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-4">
        <div class="flex items-center justify-between">
            <flux:heading size="xl" level="1">Berita</flux:heading>
            <flux:button href="{{ route('posts.create') }}" wire:navigate variant="primary" icon="plus">
                Tambah Berita
            </flux:button>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <flux:input
                wire:model.live.debounce.300ms="search"
                placeholder="Cari berita..."
                icon="magnifying-glass"
                class="w-64"
                clearable
            />

            <flux:select wire:model.live="statusFilter" class="w-44">
                <flux:select.option value="">Semua Status</flux:select.option>
                @foreach (App\Enums\PostStatus::cases() as $status)
                    <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:select wire:model.live="categoryFilter" class="w-44">
                <flux:select.option value="">Semua Kategori</flux:select.option>
                @foreach ($this->categories as $category)
                    <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        <flux:table>
            <flux:table.columns>
                <flux:table.column>Judul</flux:table.column>
                <flux:table.column>Kategori</flux:table.column>
                <flux:table.column>Status</flux:table.column>
                <flux:table.column>Penulis</flux:table.column>
                <flux:table.column>Tanggal</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>
            <flux:table.rows>
                @forelse ($this->posts as $post)
                    @if ($quickEditId === $post->id)
                    <flux:table.row :key="'qe-' . $post->id">
                        <flux:table.cell colspan="6" class="!p-0">
                            <form wire:submit="saveQuickEdit" class="space-y-4 bg-zinc-50 p-4 dark:bg-zinc-800/50">
                                <div class="flex items-center gap-2 text-sm font-semibold text-zinc-700 dark:text-zinc-200">
                                    <flux:icon.pencil-square class="size-4" />
                                    Edit Cepat
                                </div>

                                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                    <flux:input wire:model="qeTitle" label="Judul" />
                                    <flux:input wire:model="qeSlug" label="Slug" />
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                                    <flux:select wire:model="qeStatus" label="Status">
                                        @foreach (PostStatus::cases() as $s)
                                            <flux:select.option value="{{ $s->value }}">{{ $s->label() }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:select wire:model="qeAuthorId" label="Penulis">
                                        @foreach ($this->authors as $author)
                                            <flux:select.option value="{{ $author->id }}">{{ $author->name }}</flux:select.option>
                                        @endforeach
                                    </flux:select>
                                    <flux:input wire:model="qePublishedAt" type="datetime-local" label="Tanggal Publikasi" />
                                </div>

                                @if ($this->categories->isNotEmpty())
                                    <div>
                                        <flux:label class="mb-2">Kategori</flux:label>
                                        <div class="flex flex-wrap gap-x-4 gap-y-2">
                                            @foreach ($this->categories as $category)
                                                <flux:checkbox
                                                    wire:model="qeCategories"
                                                    :value="(string) $category->id"
                                                    :label="$category->name"
                                                />
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <div class="flex items-center gap-2 pt-1">
                                    <flux:button type="submit" variant="primary" size="sm">Simpan</flux:button>
                                    <flux:button type="button" wire:click="cancelQuickEdit" variant="ghost" size="sm">Batal</flux:button>
                                </div>
                            </form>
                        </flux:table.cell>
                    </flux:table.row>
                    @else
                    <flux:table.row :key="$post->id">
                        <flux:table.cell>
                            <div class="flex items-center gap-3">
                                @if ($post->featured_image)
                                    <img
                                        src="{{ Storage::url($post->featured_image) }}"
                                        alt="{{ $post->title }}"
                                        class="h-10 w-16 rounded object-cover"
                                    >
                                @endif
                                <span class="font-medium">{{ Str::limit($post->title, 50) }}</span>
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($post->categories as $category)
                                    <flux:badge color="blue" size="sm">{{ $category->name }}</flux:badge>
                                @endforeach
                            </div>
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:badge color="{{ $post->status->color() }}" size="sm">
                                {{ $post->status->label() }}
                            </flux:badge>
                        </flux:table.cell>
                        <flux:table.cell class="text-zinc-500">{{ $post->author->name }}</flux:table.cell>
                        <flux:table.cell class="text-zinc-500 text-sm">
                            {{ $post->published_at?->format('d M Y') ?? $post->created_at->format('d M Y') }}
                        </flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-2">
                                <flux:tooltip content="Lihat">
                                <flux:link href="{{ url('/berita/' . $post->slug) }}"
                                    target="_blank"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-md hover:bg-zinc-800/5 dark:hover:bg-white/15"
                                >
                                    <flux:icon.arrow-top-right-on-square class="text-zinc-800 dark:text-white" variant="mini" />
                                </flux:link>
                                </flux:tooltip>
                                <flux:button
                                    wire:click="startQuickEdit({{ $post->id }})"
                                    size="sm"
                                    variant="ghost"
                                    icon="pencil-square"
                                    tooltip="Edit Cepat"
                                />
                                <flux:button
                                    href="{{ route('posts.edit', $post) }}"
                                    wire:navigate
                                    size="sm"
                                    variant="ghost"
                                    icon="pencil"
                                    tooltip="Edit Lengkap"
                                />
                                <flux:button
                                    wire:click="copy({{ $post->id }})"
                                    size="sm"
                                    variant="ghost"
                                    icon="document-duplicate"
                                    tooltip="Salin"
                                />
                                <flux:button
                                    wire:click="confirmDelete({{ $post->id }})"
                                    size="sm"
                                    variant="ghost"
                                    icon="trash"
                                    class="text-red-500 hover:text-red-600"
                                    tooltip="Hapus"
                                />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                    @endif
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="6" class="py-12 text-center text-zinc-500">
                            @if ($search || $statusFilter || $categoryFilter)
                                Tidak ada berita yang cocok dengan filter.
                            @else
                                Belum ada berita. <flux:link href="{{ route('posts.create') }}" wire:navigate>Tambah berita pertama.</flux:link>
                            @endif
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        <flux:pagination :paginator="$this->posts" />

        <flux:modal name="confirm-delete-post" class="min-w-88">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Hapus Berita?</flux:heading>
                    <flux:text class="mt-2">
                        Yakin ingin menghapus berita <strong>"{{ $deletingName }}"</strong>? Tindakan ini tidak dapat dibatalkan.
                    </flux:text>
                </div>
                <div class="flex gap-2">
                    <flux:spacer />
                    <flux:modal.close>
                        <flux:button variant="ghost">Batal</flux:button>
                    </flux:modal.close>
                    <flux:button wire:click="delete" variant="danger">Hapus</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
</div>
