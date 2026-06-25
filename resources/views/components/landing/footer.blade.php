@php
    $year = now()->year;
@endphp

<footer class="border-t border-zinc-200 bg-zinc-50 dark:border-zinc-800 dark:bg-zinc-950">
    <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-4">
            <div class="lg:col-span-1">
                <x-landing.brand />
                <p class="mt-4 max-w-xs text-sm leading-relaxed text-zinc-600 dark:text-zinc-400">
                    Sistem Pelatihan RSUP H. Adam Malik — platform e-learning internal untuk pengembangan kompetensi tenaga kesehatan.
                </p>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">Navigasi</h4>
                <ul class="mt-4 space-y-3 text-sm">
                    <li><a href="#katalog" class="text-zinc-600 hover:text-brand-600 dark:text-zinc-400">Katalog Pelatihan</a></li>
                    <li><a href="#jadwal" class="text-zinc-600 hover:text-brand-600 dark:text-zinc-400">Jadwal Sesi</a></li>
                    <li><a href="#alur" class="text-zinc-600 hover:text-brand-600 dark:text-zinc-400">Alur Pendaftaran</a></li>
                    <li><a href="#berita" class="text-zinc-600 hover:text-brand-600 dark:text-zinc-400">Berita &amp; Pengumuman</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">Bantuan</h4>
                <ul class="mt-4 space-y-3 text-sm">
                    <li><a href="#faq" class="text-zinc-600 hover:text-brand-600 dark:text-zinc-400">FAQ</a></li>
                    <li><a href="{{ route('login') }}" class="text-zinc-600 hover:text-brand-600 dark:text-zinc-400">Masuk Akun</a></li>
                    @if (Route::has('register'))
                        <li><a href="{{ route('register') }}" class="text-zinc-600 hover:text-brand-600 dark:text-zinc-400">Buat Akun</a></li>
                    @endif
                    <li><span class="text-zinc-600 dark:text-zinc-400">Integrasi SISDMK</span></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-semibold text-zinc-900 dark:text-white">Kontak</h4>
                <ul class="mt-4 space-y-3 text-sm text-zinc-600 dark:text-zinc-400">
                    <li class="flex items-start gap-2"><flux:icon name="map-pin" class="mt-0.5 size-4 shrink-0 text-brand-600" /> Jl. Bunga Lau No. 17, Medan</li>
                    <li class="flex items-center gap-2"><flux:icon name="envelope" class="size-4 shrink-0 text-brand-600" /> diklat@rsham.co.id</li>
                    <li class="flex items-center gap-2"><flux:icon name="phone" class="size-4 shrink-0 text-brand-600" /> (061) 8364581</li>
                </ul>
            </div>
        </div>

        <div class="mt-10 flex flex-col items-center justify-between gap-4 border-t border-zinc-200 pt-6 sm:flex-row dark:border-zinc-800">
            <p class="text-sm text-zinc-500 dark:text-zinc-400">&copy; {{ $year }} SIPAHAM — RSUP H. Adam Malik. Hak cipta dilindungi.</p>
            <p class="text-xs text-zinc-400 dark:text-zinc-500">Bidang Pendidikan &amp; Penelitian (Diklat)</p>
        </div>
    </div>
</footer>
