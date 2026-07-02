<div class="flex h-full w-full flex-1 flex-col gap-4">
    <flux:heading size="xl" level="1">Kategori Pelatihan</flux:heading>

    <div class="flex items-center justify-between">
        <flux:text>Kelola kategori untuk pelatihan.</flux:text>
        <flux:button wire:click="openCreate" variant="primary" icon="plus">
            Tambah Kategori
        </flux:button>
    </div>

    <div x-data="formGuard({ prop: 'showForm', modal: 'pelatihan-category-form', confirm: 'confirm-leave' })">
    <flux:modal name="pelatihan-category-form" wire:model.self="showForm" @close="$wire.cancelForm()" :dismissible="false" :closable="false" class="w-full max-w-lg !p-0">
        <div class="flex max-h-[80vh] flex-col" @input="markDirty()" @change="markDirty()">
            <x-modal.header :title="$editingId ? 'Edit Kategori' : 'Tambah Kategori'" closable />
            <div class="flex flex-1 flex-col overflow-y-auto">
                <form wire:submit="save" @submit="onSubmit()" class="flex min-h-0 flex-1 flex-col">
                    <div class="flex min-h-0 flex-1 flex-col space-y-4 overflow-y-auto p-4">
                        <flux:input
                            wire:model.live="name"
                            label="Nama Kategori"
                            placeholder="Contoh: Manajemen Rumah Sakit"
                            required
                        />

                        <flux:input
                            wire:model="slug"
                            label="Slug"
                            placeholder="manajemen-rumah-sakit"
                            required
                        />

                        <flux:textarea
                            wire:model="description"
                            label="Deskripsi"
                            placeholder="Deskripsi singkat kategori..."
                            rows="3"
                        />

                        <div class="flex items-end gap-3">
                            <flux:select wire:model.live="icon" label="Ikon" class="flex-1">
                                @foreach (\App\Enums\PelatihanCategoryIcon::cases() as $case)
                                    <flux:select.option :value="$case->value">{{ $case->label() }}</flux:select.option>
                                @endforeach
                            </flux:select>
                            <div class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-brand-50 dark:bg-teal-900/40">
                                <flux:icon :name="\App\Enums\PelatihanCategoryIcon::tryFrom($icon)?->value ?? 'academic-cap'" class="size-5 text-brand-700 dark:text-teal-300" />
                            </div>
                        </div>
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
            <flux:table.column class="w-10"></flux:table.column>
            <flux:table.column>Nama</flux:table.column>
            <flux:table.column>Slug</flux:table.column>
            <flux:table.column>Deskripsi</flux:table.column>
            <flux:table.column>Pelatihan</flux:table.column>
            <flux:table.column></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->categories as $category)
                <flux:table.row :key="$category->id">
                    <flux:table.cell>
                        <div class="flex size-8 items-center justify-center rounded-lg bg-brand-50 dark:bg-teal-900/40">
                            <flux:icon :name="$category->icon?->value ?? 'academic-cap'" class="size-4 text-brand-700 dark:text-teal-300" />
                        </div>
                    </flux:table.cell>
                    <flux:table.cell class="font-medium">{{ $category->name }}</flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="zinc" size="sm">{{ $category->slug }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell class="text-zinc-500">{{ $category->description ?: '-' }}</flux:table.cell>
                    <flux:table.cell>{{ $category->pelatihans_count }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="edit({{ $category->id }})" size="sm" variant="ghost" icon="pencil" />
                            <flux:button
                                wire:click="copy({{ $category->id }})"
                                size="sm" variant="ghost" icon="document-duplicate"
                                tooltip="Salin kategori"
                            />
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
                    <flux:table.cell colspan="6" class="py-8 text-center text-zinc-500">
                        Belum ada kategori. Tambahkan kategori pertama.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:pagination :paginator="$this->categories" />

    <flux:modal name="confirm-delete-pelatihan-category" class="min-w-88">
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
