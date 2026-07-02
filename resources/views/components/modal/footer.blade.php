{{--
    Footer modal reusable (tombol Batal + aksi utama).

    Contoh:
        <x-modal.footer :submit="$editingId ? 'Perbarui' : 'Simpan'" />
        <x-modal.footer submit="Hapus" cancel="Tutup" cancel-action="resetForm" />

    Props:
    - submit        : label tombol utama (default: 'Simpan')
    - cancel        : label tombol batal (default: 'Batal')
    - cancel-action : method Livewire yang dipanggil saat batal (default: 'cancelForm', kosongkan untuk hanya menutup modal)
    - guarded       : jika true, tombol batal memanggil attemptClose() (guard perubahan belum disimpan)
                      alih-alih menutup modal langsung. Default: false.

    Slot (opsional): tombol/aksi tambahan yang diletakkan sebelum tombol utama.
--}}
@props([
    'submit' => 'Simpan',
    'cancel' => 'Batal',
    'cancelAction' => 'cancelForm',
    'guarded' => false,
])

<div class="border-t border-zinc-200 dark:border-zinc-700">
    <div class="flex shrink-0 justify-end gap-2 p-4">
        @if ($guarded)
            <flux:button size="sm" type="button" x-on:click="attemptClose()" variant="ghost">{{ $cancel }}</flux:button>
        @else
            <flux:modal.close>
                @if ($cancelAction)
                    <flux:button size="sm" type="button" wire:click="{{ $cancelAction }}" variant="ghost">{{ $cancel }}</flux:button>
                @else
                    <flux:button size="sm" type="button" variant="ghost">{{ $cancel }}</flux:button>
                @endif
            </flux:modal.close>
        @endif

        {{ $slot }}

        <flux:button size="sm" type="submit" variant="primary">
            {{ $submit }}
        </flux:button>
    </div>
</div>
