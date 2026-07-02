@php
    use App\Enums\PostStatus;
    use App\Models\Faq;
    use App\Models\Pelatihan;
    use App\Models\PelatihanCategory;
    use App\Models\Post;
    use App\Models\Testimonial;

    $modeLabels = ['offline' => 'Luring', 'online' => 'Daring', 'hybrid' => 'Hybrid'];

    $kategori = PelatihanCategory::withCount(['pelatihans' => fn ($query) => $query->active()->published()])
        ->get()
        ->filter(fn ($category) => $category->pelatihans_count > 0)
        ->sortByDesc('pelatihans_count')
        ->take(6)
        ->values();

    $alur = [
        ['title' => 'Buat Akun',        'description' => 'Daftar dengan NIP pegawai atau data diri untuk peserta eksternal.'],
        ['title' => 'Pilih Pelatihan',  'description' => 'Telusuri katalog dan pilih modul sesuai peran serta kebutuhan.'],
        ['title' => 'Ikuti Sesi',       'description' => 'Belajar daring atau luring sesuai jadwal yang tersedia.'],
        ['title' => 'Dapatkan Sertifikat', 'description' => 'Selesaikan evaluasi dan unduh sertifikat digital resmi.'],
    ];

    $jadwal = Pelatihan::with('category')
        ->active()
        ->published()
        ->where('start_date', '>=', now())
        ->orderBy('start_date')
        ->limit(4)
        ->get();

    $testimoni = Testimonial::active()->latest()->get();

    $berita = Post::query()
        ->where('status', PostStatus::Published)
        ->with(['categories'])
        ->latest('published_at')
        ->limit(3)
        ->get();

    $faqs = Faq::active()->orderBy('order')->get();
@endphp

<x-layouts::guest :title="__('Beranda')">

    {{-- ============ HERO ============ --}}
    <section id="hero" class="pt-16">
        <div class="mx-auto grid max-w-7xl items-center gap-14 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:py-[72px] lg:px-8">

            {{-- Left --}}
            <div data-reveal="from-left">
                <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-brand-50 px-3.5 py-1.5 text-[13px] font-semibold text-brand-800 dark:bg-teal-900/40 dark:text-teal-300">
                    <span class="inline-block size-[7px] rounded-full bg-brand-600 dark:bg-teal-400"></span>
                    Platform pelatihan resmi RSUP H. Adam Malik
                </div>

                <h1 class="font-heading font-extrabold leading-[1.06] tracking-[-0.03em] text-brand-950 dark:text-white" style="font-size:clamp(2.4rem,5vw,3.375rem)">
                    Kembangkan kompetensi tenaga rumah sakit dalam satu sistem SIMRS
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
            <div class="relative" data-reveal="from-right" style="transition-delay:150ms">
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
        <div class="mb-10 flex flex-wrap items-end justify-between gap-6" data-reveal>
            <x-landing.section-heading
                :center="false"
                eyebrow="Katalog Pelatihan"
                title="Kategori pelatihan untuk setiap peran" />
            <a href="#" class="shrink-0 text-[14.5px] font-bold text-brand-900 transition-colors hover:opacity-80 dark:text-teal-400">Lihat semua →</a>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($kategori as $item)
                <div data-reveal style="transition-delay:{{ $loop->index * 80 }}ms" class="flex">
                    <a href="#jadwal"
                       class="group flex w-full flex-col rounded-[18px] border border-zinc-200 bg-white p-6.5 transition-all hover:-translate-y-1 hover:shadow-[0_18px_40px_-22px_rgba(14,79,77,0.45)] dark:border-zinc-800 dark:bg-zinc-900 dark:hover:border-zinc-700">
                        <div class="mb-[18px] flex size-[50px] items-center justify-center rounded-[13px] {{ $loop->even ? 'bg-lime-50' : 'bg-brand-50' }}">
                            <flux:icon :name="$item->icon?->value ?? 'academic-cap'" class="size-6 text-brand-700" />
                        </div>
                        <h3 class="font-heading text-[19px] font-bold text-brand-950 dark:text-white">{{ $item->name }}</h3>
                        <p class="mt-2 flex-1 text-[14.5px] leading-relaxed text-zinc-500 dark:text-zinc-400">{{ $item->description ?: 'Pelatihan untuk pengembangan kompetensi terkait '.$item->name.'.' }}</p>
                        <div class="mt-4 text-[13px] font-semibold text-brand-600">{{ $item->pelatihans_count }} pelatihan</div>
                    </a>
                </div>
            @empty
                <p class="col-span-3 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">Belum ada kategori pelatihan.</p>
            @endforelse
        </div>
    </section>

    {{-- ============ ALUR ============ --}}
    <section id="alur" class="bg-brand-900">
        <div class="mx-auto max-w-7xl px-4 py-[72px] sm:px-6 lg:px-8">
            <div class="mb-12 text-center" data-reveal>
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
                        :description="$item['description']"
                        data-reveal :style="'transition-delay:'.($loop->index * 100).'ms'" />
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ JADWAL ============ --}}
    <section id="jadwal" class="mx-auto max-w-7xl px-4 py-[72px] sm:px-6 lg:px-8">
        <div class="mb-9 flex flex-wrap items-end justify-between gap-6" data-reveal>
            <x-landing.section-heading
                :center="false"
                eyebrow="Jadwal Mendatang"
                title="Pelatihan yang akan dibuka" />
            <a href="{{ route('kalender.index') }}" wire:navigate class="shrink-0 text-[14.5px] font-bold text-brand-900 transition-colors hover:opacity-80 dark:text-teal-400">Kalender penuh →</a>
        </div>

        <div class="flex flex-col gap-3.5">
            @forelse ($jadwal as $sesi)
                <x-landing.schedule-item
                    :title="$sesi->title"
                    :category="$sesi->category?->name ?? 'Umum'"
                    :day="$sesi->start_date->format('d')"
                    :month="$sesi->start_date->translatedFormat('M')"
                    :mode="$modeLabels[$sesi->mode].($sesi->location ? ' · '.$sesi->location : '')"
                    :quota="$sesi->quota ? $sesi->quota.' kuota tersedia' : null"
                    :status="$sesi->quota === 0 ? 'Penuh' : 'Dibuka'"
                    data-reveal :style="'transition-delay:'.($loop->index * 80).'ms'" />
            @empty
                <p class="py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">Belum ada jadwal pelatihan mendatang.</p>
            @endforelse
        </div>
    </section>

    {{-- ============ TESTIMONI ============ --}}
    @php $testimoniCount = $testimoni->count(); @endphp

    {{-- Marquee animation — mr-5 on each card ensures -50% is a perfect loop point --}}
    <style>
        @keyframes testimonial-scroll {
            from { transform: translateX(0); }
            to   { transform: translateX(-50%); }
        }
        .testimonial-track {
            width: max-content;
            animation: testimonial-scroll var(--dur, 24s) linear infinite;
            will-change: transform;
        }
        .testimonial-track.is-paused {
            animation-play-state: paused;
        }
        @media (prefers-reduced-motion: reduce) {
            .testimonial-track { animation: none; }
        }
    </style>

    <section class="bg-brand-50 dark:bg-zinc-900">
        <div class="mx-auto max-w-7xl px-4 py-[72px] sm:px-6 lg:px-8">
            <div class="mb-12 text-center" data-reveal>
                <x-landing.section-heading
                    eyebrow="Testimoni"
                    title="Apa kata peserta" />
            </div>

            <div
                x-data="{
                    paused: false,
                    _w: window.innerWidth,
                    get sliding() {
                        return this._w >= 1024 ? {{ $testimoniCount }} > 3 : {{ $testimoniCount }} > 1
                    },
                    init() {
                        window.addEventListener('resize', () => { this._w = window.innerWidth })
                    }
                }"
                class="relative"
            >
                {{-- Fade edges (hidden until slider activates) --}}
                <div
                    x-show="sliding"
                    x-cloak
                    class="pointer-events-none absolute inset-y-0 left-0 z-10 w-24 bg-linear-to-r from-brand-50 to-transparent dark:from-zinc-900"
                ></div>
                <div
                    x-show="sliding"
                    x-cloak
                    class="pointer-events-none absolute inset-y-0 right-0 z-10 w-24 bg-linear-to-l from-brand-50 to-transparent dark:from-zinc-900"
                ></div>

                {{-- Grid layout: visible when slider is off --}}
                <div x-show="!sliding" class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($testimoni as $t)
                        <x-landing.testimonial-card
                            :t="$t"
                            data-reveal
                            style="transition-delay:{{ $loop->index * 100 }}ms"
                        />
                    @endforeach
                </div>

                {{-- Slider layout: mr-5 on each card replaces gap so -50% = exact one-set width --}}
                <div x-show="sliding" x-cloak class="overflow-hidden">
                    <div
                        class="testimonial-track flex"
                        :class="{ 'is-paused': paused }"
                        style="--dur: {{ $testimoniCount * 8 }}s"
                        @mouseenter="paused = true"
                        @mouseleave="paused = false"
                    >
                        @foreach ($testimoni as $t)
                            <x-landing.testimonial-card :t="$t" class="w-80 flex-none mr-5 sm:w-96" />
                        @endforeach
                        @foreach ($testimoni as $t)
                            <x-landing.testimonial-card :t="$t" class="w-80 flex-none mr-5 sm:w-96" aria-hidden="true" />
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ============ BERITA ============ --}}
    <section id="berita" class="mx-auto max-w-7xl px-4 py-[72px] sm:px-6 lg:px-8">
        <div class="mb-9 flex flex-wrap items-end justify-between gap-6" data-reveal>
            <x-landing.section-heading
                :center="false"
                eyebrow="Berita & Pengumuman"
                title="Kabar terbaru SIPAHAM" />
            <a href="{{ route('berita.index') }}" class="shrink-0 text-[14.5px] font-bold text-brand-900 transition-colors hover:opacity-80 dark:text-teal-400">Semua berita →</a>
        </div>

        <div class="grid gap-5 lg:grid-cols-3">
            @forelse ($berita as $post)
                <div data-reveal style="transition-delay:{{ $loop->index * 100 }}ms" class="flex">
                    <x-landing.news-card
                        :title="$post->title"
                        :categories="$post->categories->pluck('name')->toArray()"
                        :date="($post->published_at ?? $post->created_at)->translatedFormat('d M Y')"
                        :excerpt="$post->excerpt != '' ? $post->excerpt : Str::limit(strip_tags($post->content), 120)"
                        :image="$post->featured_image ? Storage::url($post->featured_image) : null"
                        :href="route('berita.show', $post->slug)"
                        class="w-full"
                    />
                </div>
            @empty
                <p class="col-span-3 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">Belum ada berita.</p>
            @endforelse
        </div>
    </section>

    {{-- ============ FAQ ============ --}}
    <section id="faq" class="bg-white dark:bg-zinc-900">
        <div class="mx-auto max-w-3xl px-4 py-[72px] sm:px-6 lg:px-8">
            <div class="mb-11 text-center" data-reveal>
                <x-landing.section-heading
                    eyebrow="FAQ"
                    title="Pertanyaan yang sering diajukan" />
            </div>
            <div x-data="{ active: 1 }">
                @foreach ($faqs as $faq)
                    <x-landing.faq :id="$loop->iteration" :question="$faq->question"
                        data-reveal :style="'transition-delay:'.($loop->index * 60).'ms'">
                        {{ $faq->answer }}
                    </x-landing.faq>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ CTA BAND ============ --}}
    <section class="mx-auto px-4 py-[72px] sm:px-6 lg:px-8">
        <div class="relative flex flex-wrap max-w-[1216px] m-auto items-center justify-between gap-10 overflow-hidden rounded-3xl bg-brand-900 px-14 py-14" data-reveal>
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

            @auth
                <a href="{{ route('dashboard') }}"
                class="inline-flex items-center rounded-xl bg-lime px-7 py-3.75 text-[15.5px] font-bold text-brand-950 transition-opacity hover:opacity-90">
                    Ambil Pelatihan
                </a>
            @else
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
            @endauth
        </div>
    </section>

</x-layouts::guest>
