{{--
    Header modal reusable (judul sticky dengan garis bawah).

    Contoh:
        <x-modal.header :title="$editingId ? 'Edit Pelatihan' : 'Tambah Pelatihan'" />

    Atau pakai slot untuk konten kustom (mis. judul + subjudul):
        <x-modal.header>
            <span class="font-bold">Judul</span>
            <flux:text class="mt-1">Subjudul</flux:text>
        </x-modal.header>

    Props:
    - title    : teks judul. Jika diisi, dibungkus <span class="font-bold">. Abaikan untuk memakai slot.
    - closable : jika true, tampilkan tombol X yang memanggil attemptClose() (guard form modal). Default: false.
--}}
@props(['title' => null, 'closable' => false])

<div class="flex shrink-0 items-center gap-3 border-b border-zinc-200 pt-5 p-4 dark:border-zinc-700">
    <flux:heading size="lg" class="min-w-0 flex-1">
        @isset($title)
            <span class="font-bold">{{ $title }}</span>
        @else
            {{ $slot }}
        @endisset
    </flux:heading>

    @if ($closable)
        <flux:button
            type="button"
            x-on:click="attemptClose()"
            icon="x-mark"
            variant="ghost"
            size="sm"
            class="shrink-0"
        />
    @endif
</div>
