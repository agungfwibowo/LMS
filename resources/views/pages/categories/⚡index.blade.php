<?php

use App\Models\Category;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Kategori')] class extends Component {
    use WithPagination;
    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public ?int $editingId = null;
    public bool $showForm = false;

    #[Computed]
    public function categories()
    {
        return Category::withCount('posts')->orderBy('name')->paginate(3);
    }

    public function updatedName(): void
    {
        if (! $this->editingId) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('categories', 'slug')->ignore($this->editingId)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        if ($this->editingId) {
            Category::findOrFail($this->editingId)->update([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
            ]);
            Flux::toast(variant: 'success', text: 'Kategori berhasil diperbarui.');
        } else {
            Category::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
            ]);
            Flux::toast(variant: 'success', text: 'Kategori berhasil ditambahkan.');
        }

        $this->reset(['name', 'slug', 'description', 'editingId', 'showForm']);
        $this->resetPage();
        unset($this->categories);
    }

    public function edit(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->editingId = $id;
        $this->name = $category->name;
        $this->slug = $category->slug;
        $this->description = $category->description ?? '';
        $this->showForm = true;
    }

    public function openCreate(): void
    {
        $this->reset(['name', 'slug', 'description', 'editingId']);
        $this->showForm = true;
    }

    public function cancelForm(): void
    {
        $this->reset(['name', 'slug', 'description', 'editingId', 'showForm']);
    }

    public ?int $deletingId = null;

    public string $deletingName = '';

    public function confirmDelete(int $id): void
    {
        $category = Category::findOrFail($id);
        $this->deletingId = $id;
        $this->deletingName = $category->name;
        $this->modal('confirm-delete-category')->show();
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        Category::findOrFail($this->deletingId)->delete();
        $this->deletingId = null;
        $this->deletingName = '';
        $this->resetPage();
        unset($this->categories);
        $this->modal('confirm-delete-category')->close();
        Flux::toast(variant: 'success', text: 'Kategori berhasil dihapus.');
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-4">
    <flux:heading size="xl" level="1">Kategori Berita</flux:heading>

    <div class="flex items-center justify-between">
        <flux:text>Kelola kategori untuk berita.</flux:text>
        @if (! $showForm)
            <flux:button wire:click="openCreate" variant="primary" icon="plus">
                Tambah Kategori
            </flux:button>
        @endif
    </div>

    @if ($showForm)
        <flux:card class="max-w-lg">
            <flux:heading size="lg" class="mb-4">
                {{ $editingId ? 'Edit Kategori' : 'Tambah Kategori' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:input
                    wire:model.live="name"
                    label="Nama Kategori"
                    placeholder="Contoh: Politik"
                    required
                />

                <flux:input
                    wire:model="slug"
                    label="Slug"
                    placeholder="contoh-politik"
                    required
                />

                <flux:textarea
                    wire:model="description"
                    label="Deskripsi"
                    placeholder="Deskripsi singkat kategori..."
                    rows="3"
                />

                <div class="flex gap-2">
                    <flux:button type="submit" variant="primary">
                        {{ $editingId ? 'Perbarui' : 'Simpan' }}
                    </flux:button>
                    <flux:button wire:click="cancelForm" variant="ghost">Batal</flux:button>
                </div>
            </form>
        </flux:card>
    @endif

    <flux:table>
        <flux:table.columns>
            <flux:table.column>Nama</flux:table.column>
            <flux:table.column>Slug</flux:table.column>
            <flux:table.column>Deskripsi</flux:table.column>
            <flux:table.column>Berita</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->categories as $category)
                <flux:table.row :key="$category->id">
                    <flux:table.cell class="font-medium">{{ $category->name }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="zinc" size="sm">{{ $category->slug }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="text-zinc-500">{{ $category->description ?: '-' }}</flux:table.cell>
                    <flux:table.cell>{{ $category->posts_count }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="edit({{ $category->id }})" size="sm" variant="ghost" icon="pencil" />
                            <flux:button
                                wire:click="confirmDelete({{ $category->id }})"
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
                    <flux:table.cell colspan="5" class="py-8 text-center text-zinc-500">
                        Belum ada kategori. Tambahkan kategori pertama.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div class="mt-2">
        {{ $this->categories->links() }}
    </div>

    <flux:modal name="confirm-delete-category" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus Kategori?</flux:heading>
                <flux:text class="mt-2">
                    Yakin ingin menghapus kategori <strong>"{{ $deletingName }}"</strong>? Tindakan ini tidak dapat dibatalkan.
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
