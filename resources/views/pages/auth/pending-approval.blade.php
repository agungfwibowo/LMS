<x-layouts::auth :title="__('Menunggu Persetujuan')">
    <div class="flex flex-col gap-6 text-center">
        <div class="mx-auto flex size-14 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-900/40">
            <flux:icon.clock class="size-7 text-amber-600 dark:text-amber-400" />
        </div>

        <x-auth-header
            :title="__('Akun Menunggu Persetujuan')"
            :description="__('Akun Anda sudah terdaftar, tetapi perlu disetujui oleh admin sebelum dapat mengakses panel. Silakan hubungi admin atau coba lagi nanti.')"
        />

        <flux:text class="text-sm">
            Masuk sebagai <span class="font-medium">{{ auth()->user()->email }}</span>
        </flux:text>

        <form method="POST" action="{{ route('logout') }}" class="w-full">
            @csrf
            <flux:button type="submit" variant="outline" class="w-full" data-test="logout-button">
                {{ __('Keluar') }}
            </flux:button>
        </form>
    </div>
</x-layouts::auth>
