@php
    use App\Enums\PostStatus;
    use App\Models\Post;

    $kategori = [
        ['icon' => '🩺', 'bg' => 'bg-brand-50', 'title' => 'Keperawatan & Klinis',  'count' => 28, 'description' => 'Asuhan keperawatan, keselamatan pasien, dan kompetensi klinis lanjutan.'],
        ['icon' => '📋', 'bg' => 'bg-lime-50',  'title' => 'Manajemen RS',           'count' => 18, 'description' => 'Tata kelola, mutu pelayanan, dan kepemimpinan unit kerja.'],
        ['icon' => '🧑‍💼', 'bg' => 'bg-brand-50','title' => 'Administrasi & SDM',   'count' => 22, 'description' => 'Pelayanan publik, kearsipan, dan pengembangan kepegawaian.'],
        ['icon' => '🦺', 'bg' => 'bg-lime-50',  'title' => 'K3 Rumah Sakit',         'count' => 15, 'description' => 'Keselamatan kerja, kewaspadaan bencana, dan pengendalian infeksi.'],
        ['icon' => '💻', 'bg' => 'bg-brand-50', 'title' => 'Teknologi Informasi',    'count' => 12, 'description' => 'Rekam medis elektronik, SIMRS, dan literasi digital.'],
        ['icon' => '🤝', 'bg' => 'bg-lime-50',  'title' => 'Pelayanan Prima',        'count' => 19, 'description' => 'Komunikasi efektif, etika pelayanan, dan kepuasan pasien.'],
    ];

    $alur = [
        ['title' => 'Buat Akun',        'description' => 'Daftar dengan NIP pegawai atau data diri untuk peserta eksternal.'],
        ['title' => 'Pilih Pelatihan',  'description' => 'Telusuri katalog dan pilih modul sesuai peran serta kebutuhan.'],
        ['title' => 'Ikuti Sesi',       'description' => 'Belajar daring atau luring sesuai jadwal yang tersedia.'],
        ['title' => 'Dapatkan Sertifikat', 'description' => 'Selesaikan evaluasi dan unduh sertifikat digital resmi.'],
    ];

    $jadwal = [
        ['title' => 'Pelatihan Keselamatan Pasien (Patient Safety)', 'category' => 'Keperawatan', 'day' => '08', 'month' => 'Jul', 'mode' => 'Luring · Aula Diklat',  'quota' => 'Sisa 12 kuota', 'status' => 'Dibuka'],
        ['title' => 'Manajemen Mutu Pelayanan Rumah Sakit',          'category' => 'Manajemen',   'day' => '15', 'month' => 'Jul', 'mode' => 'Daring · Zoom',         'quota' => 'Sisa 40 kuota', 'status' => 'Dibuka'],
        ['title' => 'Pencegahan & Pengendalian Infeksi (PPI)',        'category' => 'K3RS',        'day' => '22', 'month' => 'Jul', 'mode' => 'Luring · Gedung A',     'quota' => 'Sisa 8 kuota',  'status' => 'Hampir Penuh'],
        ['title' => 'Pelayanan Publik & Komunikasi Efektif',          'category' => 'Administrasi','day' => '05', 'month' => 'Agu', 'mode' => 'Daring · LMS',          'quota' => 'Sisa 60 kuota', 'status' => 'Dibuka'],
    ];

    $testimoni = [
        ['quote' => 'Materi pelatihannya relevan dengan tugas harian saya di bangsal. Sertifikatnya juga langsung terbit di akun.', 'initials' => 'SR', 'bg' => 'bg-brand-50', 'name' => 'Siti Rahmawati', 'role' => 'Perawat Pelaksana'],
        ['quote' => 'Sebagai kepala unit, SIPAHAM memudahkan saya memantau kompetensi seluruh anggota tim secara terpusat.', 'initials' => 'BH', 'bg' => 'bg-lime-50',  'name' => 'Budi Hartono', 'role' => 'Kepala Instalasi'],
        ['quote' => 'Proses pendaftaran cepat dan jadwalnya jelas. Pelatihan daringnya fleksibel di sela jam kerja.', 'initials' => 'DA', 'bg' => 'bg-brand-50', 'name' => 'Dewi Anggraini', 'role' => 'Staf Administrasi'],
    ];

    $berita = Post::query()
        ->where('status', PostStatus::Published)
        ->with(['categories'])
        ->latest('published_at')
        ->limit(3)
        ->get();

    $faqs = [
        ['question' => 'Siapa saja yang bisa mendaftar?',             'answer' => 'Seluruh pegawai RSUP H. Adam Malik (medis, non-medis, manajemen) serta peserta eksternal yang memenuhi syarat dapat mendaftar melalui SIPAHAM.'],
        ['question' => 'Bagaimana cara masuk ke sistem?',             'answer' => 'Pegawai dapat masuk menggunakan NIP dan kata sandi terdaftar. Peserta eksternal menggunakan email yang didaftarkan saat registrasi.'],
        ['question' => 'Apakah pelatihan dipungut biaya?',            'answer' => 'Sebagian besar pelatihan internal gratis bagi pegawai. Pelatihan tertentu untuk peserta eksternal dapat dikenakan biaya yang tertera pada detail modul.'],
        ['question' => 'Apakah sertifikat diakui resmi?',             'answer' => 'Ya. Sertifikat diterbitkan secara digital oleh Bagian Diklat RS Adam Malik dan dapat terintegrasi dengan SISDMK.'],
        ['question' => 'Bagaimana jika berhalangan hadir pada sesi luring?', 'answer' => 'Anda dapat mengajukan penjadwalan ulang melalui akun selama kuota batch berikutnya masih tersedia.'],
    ];
@endphp

<x-layouts::guest :title="__('Beranda')">

    {{-- ============ HERO ============ --}}
    <section id="hero" class="pt-16">
        <div class="mx-auto grid max-w-7xl items-center gap-14 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:py-[72px] lg:px-8">

            {{-- Left --}}
            <div>
                <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-brand-50 px-3.5 py-1.5 text-[13px] font-semibold text-brand-800 dark:bg-teal-900/40 dark:text-teal-300">
                    <span class="inline-block size-[7px] rounded-full bg-brand-600 dark:bg-teal-400"></span>
                    Platform pelatihan resmi RSUP H. Adam Malik
                </div>

                <h1 class="font-heading font-extrabold leading-[1.06] tracking-[-0.03em] text-brand-950 dark:text-white" style="font-size:clamp(2.4rem,5vw,3.375rem)">
                    Kembangkan kompetensi tenaga rumah sakit dalam satu sistem
                </h1>

                <p class="mt-6 max-w-[520px] text-lg leading-relaxed text-zinc-600 dark:text-zinc-400">
                    SIPAHAM menghadirkan pelatihan terstruktur untuk seluruh tenaga medis, non-medis, dan manajemen — daftar, ikuti, dan kelola sertifikat Anda dengan mudah.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center gap-2 rounded-xl bg-lime px-7 py-3.75 text-[15.5px] font-bold text-brand-950 shadow-[0_4px_14px_rgba(160,176,20,0.35)] transition-opacity hover:opacity-90">
                            Mulai Pelatihan
                        </a>
                    @endif
                    <a href="#katalog"
                       class="inline-flex items-center gap-2 rounded-xl border border-zinc-200 bg-white px-7 py-3.75 text-[15.5px] font-bold text-brand-900 transition-colors hover:bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-800 dark:text-teal-400 dark:hover:bg-zinc-700">
                        Lihat Katalog
                    </a>
                </div>

                {{-- Stats --}}
                <div class="mt-12 flex flex-wrap items-center gap-0">
                    <div class="pr-10">
                        <div class="font-heading font-extrabold flex text-[30px] leading-none text-brand-800 dark:text-teal-400"><x-landing.counter value="120"/>+</div>
                        <div class="mt-1 text-[13.5px] font-medium text-zinc-500 dark:text-zinc-400">Modul pelatihan</div>
                    </div>
                    <div class="mx-0 h-10 w-px bg-zinc-200 dark:bg-zinc-700"></div>
                    <div class="px-10">
                        <div class="font-heading font-extrabold flex text-[30px] leading-none text-brand-800 dark:text-teal-400"><x-landing.counter value="8500"/>+</div>
                        <div class="mt-1 text-[13.5px] font-medium text-zinc-500 dark:text-zinc-400">Peserta aktif</div>
                    </div>
                    <div class="mx-0 h-10 w-px bg-zinc-200 dark:bg-zinc-700"></div>
                    <div class="pl-10">
                        <div class="font-heading font-extrabold flex text-[30px] leading-none text-brand-800 dark:text-teal-400"><x-landing.counter value="96"/>%</div>
                        <div class="mt-1 text-[13.5px] font-medium text-zinc-500 dark:text-zinc-400">Tingkat kelulusan</div>
                    </div>
                </div>
            </div>

            {{-- Right — widget card --}}
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
                            <div class="progress h-full w-3/4 rounded-full bg-linear-to-r from-brand-500 to-brand-700"></div>
                        </div>
                    </div>

                    <div class="mt-6 grid grid-cols-3 gap-3">
                        <div class="rounded-xl bg-zinc-50 p-3 text-center dark:bg-zinc-800/60">
                            <div class="text-lg font-bold text-zinc-900 dark:text-white">
                                <x-landing.counter value="85"/>
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Modul</p>
                        </div>
                        <div class="rounded-xl bg-zinc-50 p-3 text-center dark:bg-zinc-800/60">
                            <div class="text-lg font-bold text-zinc-900 dark:text-white">
                                <x-landing.counter value="12"/>
                            </div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Sertifikat</p>
                        </div>
                        <div class="rounded-xl bg-zinc-50 p-3 text-center dark:bg-zinc-800/60">
                            <div class="text-lg font-bold text-zinc-900 dark:text-white">A</div>
                            <p class="text-xs text-zinc-500 dark:text-zinc-400">Nilai</p>
                        </div>
                    </div>
                </div>

                {{-- Floating badge --}}
                <div class="animate-float absolute -bottom-5 -left-5 hidden items-center gap-3 rounded-2xl border border-zinc-200 bg-white p-4 shadow-lg sm:flex dark:border-zinc-800 dark:bg-zinc-900">
                    <span class="flex size-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600 dark:bg-brand-500/10 dark:text-brand-400"><flux:icon name="document-check" class="size-5" /></span>
                    <div>
                        <p class="text-sm font-semibold text-zinc-900 dark:text-white">Sertifikat Terbit</p>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Tersinkron ke SISDMK</p>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- ============ KATALOG ============ --}}
    <section id="katalog" class="mx-auto max-w-7xl px-4 py-20 sm:px-6 lg:px-8">
        <div class="mb-10 flex flex-wrap items-end justify-between gap-6">
            <x-landing.section-heading
                :center="false"
                eyebrow="Katalog Pelatihan"
                title="Kategori pelatihan untuk setiap peran" />
            <a href="#" class="shrink-0 text-[14.5px] font-bold text-brand-900 transition-colors hover:opacity-80 dark:text-teal-400">Lihat semua →</a>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($kategori as $item)
                <a href="#jadwal"
                   class="group flex flex-col rounded-[18px] border border-zinc-200 bg-white p-6.5 transition-all hover:-translate-y-1 hover:shadow-[0_18px_40px_-22px_rgba(14,79,77,0.45)] dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700">
                    <div class="mb-[18px] flex size-[50px] items-center justify-center rounded-[13px] text-2xl {{ $item['bg'] }}">{{ $item['icon'] }}</div>
                    <h3 class="font-heading text-[19px] font-bold text-brand-950 dark:text-white">{{ $item['title'] }}</h3>
                    <p class="mt-2 flex-1 text-[14.5px] leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $item['description'] }}</p>
                    <div class="mt-4 text-[13px] font-semibold text-brand-600">{{ $item['count'] }} modul</div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- ============ ALUR ============ --}}
    <section id="alur" class="bg-brand-900">
        <div class="mx-auto max-w-7xl px-4 py-[72px] sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <x-landing.section-heading
                    :dark="true"
                    eyebrow="Cara Mendaftar"
                    title="Empat langkah untuk mulai belajar" />
            </div>
            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ($alur as $i => $item)
                    <x-landing.step
                        variant="dark"
                        :number="$i + 1"
                        :title="$item['title']"
                        :description="$item['description']" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ JADWAL ============ --}}
    <section id="jadwal" class="mx-auto max-w-7xl px-4 py-[72px] sm:px-6 lg:px-8">
        <div class="mb-9 flex flex-wrap items-end justify-between gap-6">
            <x-landing.section-heading
                :center="false"
                eyebrow="Jadwal Mendatang"
                title="Pelatihan yang akan dibuka" />
            <a href="#" class="shrink-0 text-[14.5px] font-bold text-brand-900 transition-colors hover:opacity-80 dark:text-teal-400">Kalender penuh →</a>
        </div>

        <div class="flex flex-col gap-3.5">
            @foreach ($jadwal as $sesi)
                <x-landing.schedule-item
                    :title="$sesi['title']"
                    :category="$sesi['category']"
                    :day="$sesi['day']"
                    :month="$sesi['month']"
                    :mode="$sesi['mode']"
                    :quota="$sesi['quota']"
                    :status="$sesi['status']" />
            @endforeach
        </div>
    </section>

    {{-- ============ TESTIMONI ============ --}}
    <section class="bg-brand-50 dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl px-4 py-[72px] sm:px-6 lg:px-8">
            <div class="mb-12 text-center">
                <x-landing.section-heading
                    eyebrow="Testimoni"
                    title="Apa kata peserta" />
            </div>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($testimoni as $t)
                    <div class="flex flex-col rounded-[18px] border border-zinc-200 bg-white p-7 dark:border-zinc-700 dark:bg-zinc-800/60">
                        <div class="mb-2 font-heading text-[30px] font-extrabold leading-none text-lime">&ldquo;</div>
                        <p class="mb-6 flex-1 text-[15.5px] leading-[1.65] text-zinc-700 dark:text-zinc-300">{{ $t['quote'] }}</p>
                        <div class="flex items-center gap-3">
                            <div class="{{ $t['bg'] }} flex size-11 items-center justify-center rounded-full font-heading text-sm font-bold text-brand-900 dark:text-teal-300">{{ $t['initials'] }}</div>
                            <div>
                                <div class="text-[14.5px] font-bold text-brand-950 dark:text-white">{{ $t['name'] }}</div>
                                <div class="text-[13px] text-zinc-500 dark:text-zinc-400">{{ $t['role'] }}</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ BERITA ============ --}}
    <section id="berita" class="mx-auto max-w-7xl px-4 py-[72px] sm:px-6 lg:px-8">
        <div class="mb-9 flex flex-wrap items-end justify-between gap-6">
            <x-landing.section-heading
                :center="false"
                eyebrow="Berita & Pengumuman"
                title="Kabar terbaru SIPAHAM" />
            <a href="{{ route('berita.index') }}" class="shrink-0 text-[14.5px] font-bold text-brand-900 transition-colors hover:opacity-80 dark:text-teal-400">Semua berita →</a>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($berita as $post)
                <x-landing.news-card
                    :title="$post->title"
                    :categories="$post->categories->pluck('name')->toArray()"
                    :date="($post->published_at ?? $post->created_at)->translatedFormat('d M Y')"
                    :excerpt="$post->excerpt != '' ? $post->excerpt : Str::limit(strip_tags($post->content), 120)"
                    :image="$post->featured_image ? Storage::url($post->featured_image) : null"
                    :href="route('berita.show', $post->slug)"
                />
            @empty
                <p class="col-span-3 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">Belum ada berita.</p>
            @endforelse
        </div>
    </section>

    {{-- ============ FAQ ============ --}}
    <section id="faq" class="bg-brand-50 dark:bg-zinc-900">
        <div class="mx-auto max-w-3xl px-4 py-[72px] sm:px-6 lg:px-8">
            <div class="mb-11 text-center">
                <x-landing.section-heading
                    eyebrow="FAQ"
                    title="Pertanyaan yang sering diajukan" />
            </div>
            <div x-data="{ active: 1 }">
                @foreach ($faqs as $faq)
                    <x-landing.faq :id="$loop->iteration" :question="$faq['question']">
                        {{ $faq['answer'] }}
                    </x-landing.faq>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ CTA BAND ============ --}}
    <section class="mx-auto max-w-7xl px-4 py-[72px] sm:px-6 lg:px-8">
        <div class="relative flex flex-wrap items-center justify-between gap-10 overflow-hidden rounded-3xl bg-brand-900 px-14 py-14">
            {{-- Decorative circle --}}
            <div class="pointer-events-none absolute -right-10 -top-10 size-60 rounded-full bg-lime/16"></div>

            <div class="relative">
                <h2 class="font-heading font-extrabold text-white leading-[1.1] tracking-tight max-w-[520px]" style="font-size:clamp(1.6rem,3vw,2.125rem)">
                    Siap mengembangkan kompetensi tim Anda?
                </h2>
                <p class="mt-3.5 max-w-[480px] text-base text-zinc-300">
                    Masuk dengan akun pegawai RS Adam Malik atau daftar sebagai peserta baru.
                </p>
            </div>

            <div class="relative flex flex-wrap gap-3.5">
                @if (Route::has('register'))
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center rounded-xl bg-lime px-7 py-3.75 text-[15.5px] font-bold text-brand-950 transition-opacity hover:opacity-90">
                        Daftar Sekarang
                    </a>
                @endif
                <a href="{{ route('login') }}"
                   class="inline-flex items-center rounded-xl border border-white/30 px-7 py-3.75 text-[15.5px] font-bold text-white transition-colors hover:bg-white/10">
                    Masuk
                </a>
            </div>
        </div>
    </section>

</x-layouts::guest>
