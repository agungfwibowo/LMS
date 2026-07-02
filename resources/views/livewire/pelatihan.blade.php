<div class="flex h-full w-full flex-1 flex-col gap-4">
    <flux:heading size="xl" level="1">Pelatihan</flux:heading>

    <div class="flex items-center justify-between">
        <flux:text>Kelola daftar pelatihan.</flux:text>
        <flux:button href="{{ route('pelatihan.create') }}" wire:navigate variant="primary" icon="plus">
            Tambah Pelatihan
        </flux:button>
    </div>

    <flux:table>
        <flux:table.columns>
            <flux:table.column class="w-16">Poster</flux:table.column>
            <flux:table.column>Judul &amp; Kategori</flux:table.column>
            <flux:table.column class="w-20">Modul</flux:table.column>
            <flux:table.column class="w-40">Jadwal</flux:table.column>
            <flux:table.column class="w-28">Mode</flux:table.column>
            <flux:table.column class="w-28">Status</flux:table.column>
            <flux:table.column class="w-24">Aktif</flux:table.column>
            <flux:table.column class="w-32"></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->pelatihans as $pelatihan)
                @if ($quickEditId === $pelatihan->id)
                <flux:table.row :key="'qe-' . $pelatihan->id">
                    <flux:table.cell colspan="8" class="!p-0">
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
                                <flux:select wire:model="qeCategoryId" label="Kategori" placeholder="Tanpa kategori">
                                    @foreach ($this->categories as $category)
                                        <flux:select.option :value="$category->id">{{ $category->name }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:select wire:model="qeStatus" label="Status">
                                    @foreach (App\Enums\PelatihanStatus::cases() as $s)
                                        <flux:select.option value="{{ $s->value }}">{{ $s->label() }}</flux:select.option>
                                    @endforeach
                                </flux:select>
                                <flux:field variant="inline" class="pt-6">
                                    <flux:checkbox wire:model="qeIsActive" />
                                    <flux:label>Aktif</flux:label>
                                </flux:field>
                            </div>

                            <div class="flex items-center gap-2 pt-1">
                                <flux:button type="submit" variant="primary" size="sm">Simpan</flux:button>
                                <flux:button type="button" wire:click="cancelQuickEdit" variant="ghost" size="sm">Batal</flux:button>
                            </div>
                        </form>
                    </flux:table.cell>
                </flux:table.row>
                @else
                <flux:table.row :key="$pelatihan->id">
                    <flux:table.cell>
                        @if ($pelatihan->thumbnail_url)
                            <img src="{{ $pelatihan->thumbnail_url }}" class="h-10 w-14 rounded object-cover" alt="{{ $pelatihan->title }}">
                        @else
                            <div class="flex h-10 w-14 items-center justify-center rounded bg-zinc-100 text-zinc-400 dark:bg-zinc-700">
                                <flux:icon.photo class="size-4" />
                            </div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="text-sm font-medium">{{ $pelatihan->title }}</div>
                        <div class="text-xs text-zinc-400">{{ $pelatihan->category?->name ?? 'Tanpa kategori' }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if ($pelatihan->modules_count > 0)
                            <flux:tooltip content="{{ $pelatihan->modules->pluck('title')->implode(', ') }}">
                                <flux:badge color="zinc" size="sm" class="cursor-default">{{ $pelatihan->modules_count }}</flux:badge>
                            </flux:tooltip>
                        @else
                            <flux:badge color="zinc" size="sm">0</flux:badge>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="text-xs text-zinc-500">
                        {{ $pelatihan->start_date?->translatedFormat('d M Y, H:i') ?? '-' }}
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge color="zinc" size="sm">{{ ucfirst($pelatihan->mode) }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <flux:badge :color="$pelatihan->status->color()" size="sm">{{ $pelatihan->status->label() }}</flux:badge>
                    </flux:table.cell>
                    <flux:table.cell>
                        <button wire:click="toggleActive({{ $pelatihan->id }})" class="cursor-pointer">
                            @if ($pelatihan->is_active)
                                <flux:badge color="lime" size="sm">Aktif</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">Nonaktif</flux:badge>
                            @endif
                        </button>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <flux:button
                                wire:click="startQuickEdit({{ $pelatihan->id }})"
                                size="sm" variant="ghost" icon="pencil-square"
                                tooltip="Edit Cepat"
                            />
                            <flux:button href="{{ route('pelatihan.edit', $pelatihan) }}" wire:navigate size="sm" variant="ghost" icon="pencil" tooltip="Edit Lengkap" />
                            <flux:button
                                wire:click="copy({{ $pelatihan->id }})"
                                size="sm" variant="ghost" icon="document-duplicate"
                                tooltip="Salin pelatihan"
                            />
                            <flux:button
                                wire:click="confirmDelete({{ $pelatihan->id }})"
                                size="sm" variant="ghost" icon="trash"
                                class="text-red-500 hover:text-red-600"
                            />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
                @endif
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="8" class="py-8 text-center text-zinc-500">
                        Belum ada pelatihan. Tambahkan pelatihan pertama.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:pagination :paginator="$this->pelatihans" />

    <flux:modal name="confirm-delete-pelatihan" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus Pelatihan?</flux:heading>
                <flux:text class="mt-2">
                    Yakin ingin menghapus pelatihan <strong>"{{ $deletingTitle }}"</strong>? Tindakan ini tidak dapat dibatalkan.
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
