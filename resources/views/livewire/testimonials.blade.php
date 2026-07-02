<div class="flex h-full w-full flex-1 flex-col gap-4">
    <flux:heading size="xl" level="1">Testimoni</flux:heading>

    <div class="flex items-center justify-between">
        <flux:text>Kelola testimoni peserta yang ditampilkan di halaman utama.</flux:text>
        <flux:button wire:click="openCreate" variant="primary" icon="plus">
            Tambah Testimoni
        </flux:button>
    </div>

    <div x-data="formGuard({ prop: 'showForm', modal: 'testimonial-form', confirm: 'confirm-leave' })">
    <flux:modal name="testimonial-form" wire:model.self="showForm" @close="$wire.cancelForm()" :dismissible="false" :closable="false" class="w-full max-w-2xl !p-0">
        <div class="flex max-h-[80vh] flex-col" @input="markDirty()" @change="markDirty()">
            <x-modal.header :title="$editingId ? 'Edit Testimoni' : 'Tambah Testimoni'" closable />
            <div class="flex flex-1 flex-col overflow-y-auto">
                <form wire:submit="save" @submit="onSubmit()" class="flex min-h-0 flex-1 flex-col">
                    <div class="flex min-h-0 flex-1 flex-col space-y-4 overflow-y-auto p-4">
                        <div class="grid grid-cols-2 gap-4">
                            <flux:input wire:model="name" label="Nama" placeholder="Siti Rahmawati" required />
                            <flux:input wire:model="role" label="Jabatan / Peran" placeholder="Perawat Pelaksana" required />
                        </div>

                        <flux:textarea wire:model="quote" label="Kutipan" placeholder="Tulis testimoni peserta..." rows="3" required />

                        {{-- Foto --}}
                        <div>
                            <flux:label>Foto Profil</flux:label>
                            <div class="mt-2 flex gap-4">
                                <label class="flex cursor-pointer items-center gap-2 text-sm">
                                    <input type="radio" wire:model.live="photoSource" value="initials" class="accent-brand-600"> Inisial
                                </label>
                                <label class="flex cursor-pointer items-center gap-2 text-sm">
                                    <input type="radio" wire:model.live="photoSource" value="upload" class="accent-brand-600"> Upload foto
                                </label>
                                <label class="flex cursor-pointer items-center gap-2 text-sm">
                                    <input type="radio" wire:model.live="photoSource" value="external" class="accent-brand-600"> Link eksternal
                                </label>
                            </div>

                            @if ($photoSource === 'upload')
                                <div class="mt-3 space-y-2">
                                    {{-- Preview --}}
                                    @if ($uploadedPhoto)
                                        <img src="{{ $uploadedPhoto->temporaryUrl() }}" class="size-16 rounded-full object-cover" alt="Preview">
                                    @elseif ($existingPhoto && ! str_starts_with($existingPhoto, 'http'))
                                        <img src="{{ Storage::disk('public')->url($existingPhoto) }}" class="size-16 rounded-full object-cover" alt="Foto saat ini">
                                    @endif
                                    <flux:input type="file" wire:model="uploadedPhoto" accept="image/*" />
                                    @error('uploadedPhoto') <flux:error>{{ $message }}</flux:error> @enderror
                                </div>
                            @endif

                            @if ($photoSource === 'external')
                                <div class="mt-3">
                                    <flux:input wire:model="photoUrl" placeholder="https://example.com/foto.jpg" />
                                    @if ($photoUrl)
                                        <img src="{{ $photoUrl }}" class="mt-2 size-16 rounded-full object-cover" alt="Preview">
                                    @endif
                                    @error('photoUrl') <flux:error>{{ $message }}</flux:error> @enderror
                                </div>
                            @endif
                        </div>

                        {{-- Warna avatar (hanya relevan kalau pakai inisial) --}}
                        @if ($photoSource === 'initials')
                            <flux:select wire:model="avatarColor" label="Warna Avatar">
                                <option value="brand">Teal (brand)</option>
                                <option value="lime">Lime (hijau)</option>
                            </flux:select>
                        @endif

                        {{-- Rating slider dengan live star preview --}}
                        <div
                            x-data="{
                                r: $wire.entangle('rating').live,
                                fill(i) {
                                    const rem = Number(this.r) - (i - 1);
                                    if (rem >= 1) return 100;
                                    if (rem >= 0.5) return 50;
                                    return 0;
                                }
                            }"
                        >
                            <flux:label class="mb-2 block">Rating</flux:label>

                            {{-- Star preview --}}
                            @php $starPath = 'M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z'; @endphp
                            <div class="mb-2 flex items-center gap-1">
                                <template x-for="i in 5" :key="i">
                                    <span class="relative inline-block h-6 w-6">
                                        <svg class="absolute inset-0 h-6 w-6 text-zinc-200/80 dark:text-zinc-600/80 " fill="currentColor" viewBox="0 0 20 20">
                                            <path d="{{ $starPath }}"/>
                                        </svg>
                                        <span class="absolute inset-0 overflow-hidden" :style="'width:' + fill(i) + '%'">
                                            <svg class="h-6 w-6 text-lime" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="{{ $starPath }}"/>
                                            </svg>
                                        </span>
                                    </span>
                                </template>
                                <span class="ml-2 text-sm font-semibold tabular-nums" x-text="Number(r).toFixed(1) + ' / 5.0'"></span>
                            </div>

                            <input
                                type="range"
                                x-model.number="r"
                                min="0" max="5" step="0.5"
                                class="custom-range w-full cursor-pointer accent-lime"
                            >
                            <div class="mt-0.5 flex justify-between text-[11px] text-zinc-400">
                                <span>0</span><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span>
                            </div>
                        </div>

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
            <flux:table.column class="w-12">Foto</flux:table.column>
            <flux:table.column>Nama &amp; Peran</flux:table.column>
            <flux:table.column>Kutipan</flux:table.column>
            <flux:table.column class="w-28">Rating</flux:table.column>
            <flux:table.column class="w-24">Status</flux:table.column>
            <flux:table.column class="w-24"></flux:table.column>
        </flux:table.columns>
        <flux:table.rows>
            @forelse ($this->testimonials as $testimonial)
                <flux:table.row :key="$testimonial->id">
                    <flux:table.cell>
                        @if ($testimonial->photo_url)
                            <img src="{{ $testimonial->photo_url }}" class="size-9 rounded-full object-cover" alt="{{ $testimonial->name }}">
                        @else
                            <div class="{{ $testimonial->avatar_bg_class }} flex size-9 items-center justify-center rounded-full text-xs font-bold text-brand-900 dark:text-teal-300">
                                {{ $testimonial->initials }}
                            </div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="font-medium text-sm">{{ $testimonial->name }}</div>
                        <div class="text-xs text-zinc-400">{{ $testimonial->role }}</div>
                    </flux:table.cell>
                    <flux:table.cell class="text-sm text-zinc-500">
                        <div class="line-clamp-2 max-w-xs">{{ $testimonial->quote }}</div>
                    </flux:table.cell>
                    <flux:table.cell>
                        <x-star-rating :rating="$testimonial->rating ?? 0" :show-value="true" />
                    </flux:table.cell>
                    <flux:table.cell>
                        <button wire:click="toggleActive({{ $testimonial->id }})" class="cursor-pointer">
                            @if ($testimonial->is_active)
                                <flux:badge color="lime" size="sm">Aktif</flux:badge>
                            @else
                                <flux:badge color="zinc" size="sm">Nonaktif</flux:badge>
                            @endif
                        </button>
                    </flux:table.cell>
                    <flux:table.cell>
                        <div class="flex items-center gap-2">
                            <flux:button wire:click="edit({{ $testimonial->id }})" size="sm" variant="ghost" icon="pencil" />
                            <flux:button
                                wire:click="copy({{ $testimonial->id }})"
                                size="sm" variant="ghost" icon="document-duplicate"
                                tooltip="Salin testimoni"
                            />
                            <flux:button
                                wire:click="confirmDelete({{ $testimonial->id }})"
                                size="sm" variant="ghost" icon="trash"
                                class="text-red-500 hover:text-red-600"
                            />
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="5" class="py-8 text-center text-zinc-500">
                        Belum ada testimoni. Tambahkan testimoni pertama.
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="confirm-delete-testimonial" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus Testimoni?</flux:heading>
                <flux:text class="mt-2">
                    Yakin ingin menghapus testimoni dari <strong>"{{ $deletingName }}"</strong>? Tindakan ini tidak dapat dibatalkan.
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
