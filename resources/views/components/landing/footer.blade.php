@php
    $year = now()->year;
@endphp

<footer class="bg-brand-950 text-brand-100">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
        <div class="grid gap-10 lg:grid-cols-4">
            {{-- Brand --}}
            <div class="lg:col-span-1">
                <a href="{{ route('home') }}" class="flex items-center gap-3" wire:navigate>
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-white/12">
                        <img src="{{ asset('logo.png') }}" alt="SIPAHAM" class="size-7 object-contain">
                    </span>
                    <span class="font-heading text-[19px] font-extrabold text-white">SIPAHAM</span>
                </a>
                <p class="mt-4 max-w-70 text-sm leading-relaxed text-brand-200">
                    Sistem Pelatihan RSUP H. Adam Malik — wadah pengembangan kompetensi tenaga rumah sakit secara terintegrasi.
                </p>
            </div>

            {{-- Navigasi --}}
            <div>
                <h4 class="font-heading text-sm font-bold text-white mb-4">Navigasi</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="#katalog" class="text-brand-100 transition-colors hover:text-lime">Katalog Pelatihan</a></li>
                    <li><a href="#jadwal" class="text-brand-100 transition-colors hover:text-lime">Jadwal Sesi</a></li>
                    <li><a href="#alur" class="text-brand-100 transition-colors hover:text-lime">Alur Pendaftaran</a></li>
                    <li><a href="#berita" class="text-brand-100 transition-colors hover:text-lime">Berita &amp; Pengumuman</a></li>
                </ul>
            </div>

            {{-- Akun --}}
            <div>
                <h4 class="font-heading text-sm font-bold text-white mb-4">Akun</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="{{ route('login') }}" class="text-brand-100 transition-colors hover:text-lime">Masuk</a></li>
                    @if (Route::has('register'))
                        <li><a href="{{ route('register') }}" class="text-brand-100 transition-colors hover:text-lime">Daftar peserta</a></li>
                    @endif
                    <li><a href="#faq" class="text-brand-100 transition-colors hover:text-lime">Bantuan</a></li>
                    <li><span class="text-brand-100">Integrasi SISDMK</span></li>
                </ul>
            </div>

            {{-- Kontak --}}
            <div>
                <h4 class="font-heading text-sm font-bold text-white mb-4">Kontak</h4>
                <div class="text-sm leading-[1.7] text-brand-200">
                    Jl. Bunga Lau No. 17, Medan<br>
                    Sumatera Utara 20136<br>
                    (061) 8364581<br>
                    diklat@rsham.co.id
                </div>
            </div>
        </div>
    </div>

    <div class="border-t border-white/8">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-4 py-5 sm:flex-row sm:px-6 lg:px-8">
            <span class="text-sm text-brand-300">&copy; {{ $year }} RSUP H. Adam Malik &middot; Bagian Diklat</span>
            <span class="text-sm text-brand-300">Kebijakan Privasi &middot; Syarat &amp; Ketentuan</span>
        </div>
    </div>
</footer>
