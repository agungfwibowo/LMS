<?php

use App\Models\Category;
use App\Models\Post;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
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
                                <flux:button
                                    href="{{ route('posts.edit', $post) }}"
                                    wire:navigate
                                    size="sm"
                                    variant="ghost"
                                    icon="pencil"
                                />
                                <flux:button
                                    wire:click="confirmDelete({{ $post->id }})"
                                    size="sm"
                                    variant="ghost"
                                    icon="trash"
                                    class="text-red-500 hover:text-red-600"
                                />
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
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

        <div class="mt-2">
            {{ $this->posts->links() }}
        </div>

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
