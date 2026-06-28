<?php

use App\Models\Tag;
use Flux\Flux;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Title('Tags')] class extends Component {
    use WithPagination;
    public string $name = '';
    public string $slug = '';
    public ?int $editingId = null;
    public bool $showForm = false;

    #[Computed]
    public function tags()
    {
        return Tag::withCount('posts')->orderBy('name')->paginate(10);
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
            'slug' => ['required', 'string', 'max:255', Rule::unique('tags', 'slug')->ignore($this->editingId)],
        ]);

        if ($this->editingId) {
            Tag::findOrFail($this->editingId)->update([
                'name' => $this->name,
                'slug' => $this->slug,
            ]);
            Flux::toast(variant: 'success', text: 'Tag berhasil diperbarui.');
        } else {
            Tag::create([
                'name' => $this->name,
                'slug' => $this->slug,
            ]);
            Flux::toast(variant: 'success', text: 'Tag berhasil ditambahkan.');
        }

        $this->reset(['name', 'slug', 'editingId', 'showForm']);
        $this->resetPage();
        unset($this->tags);
    }

    public function edit(int $id): void
    {
        $tag = Tag::findOrFail($id);
        $this->editingId = $id;
        $this->name = $tag->name;
        $this->slug = $tag->slug;
        $this->showForm = true;
    }

    public function openCreate(): void
    {
        $this->reset(['name', 'slug', 'editingId']);
        $this->showForm = true;
    }

    public function cancelForm(): void
    {
        $this->reset(['name', 'slug', 'editingId', 'showForm']);
    }

    public ?int $deletingId = null;

    public string $deletingName = '';

    public function confirmDelete(int $id): void
    {
        $tag = Tag::findOrFail($id);
        $this->deletingId = $id;
        $this->deletingName = $tag->name;
        $this->modal('confirm-delete-tag')->show();
    }

    public function delete(): void
    {
        if (! $this->deletingId) {
            return;
        }

        Tag::findOrFail($this->deletingId)->delete();
        $this->deletingId = null;
        $this->deletingName = '';
        $this->resetPage();
        unset($this->tags);
        $this->modal('confirm-delete-tag')->close();
        Flux::toast(variant: 'success', text: 'Tag berhasil dihapus.');
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-4">
    <flux:heading size="xl" level="1">Tags Berita</flux:heading>

    <div class="flex items-center justify-between">
        <flux:text>Kelola tags untuk berita.</flux:text>
        @if (! $showForm)
            <flux:button wire:click="openCreate" variant="primary" icon="plus">
                Tambah Tag
            </flux:button>
        @endif
    </div>

    @if ($showForm)
        <flux:card class="max-w-sm">
            <flux:heading size="lg" class="mb-4">
                {{ $editingId ? 'Edit Tag' : 'Tambah Tag' }}
            </flux:heading>

            <form wire:submit="save" class="space-y-4">
                <flux:input
                    wire:model.live="name"
                    label="Nama Tag"
                    placeholder="Contoh: Hukum"
                    required
                />

                <flux:input
                    wire:model="slug"
                    label="Slug"
                    placeholder="hukum"
                    required
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
            <flux:table.column>Berita</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->tags as $tag)
                <flux:table.row :key="$tag->id">
                    <flux:table.cell class="font-medium">{{ $tag->name }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="zinc" size="sm">{{ $tag->slug }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>{{ $tag->posts_count }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="edit({{ $tag->id }})" size="sm" variant="ghost" icon="pencil" />
                            <flux:button
                                wire:click="confirmDelete({{ $tag->id }})"
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
                    <flux:table.cell colspan="4" class="py-8 text-center text-zinc-500">
                        Belum ada tag. Tambahkan tag pertama.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <div class="mt-2">
        {{ $this->tags->links() }}
    </div>

    <flux:modal name="confirm-delete-tag" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus Tag?</flux:heading>
                <flux:text class="mt-2">
                    Yakin ingin menghapus tag <strong>"{{ $deletingName }}"</strong>? Tindakan ini tidak dapat dibatalkan.
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
