@php
    // Data dummy — nanti diganti sumber dinamis (Livewire/Eloquent).
    $kategori = [
        ['icon' => 'heart',          'title' => 'Pelatihan Medis',       'count' => 24, 'description' => 'Pelatihan klinis untuk dokter, perawat, dan tenaga medis lain sesuai standar kompetensi.'],
        ['icon' => 'beaker',         'title' => 'Penunjang & Lab',       'count' => 12, 'description' => 'Modul laboratorium, radiologi, farmasi, dan tenaga penunjang medis.'],
        ['icon' => 'briefcase',      'title' => 'Manajemen & Mutu',      'count' => 18, 'description' => 'Tata kelola, akreditasi, keselamatan pasien, dan manajemen rumah sakit.'],
        ['icon' => 'users',          'title' => 'Non-Medis & Umum',      'count' => 15, 'description' => 'Pelatihan administrasi, layanan publik, dan pengembangan SDM umum.'],
        ['icon' => 'shield-check',   'title' => 'K3 & Keselamatan',      'count' => 9,  'description' => 'Keselamatan kerja, pengendalian infeksi, dan kesiapsiagaan bencana.'],
        ['icon' => 'academic-cap',   'title' => 'Pelatihan Eksternal',   'count' => 7,  'description' => 'Program terbuka untuk peserta dari institusi mitra dan eksternal.'],
    ];

    $alur = [
        ['icon' => 'user-plus',          'title' => 'Buat Akun',        'description' => 'Daftar sebagai peserta medis, non-medis, manajemen, atau eksternal dengan data kepegawaian.'],
        ['icon' => 'magnifying-glass',   'title' => 'Pilih Pelatihan',  'description' => 'Telusuri katalog per kategori, cek jadwal sesi, dan kuota yang tersedia.'],
        ['icon' => 'pencil-square',      'title' => 'Ikuti Modul',      'description' => 'Akses materi e-learning, kerjakan evaluasi, dan ikuti sesi sesuai jadwal.'],
        ['icon' => 'document-check',     'title' => 'Unduh Sertifikat', 'description' => 'Sertifikat digital terbit otomatis dan tersinkron ke SISDMK.'],
    ];

    $jadwal = [
        ['title' => 'Bantuan Hidup Dasar (BHD) Angkatan 12', 'category' => 'Medis',           'date' => '02 Jul 2026', 'time' => '08.00–15.00', 'quota' => 'Sisa 8 kursi', 'status' => 'Dibuka'],
        ['title' => 'Pencegahan & Pengendalian Infeksi',     'category' => 'K3 & Keselamatan', 'date' => '05 Jul 2026', 'time' => '09.00–12.00', 'quota' => 'Sisa 3 kursi', 'status' => 'Hampir Penuh'],
        ['title' => 'Komunikasi Efektif Pelayanan Publik',   'category' => 'Non-Medis',        'date' => '08 Jul 2026', 'time' => '13.00–16.00', 'quota' => 'Sisa 20 kursi', 'status' => 'Dibuka'],
        ['title' => 'Manajemen Mutu & Akreditasi RS',        'category' => 'Manajemen',        'date' => '10 Jul 2026', 'time' => '08.00–16.00', 'quota' => 'Kuota penuh', 'status' => 'Penuh'],
    ];

    $berita = [
        ['icon' => 'megaphone',   'category' => 'Pengumuman', 'date' => '20 Jun 2026', 'title' => 'Pembukaan Pendaftaran Pelatihan Triwulan III 2026', 'excerpt' => 'Katalog pelatihan periode Juli–September telah dibuka untuk seluruh kategori peserta.'],
        ['icon' => 'newspaper',   'category' => 'Berita',     'date' => '15 Jun 2026', 'title' => 'Integrasi Sertifikat SIPAHAM dengan SISDMK', 'excerpt' => 'Seluruh sertifikat digital kini tersinkron otomatis ke sistem SISDMK Kemenkes.'],
        ['icon' => 'trophy',      'category' => 'Prestasi',   'date' => '10 Jun 2026', 'title' => 'RSUP H. Adam Malik Raih Predikat Diklat Terbaik', 'excerpt' => 'Capaian kompetensi SDM meningkat 32% sepanjang semester pertama 2026.'],
    ];

    $faqs = [
        ['question' => 'Siapa saja yang bisa mengikuti pelatihan di SIPAHAM?', 'answer' => 'Seluruh tenaga kesehatan dan non-kesehatan RSUP H. Adam Malik — medis, penunjang, manajemen — serta peserta eksternal dari institusi mitra dapat mendaftar sesuai kategori yang tersedia.'],
        ['question' => 'Bagaimana cara mendaftar pelatihan?', 'answer' => 'Buat akun terlebih dahulu, lengkapi data kepegawaian, lalu pilih pelatihan pada katalog dan daftar pada sesi dengan kuota yang masih tersedia.'],
        ['question' => 'Apakah sertifikat yang diterbitkan resmi?', 'answer' => 'Ya. Sertifikat digital terbit otomatis setelah peserta menyelesaikan modul dan evaluasi, serta tersinkron langsung ke sistem SISDMK Kementerian Kesehatan.'],
        ['question' => 'Apakah pelatihan dilakukan secara daring atau luring?', 'answer' => 'Tergantung jenis pelatihan. Sebagian modul tersedia penuh secara e-learning, sebagian lainnya menggabungkan sesi luring sesuai jadwal yang tercantum.'],
        ['question' => 'Bagaimana manajemen memantau kompetensi SDM?', 'answer' => 'Manajemen RS memperoleh rekap kompetensi SDM secara terpusat — capaian pelatihan, sertifikasi, dan riwayat peserta dapat dipantau dari satu dasbor.'],
    ];
@endphp

<x-layouts::guest :title="__('Beranda')">

    {{-- ================= HERO ================= --}}
    <section id="hero" class="relative overflow-hidden pt-16">
        {{-- dekorasi background --}}
        <div aria-hidden="true" class="pointer-events-none absolute inset-0 -z-10">
            <div class="absolute inset-0 bg-gradient-to-b from-brand-50/80 to-white dark:from-brand-950/30 dark:to-zinc-950"></div>
            <div class="absolute -right-24 -top-24 size-96 rounded-full bg-brand-200/40 blur-3xl dark:bg-brand-800/20"></div>
        </div>

        <div class="mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:py-24 lg:px-8">
            <div>
                <span class="inline-flex items-center gap-2 rounded-full border border-brand-200 bg-white px-3 py-1 text-xs font-semibold text-brand-700 dark:border-brand-800 dark:bg-zinc-900 dark:text-brand-400">
                    <span class="flex size-2 rounded-full bg-brand-500"></span>
                    Sistem Pelatihan RSUP H. Adam Malik
                </span>

                <h1 class="mt-6 text-4xl font-bold leading-tight tracking-tight text-zinc-900 sm:text-5xl lg:text-6xl dark:text-white">
                    Tingkatkan Kompetensi <span class="text-brand-600 dark:text-brand-400">Tenaga Kesehatan</span> dalam Satu Platform
                </h1>

                <p class="mt-6 max-w-xl text-lg leading-relaxed text-zinc-600 dark:text-zinc-300">
                    SIPAHAM mengelola seluruh siklus pelatihan — dari pendaftaran, modul e-learning, hingga sertifikat digital yang terintegrasi langsung ke SISDMK.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-brand-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-brand-700">
                            Daftar Pelatihan
                            <flux:icon name="arrow-right" class="size-4" />
                        </a>
                    @endif
                    <a href="#katalog" class="inline-flex items-center gap-2 rounded-xl border border-zinc-300 bg-white px-6 py-3 text-sm font-semibold text-zinc-700 transition-colors hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:bg-zinc-800">
                        Lihat Katalog
                    </a>
                </div>

                <div class="mt-8 flex items-center gap-6 text-sm text-zinc-500 dark:text-zinc-400">
                    <span class="inline-flex items-center gap-2"><flux:icon name="check-circle" class="size-5 text-brand-600" /> Sertifikat digital</span>
                    <span class="inline-flex items-center gap-2"><flux:icon name="check-circle" class="size-5 text-brand-600" /> Terintegrasi SISDMK</span>
                </div>
            </div>

            {{-- kartu visual hero --}}
            <div class="relative">
                <div class="rounded-3xl border border-zinc-200 bg-white p-6 shadow-xl dark:border-zinc-800 dark:bg-zinc-900">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="flex size-11 items-center justify-center rounded-xl bg-brand-600 text-white"><flux:icon name="play-circle" class="size-6" /></span>
                            <div>
                                <p class="text-sm font-semibold text-zinc-900 dark:text-white">Modul Berjalan</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">Bantuan Hidup Dasar</p>
                            </div>
                        </div>
                        <span class="rounded-full bg-brand-50 px-3 py-1 text-xs font-semibold text-brand-700 dark:bg-brand-500/10 dark:text-brand-400">Aktif</span>
                    </div>

                    <div class="mt-5">
                        <div class="flex items-center justify-between text-xs font-medium text-zinc-500 dark:text-zinc-400">
                            <span>Progres</span><span>75%</span>
                        </div>
                        <div class="mt-2 h-2 w-full overflow-hidden rounded-full bg-zinc-100 dark:bg-zinc-800">
                            <div class="h-full w-3/4 rounded-full bg-gradient-to-r from-brand-500 to-brand-700"></div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-3 gap-3">
                        <div class="rounded-xl bg-zinc-50 p-3 text-center dark:bg-zinc-800/60">
                            <p class="text-lg font-bold text-zinc-900 dark:text-white">85</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Modul</p>
                        </div>
                        <div class="rounded-xl bg-zinc-50 p-3 text-center dark:bg-zinc-800/60">
                            <p class="text-lg font-bold text-zinc-900 dark:text-white">12</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Sertifikat</p>
                        </div>
                        <div class="rounded-xl bg-zinc-50 p-3 text-center dark:bg-zinc-800/60">
                            <p class="text-lg font-bold text-zinc-900 dark:text-white">A</p>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Nilai</p>
                        </div>
                    </div>
                </div>

                {{-- badge mengambang --}}
                <div class="absolute -bottom-5 -left-5 hidden items-center gap-3 rounded-2xl border border-zinc-200 bg-white p-4 shadow-lg sm:flex dark:border-zinc-800 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400"><flux:icon name="document-check" class="size-5" /></span>
                    <div>
                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">Sertifikat Terbit</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Tersinkron ke SISDMK</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ================= STATISTIK ================= --}}
    <section class="border-y border-zinc-200 bg-zinc-50/60 dark:border-zinc-800 dark:bg-zinc-900/40">
        <dl class="mx-auto grid max-w-7xl grid-cols-2 gap-8 px-4 py-12 sm:px-6 lg:grid-cols-4 lg:px-8">
            <x-landing.stat icon="rectangle-stack" value="85+" label="Modul Pelatihan" />
            <x-landing.stat icon="users" value="3.200+" label="Peserta Terdaftar" />
            <x-landing.stat icon="document-check" value="12.500+" label="Sertifikat Terbit" />
            <x-landing.stat icon="building-office-2" value="6" label="Kategori Bidang" />
        </dl>
    </section>

    {{-- ================= KATALOG PELATIHAN ================= --}}
    <section id="katalog" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <x-landing.section-heading
            eyebrow="Katalog Pelatihan"
            title="Pelatihan untuk Setiap Bidang"
            subtitle="Modul tersusun per kategori kompetensi agar peserta mudah menemukan pelatihan yang relevan dengan profesinya." />

        <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($kategori as $item)
                <x-landing.category-card
                    :icon="$item['icon']"
                    :title="$item['title']"
                    :count="$item['count']"
                    :description="$item['description']"
                    href="#jadwal" />
            @endforeach
        </div>
    </section>

    {{-- ================= ALUR PENDAFTARAN ================= --}}
    <section id="alur" class="border-y border-zinc-200 bg-zinc-50/60 dark:border-zinc-800 dark:bg-zinc-900/40">
        <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <x-landing.section-heading
                eyebrow="Alur Pendaftaran"
                title="Empat Langkah Mudah"
                subtitle="Dari membuat akun hingga sertifikat di tangan — seluruh proses dilakukan dalam satu platform." />

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($alur as $i => $item)
                    <x-landing.step
                        :number="$i + 1"
                        :icon="$item['icon']"
                        :title="$item['title']"
                        :description="$item['description']" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= JADWAL ================= --}}
    <section id="jadwal" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
            <x-landing.section-heading
                :center="false"
                eyebrow="Jadwal Sesi"
                title="Jadwal Pelatihan Terdekat"
                subtitle="Cek jadwal sesi dan kuota yang tersedia, lalu daftar sebelum kuota penuh." />
            <a href="#" class="inline-flex shrink-0 items-center gap-1 text-sm font-semibold text-brand-600 dark:text-brand-400">
                Lihat semua jadwal <flux:icon name="arrow-right" class="size-4" />
            </a>
        </div>

        <div class="mt-10 flex flex-col gap-4">
            @foreach ($jadwal as $sesi)
                <x-landing.schedule-item
                    :title="$sesi['title']"
                    :category="$sesi['category']"
                    :date="$sesi['date']"
                    :time="$sesi['time']"
                    :quota="$sesi['quota']"
                    :status="$sesi['status']" />
            @endforeach
        </div>
    </section>

    {{-- ================= BERITA ================= --}}
    <section id="berita" class="border-y border-zinc-200 bg-zinc-50/60 dark:border-zinc-800 dark:bg-zinc-900/40">
        <div class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
            <x-landing.section-heading
                eyebrow="Berita & Pengumuman"
                title="Kabar Terbaru Diklat"
                subtitle="Informasi pembukaan pelatihan, pengumuman, dan capaian terbaru RSUP H. Adam Malik." />

            <div class="mt-12 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($berita as $item)
                    <x-landing.news-card
                        :icon="$item['icon']"
                        :category="$item['category']"
                        :date="$item['date']"
                        :title="$item['title']"
                        :excerpt="$item['excerpt']" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- ================= FAQ ================= --}}
    <section id="faq" class="mx-auto max-w-3xl px-4 py-20 sm:px-6 lg:px-8">
        <x-landing.section-heading
            eyebrow="FAQ"
            title="Pertanyaan yang Sering Diajukan" />

        <div class="mt-10" x-data="{ active: 1 }">
            @foreach ($faqs as $faq)
                <x-landing.faq :id="$loop->iteration" :question="$faq['question']">
                    {{ $faq['answer'] }}
                </x-landing.faq>
            @endforeach
        </div>
    </section>

    {{-- ================= CTA ================= --}}
    <section class="mx-auto max-w-7xl px-4 pb-20 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 to-brand-800 px-6 py-14 text-center shadow-xl sm:px-12">
            <div aria-hidden="true" class="pointer-events-none absolute -right-16 -top-16 size-64 rounded-full bg-white/10 blur-2xl"></div>
            <h2 class="relative text-3xl font-bold tracking-tight text-white sm:text-4xl">Siap mengembangkan kompetensi Anda?</h2>
            <p class="relative mx-auto mt-4 max-w-2xl text-base leading-relaxed text-brand-50">
                Bergabunglah dengan ribuan tenaga kesehatan yang telah meningkatkan kompetensinya melalui SIPAHAM.
            </p>
            <div class="relative mt-8 flex flex-wrap justify-center gap-3">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-semibold text-brand-700 shadow-sm transition-colors hover:bg-brand-50">
                        Daftar Sekarang
                        <flux:icon name="arrow-right" class="size-4" />
                    </a>
                @endif
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 rounded-xl border border-white/30 px-6 py-3 text-sm font-semibold text-white transition-colors hover:bg-white/10">
                    Masuk Akun
                </a>
            </div>
        </div>
    </section>

</x-layouts::guest>
