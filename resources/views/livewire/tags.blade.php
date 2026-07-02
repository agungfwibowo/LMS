<div class="flex h-full w-full flex-1 flex-col gap-4">
    <flux:heading size="xl" level="1">Tags Berita</flux:heading>

    <div class="flex items-center justify-between">
        <flux:text>Kelola tags untuk berita.</flux:text>
        <flux:button wire:click="openCreate" variant="primary" icon="plus">
            Tambah Tag
        </flux:button>
    </div>

    <div x-data="formGuard({ prop: 'showForm', modal: 'tag-form', confirm: 'confirm-leave' })">
    <flux:modal name="tag-form" wire:model.self="showForm" @close="$wire.cancelForm()" :dismissible="false" :closable="false" class="w-full max-w-md !p-0">
        <div class="flex max-h-[80vh] flex-col" @input="markDirty()" @change="markDirty()">
            <x-modal.header :title="$editingId ? 'Edit Tag' : 'Tambah Tag'" closable />
            <div class="flex flex-1 flex-col overflow-y-auto">
                <form wire:submit="save" @submit="onSubmit()" class="flex min-h-0 flex-1 flex-col">
                    <div class="flex min-h-0 flex-1 flex-col space-y-4 overflow-y-auto p-4">
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
                    </div>
                    <x-modal.footer :submit="$editingId ? 'Perbarui' : 'Simpan'" guarded />
                </form>
            </div>
        </div>
    </flux:modal>

    <x-modal.confirm-leave name="confirm-leave" />
    </div>

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
                                wire:click="copy({{ $tag->id }})"
                                size="sm" variant="ghost" icon="document-duplicate"
                                tooltip="Salin tag"
                            />
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

    <flux:pagination :paginator="$this->tags" />

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
