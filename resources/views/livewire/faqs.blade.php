<div class="flex h-full w-full flex-1 flex-col gap-4">
    <flux:heading size="xl" level="1">FAQ</flux:heading>

    <div class="flex items-center justify-between">
        <flux:text>Kelola pertanyaan yang sering diajukan pada halaman utama.</flux:text>
        <flux:button wire:click="openCreate" variant="primary" icon="plus">
            Tambah FAQ
        </flux:button>
    </div>

    <div x-data="formGuard({ prop: 'showForm', modal: 'faq-form', confirm: 'confirm-leave' })">
    <flux:modal name="faq-form" wire:model.self="showForm" @close="$wire.cancelForm()" :dismissible="false" :closable="false" class="w-full max-w-2xl !p-0">
        <div class="flex max-h-[80vh] flex-col" @input="markDirty()" @change="markDirty()">
            <x-modal.header :title="$editingId ? 'Edit FAQ' : 'Tambah FAQ'" closable />
            <div class="flex flex-1 flex-col overflow-y-auto">
                <form wire:submit="save" @submit="onSubmit()" class="flex min-h-0 flex-1 flex-col">
                    <div class="flex min-h-0 flex-1 flex-col space-y-4 overflow-y-auto p-4">
                        <flux:textarea
                            wire:model="question"
                            label="Pertanyaan"
                            placeholder="Tulis pertanyaan..."
                            rows="2"
                            required
                        />

                        <flux:textarea
                            wire:model="answer"
                            label="Jawaban"
                            placeholder="Tulis jawaban lengkap..."
                            rows="4"
                            required
                        />

                        <flux:switch wire:model="isActive" label="Tampilkan di halaman utama" />
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
            <flux:table.column class="w-8">#</flux:table.column>
            <flux:table.column>Pertanyaan</flux:table.column>
            <flux:table.column class="w-24">Status</flux:table.column>
            <flux:table.column class="w-32">Urutan</flux:table.column>
            <flux:table.column class="w-24"></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->faqs as $faq)
                <flux:table.row :key="$faq->id">
                    <flux:table.cell class="text-zinc-400 tabular-nums">{{ $loop->iteration }}</flux:table.cell>
                    <flux:table.cell>
                        <div class="font-medium text-sm">{{ $faq->question }}</div>
                        <div class="text-xs text-zinc-400 mt-0.5 line-clamp-1">{{ $faq->answer }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <button wire:click="toggleActive({{ $faq->id }})" class="cursor-pointer">
                            @if ($faq->is_active)
                                <flux:badge color="lime" size="sm">Aktif</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">Nonaktif</flux:badge>
                            @endif
                        </button>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-1">
                            <flux:button
                                wire:click="moveUp({{ $faq->id }})"
                                size="sm" variant="ghost" icon="chevron-up"
                                :disabled="$loop->first"
                            />
                            <flux:button
                                wire:click="moveDown({{ $faq->id }})"
                                size="sm" variant="ghost" icon="chevron-down"
                                :disabled="$loop->last"
                            />
                        </div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="edit({{ $faq->id }})" size="sm" variant="ghost" icon="pencil" />
                            <flux:button
                                wire:click="copy({{ $faq->id }})"
                                size="sm" variant="ghost" icon="document-duplicate"
                                tooltip="Salin FAQ"
                            />
                            <flux:button
                                wire:click="confirmDelete({{ $faq->id }})"
                                size="sm" variant="ghost" icon="trash"
                                class="text-red-500 hover:text-red-600"
                            />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="py-8 text-center text-zinc-500">
                        Belum ada FAQ. Tambahkan pertanyaan pertama.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="confirm-delete-faq" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus FAQ?</flux:heading>
                <flux:text class="mt-2">
                    Yakin ingin menghapus pertanyaan <strong>"{{ Str::limit($deletingQuestion, 60) }}"</strong>? Tindakan ini tidak dapat dibatalkan.
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
