{{--
    Dialog konfirmasi "buang perubahan" untuk form modal.

    Ditempatkan DI DALAM elemen x-data="formGuard(...)" agar tombolnya
    bisa memanggil method guard (confirmLeave). Nama modal harus sama
    dengan `confirm` pada konfigurasi formGuard.

    Contoh:
        <div x-data="formGuard({ prop: 'showForm', modal: 'category-form', confirm: 'confirm-leave' })">
            ...
            <x-modal.confirm-leave name="confirm-leave" />
        </div>

    Props:
    - name : nama unik modal konfirmasi (default: 'confirm-leave')
--}}
@props(['name' => 'confirm-leave'])

<flux:modal :name="$name" class="min-w-88">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">Tutup Form?</flux:heading>
            <flux:text class="mt-2">Ada perubahan yang belum disimpan. Yakin ingin menutup form ini? Perubahan Anda akan hilang.</flux:text>
        </div>
        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">Tetap di Sini</flux:button>
            </flux:modal.close>
            <flux:button x-on:click="confirmLeave()" variant="danger">Buang Perubahan</flux:button>
        </div>
    </div>
</flux:modal>
