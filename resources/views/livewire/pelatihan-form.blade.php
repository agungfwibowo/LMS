<div class="flex h-full w-full flex-1 flex-col gap-4">
    <div class="flex items-center gap-3">
        <flux:button href="{{ route('pelatihan.index') }}" wire:navigate variant="ghost" icon="arrow-left" size="sm" />
        <flux:heading size="xl" level="1">
            {{ $pelatihan ? 'Edit Pelatihan' : 'Tambah Pelatihan' }}
        </flux:heading>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Form Pelatihan (kiri, lebih ramping) --}}
        <form wire:submit="save" class="space-y-4 lg:col-span-1">
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <flux:input wire:model.live="title" label="Judul Pelatihan" placeholder="Contoh: Pelatihan BHD" required />
                <flux:input wire:model="slug" label="Slug" placeholder="pelatihan-bhd" required />
            </div>

            <flux:select wire:model="pelatihanCategoryId" label="Kategori" placeholder="Pilih kategori...">
                <flux:select.option value="">— Tanpa kategori —</flux:select.option>
                @foreach ($this->categories as $category)
                    <flux:select.option :value="$category->id">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>

            <flux:textarea wire:model="description" label="Deskripsi" placeholder="Deskripsi / materi pelatihan..." rows="4" />

            {{-- Thumbnail --}}
            <div>
                <flux:label>Thumbnail / Poster</flux:label>
                <div class="mt-2 space-y-2">
                    @if ($uploadedThumbnail)
                        <img src="{{ $uploadedThumbnail->temporaryUrl() }}" class="h-32 rounded-lg object-cover" alt="Preview">
                    @elseif ($existingThumbnail)
                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($existingThumbnail) }}" class="h-32 rounded-lg object-cover" alt="Thumbnail saat ini">
                    @endif
                    <flux:input type="file" wire:model="uploadedThumbnail" accept="image/*" />
                    @error('uploadedThumbnail') <flux:error>{{ $message }}</flux:error> @enderror
                </div>
            </div>

            {{-- Jadwal --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <flux:input type="datetime-local" wire:model="startDate" label="Mulai" />
                <flux:input type="datetime-local" wire:model="endDate" label="Selesai" />
            </div>

            {{-- Pelaksanaan --}}
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <flux:select wire:model="mode" label="Mode">
                    <flux:select.option value="offline">Offline</flux:select.option>
                    <flux:select.option value="online">Online</flux:select.option>
                    <flux:select.option value="hybrid">Hybrid</flux:select.option>
                </flux:select>
                <flux:input wire:model="location" label="Lokasi" placeholder="Aula RS / Zoom" />
            </div>

            <flux:input wire:model="instructor" label="Pemateri / Narasumber" placeholder="dr. Andi, Sp.PD" />

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <flux:input type="number" wire:model="quota" label="Kuota Peserta" placeholder="Kosongkan jika tanpa batas" min="0" />
                <flux:input type="number" wire:model="price" label="Biaya (Rp)" placeholder="0 = gratis" min="0" />
            </div>

            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <flux:select wire:model="status" label="Status">
                    @foreach (\App\Enums\PelatihanStatus::cases() as $case)
                        <flux:select.option :value="$case->value">{{ $case->label() }}</flux:select.option>
                    @endforeach
                </flux:select>
                <div class="flex items-end">
                    <flux:switch wire:model="isActive" label="Tampilkan di halaman publik" />
                </div>
            </div>

            <div class="flex gap-2 pt-2">
                <flux:button type="submit" variant="primary" class="flex-1">
                    {{ $pelatihan ? 'Perbarui Pelatihan' : 'Simpan Pelatihan' }}
                </flux:button>
                <flux:button href="{{ route('pelatihan.index') }}" wire:navigate variant="ghost">
                    Batal
                </flux:button>
            </div>
        </form>

        {{-- Panel Modul (kanan, lebih lebar) --}}
        <div class="lg:col-span-2">
            @if (! $pelatihan)
                <div class="flex h-full min-h-56 flex-col items-center justify-center gap-2 rounded-lg border border-dashed border-zinc-300 p-8 text-center dark:border-zinc-700">
                    <flux:icon.lock-closed class="size-6 text-zinc-400" />
                    <flux:text class="text-zinc-500">
                        Simpan pelatihan terlebih dahulu untuk mulai menambahkan modul.
                    </flux:text>
                </div>
            @else
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <flux:heading size="lg">Modul Pelatihan</flux:heading>
                        <flux:badge color="zinc" size="sm">{{ $this->modules->count() }} modul</flux:badge>
                    </div>
                    <flux:button wire:click="openModuleCreate" variant="primary" size="sm" icon="plus">
                        Tambah Modul
                    </flux:button>
                </div>

                <div class="mt-4 space-y-4">
                    @forelse ($this->modules as $moduleIndex => $module)
                        <div wire:key="module-{{ $module->id }}" class="rounded-lg border border-zinc-200 dark:border-zinc-700">
                            <div class="flex items-start gap-3 border-b border-zinc-200 p-3 dark:border-zinc-700">
                                <span class="mt-1 text-sm font-medium tabular-nums text-zinc-400">{{ $moduleIndex + 1 }}.</span>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold">{{ $module->title }}</p>
                                    @if ($module->description)
                                        <p class="mt-0.5 text-xs text-zinc-500">{{ $module->description }}</p>
                                    @endif
                                </div>
                                <div class="flex flex-col items-center gap-1">
                                    <flux:button wire:click="moveModuleUp({{ $module->id }})" size="xs" variant="ghost" icon="chevron-up" :disabled="$moduleIndex === 0" />
                                    <flux:button wire:click="moveModuleDown({{ $module->id }})" size="xs" variant="ghost" icon="chevron-down" :disabled="$moduleIndex === $this->modules->count() - 1" />
                                </div>
                                <div class="flex items-center gap-1">
                                    <flux:button wire:click="editModule({{ $module->id }})" size="sm" variant="ghost" icon="pencil" />
                                    <flux:button
                                        wire:click="copyModule({{ $module->id }})"
                                        size="sm" variant="ghost" icon="document-duplicate"
                                        tooltip="Salin modul"
                                    />
                                    <flux:button
                                        wire:click="confirmDeleteModule({{ $module->id }})"
                                        size="sm" variant="ghost" icon="trash"
                                        class="text-red-500 hover:text-red-600"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 p-3 md:grid-cols-2">
                                {{-- Video --}}
                                <div>
                                    <div class="flex items-center justify-between">
                                        <flux:text class="text-xs font-semibold uppercase text-zinc-400">Video ({{ $module->videos->count() }})</flux:text>
                                        <flux:button wire:click="openVideoCreate({{ $module->id }})" size="xs" variant="ghost" icon="plus">
                                            Tambah
                                        </flux:button>
                                    </div>
                                    <div class="mt-2 space-y-1.5">
                                        @forelse ($module->videos as $video)
                                            <div wire:key="video-{{ $video->id }}" class="flex items-center gap-2 rounded-md bg-zinc-50 px-2 py-1.5 text-xs dark:bg-zinc-800">
                                                <flux:icon.play-circle class="size-4 shrink-0 text-brand-500" />
                                                <span class="flex-1 truncate">{{ $video->title }}</span>
                                                <flux:badge color="zinc" size="sm">
                                                    {{ $video->source_type === \App\Enums\PelatihanVideoSourceType::Upload ? 'Upload' : 'Embed' }}
                                                </flux:badge>
                                                <div class="flex items-center gap-0.5">
                                                    <flux:button wire:click="moveVideoUp({{ $video->id }})" size="xs" variant="ghost" icon="chevron-up" />
                                                    <flux:button wire:click="moveVideoDown({{ $video->id }})" size="xs" variant="ghost" icon="chevron-down" />
                                                    <flux:button wire:click="editVideo({{ $video->id }})" size="xs" variant="ghost" icon="pencil" />
                                                    <flux:button wire:click="copyVideo({{ $video->id }})" size="xs" variant="ghost" icon="document-duplicate" tooltip="Salin video" />
                                                    <flux:button wire:click="confirmDeleteVideo({{ $video->id }})" size="xs" variant="ghost" icon="trash" class="text-red-500 hover:text-red-600" />
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-xs text-zinc-400">Belum ada video.</p>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Soal --}}
                                <div>
                                    <div class="flex items-center justify-between">
                                        <flux:text class="text-xs font-semibold uppercase text-zinc-400">Soal ({{ $module->questions->count() }})</flux:text>
                                        <flux:button wire:click="openQuestionCreate({{ $module->id }})" size="xs" variant="ghost" icon="plus">
                                            Tambah
                                        </flux:button>
                                    </div>
                                    <div class="mt-2 space-y-1.5">
                                        @forelse ($module->questions as $question)
                                            <div wire:key="question-{{ $question->id }}" class="flex items-center gap-2 rounded-md bg-zinc-50 px-2 py-1.5 text-xs dark:bg-zinc-800">
                                                <flux:icon.question-mark-circle class="size-4 shrink-0 text-brand-500" />
                                                <span class="flex-1 truncate">{{ $question->pertanyaan }}</span>
                                                <flux:badge color="zinc" size="sm">{{ $question->tipe->label() }}</flux:badge>
                                                <div class="flex items-center gap-0.5">
                                                    <flux:button wire:click="moveQuestionUp({{ $question->id }})" size="xs" variant="ghost" icon="chevron-up" />
                                                    <flux:button wire:click="moveQuestionDown({{ $question->id }})" size="xs" variant="ghost" icon="chevron-down" />
                                                    <flux:button wire:click="editQuestion({{ $question->id }})" size="xs" variant="ghost" icon="pencil" />
                                                    <flux:button wire:click="copyQuestion({{ $question->id }})" size="xs" variant="ghost" icon="document-duplicate" tooltip="Salin soal" />
                                                    <flux:button wire:click="confirmDeleteQuestion({{ $question->id }})" size="xs" variant="ghost" icon="trash" class="text-red-500 hover:text-red-600" />
                                                </div>
                                            </div>
                                        @empty
                                            <p class="text-xs text-zinc-400">Belum ada soal.</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="mt-4 rounded-lg border border-dashed border-zinc-300 p-8 text-center text-zinc-500 dark:border-zinc-700">
                            Belum ada modul. Tambahkan modul pertama untuk mulai mengisi video &amp; soal.
                        </div>
                    @endforelse
                </div>
            @endif
        </div>
    </div>

    {{-- Modal tambah/edit modul --}}
    <div x-data="formGuard({ prop: 'showModuleForm', modal: 'pelatihan-module-form', confirm: 'confirm-leave-module' })">
    <flux:modal name="pelatihan-module-form" wire:model.self="showModuleForm" @close="$wire.cancelModuleForm()" :dismissible="false" :closable="false" class="w-full max-w-lg !p-0">
        <div class="flex max-h-[80vh] flex-col" @input="markDirty()" @change="markDirty()">
            <x-modal.header :title="$editingModuleId ? 'Edit Modul' : 'Tambah Modul'" closable />
            <div class="flex flex-1 flex-col overflow-y-auto">
                <form wire:submit="saveModule" @submit="onSubmit()" class="flex min-h-0 flex-1 flex-col">
                    <div class="flex min-h-0 flex-1 flex-col space-y-4 overflow-y-auto p-4">
                        <flux:input wire:model="moduleTitle" label="Judul Modul" placeholder="Contoh: Modul 1 - Pengenalan" required />
                        <flux:textarea wire:model="moduleDescription" label="Deskripsi (opsional)" placeholder="Ringkasan isi modul..." rows="3" />
                    </div>
                    <x-modal.footer :submit="$editingModuleId ? 'Perbarui Modul' : 'Simpan Modul'" guarded />
                </form>
            </div>
        </div>
    </flux:modal>

    <x-modal.confirm-leave name="confirm-leave-module" />
    </div>

    <flux:modal name="confirm-delete-pelatihan-module" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus Modul?</flux:heading>
                <flux:text class="mt-2">
                    Yakin ingin menghapus modul <strong>"{{ $deletingModuleTitle }}"</strong>? Seluruh video dan soal di dalamnya akan ikut terhapus. Tindakan ini tidak dapat dibatalkan.
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="deleteModule" variant="danger">Hapus</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal tambah/edit video --}}
    <div x-data="formGuard({ prop: 'showVideoForm', modal: 'pelatihan-video-form', confirm: 'confirm-leave-video' })">
    <flux:modal name="pelatihan-video-form" wire:model.self="showVideoForm" @close="$wire.cancelVideoForm()" :dismissible="false" :closable="false" class="w-full max-w-lg !p-0">
        <div class="flex max-h-[80vh] flex-col" @input="markDirty()" @change="markDirty()">
            <x-modal.header :title="$editingVideoId ? 'Edit Video' : 'Tambah Video'" closable />
            <div class="flex flex-1 flex-col overflow-y-auto">
                <form wire:submit="saveVideo" @submit="onSubmit()" class="flex min-h-0 flex-1 flex-col">
                    <div class="flex min-h-0 flex-1 flex-col space-y-4 overflow-y-auto p-4">
                        <flux:input wire:model="videoTitle" label="Judul Video" placeholder="Contoh: Pengenalan BHD" required />

                        <flux:select wire:model.live="videoSourceType" label="Sumber Video">
                            @foreach (\App\Enums\PelatihanVideoSourceType::cases() as $case)
                                <flux:select.option :value="$case->value">{{ $case->label() }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        @if ($videoSourceType === 'embed')
                            <flux:input wire:model="videoUrl" label="URL YouTube/Vimeo" placeholder="https://youtube.com/watch?v=..." required />
                        @else
                            <div>
                                <flux:label>Berkas Video</flux:label>
                                <div class="mt-2 space-y-2">
                                    @if ($uploadedVideo)
                                        <flux:text class="text-xs text-zinc-500">Berkas terpilih: {{ $uploadedVideo->getClientOriginalName() }}</flux:text>
                                    @elseif ($existingVideoFile)
                                        <flux:text class="text-xs text-zinc-500">Berkas saat ini: {{ basename($existingVideoFile) }}</flux:text>
                                    @endif
                                    <flux:input type="file" wire:model="uploadedVideo" accept="video/*" />
                                    @error('uploadedVideo') <flux:error>{{ $message }}</flux:error> @enderror
                                </div>
                            </div>
                        @endif

                        <flux:input type="number" wire:model="videoDurationSeconds" label="Durasi (detik, opsional)" min="0" />
                    </div>
                    <x-modal.footer :submit="$editingVideoId ? 'Perbarui Video' : 'Simpan Video'" guarded />
                </form>
            </div>
        </div>
    </flux:modal>

    <x-modal.confirm-leave name="confirm-leave-video" />
    </div>

    <flux:modal name="confirm-delete-pelatihan-video" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus Video?</flux:heading>
                <flux:text class="mt-2">
                    Yakin ingin menghapus video <strong>"{{ $deletingVideoTitle }}"</strong>? Tindakan ini tidak dapat dibatalkan.
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="deleteVideo" variant="danger">Hapus</flux:button>
            </div>
        </div>
    </flux:modal>

    {{-- Modal tambah/edit soal --}}
    <div x-data="formGuard({ prop: 'showQuestionForm', modal: 'pelatihan-question-form', confirm: 'confirm-leave-question' })">
    <flux:modal name="pelatihan-question-form" wire:model.self="showQuestionForm" @close="$wire.cancelQuestionForm()" :dismissible="false" :closable="false" class="w-full max-w-xl !p-0">
        <div class="flex max-h-[80vh] flex-col" @input="markDirty()" @change="markDirty()">
            <x-modal.header :title="$editingQuestionId ? 'Edit Soal' : 'Tambah Soal'" closable />
            <div class="flex flex-1 flex-col overflow-y-auto">
                <form wire:submit="saveQuestion" @submit="onSubmit()" class="flex min-h-0 flex-1 flex-col">
                    <div class="flex min-h-0 flex-1 flex-col space-y-4 overflow-y-auto p-4">
                        <flux:select wire:model.live="questionType" label="Tipe Soal">
                            @foreach (\App\Enums\PelatihanQuestionType::cases() as $case)
                                <flux:select.option :value="$case->value">{{ $case->label() }}</flux:select.option>
                            @endforeach
                        </flux:select>

                        <flux:textarea wire:model="questionText" label="Pertanyaan" placeholder="Tulis pertanyaan..." rows="3" required />

                        <flux:input type="number" wire:model="bobot" label="Bobot" min="1" required />

                        @if ($questionType === 'pilihan_ganda')
                            <div>
                                <flux:label>Pilihan Jawaban</flux:label>
                                <flux:description>Klik radio untuk menandai jawaban yang benar.</flux:description>
                                <div class="mt-2 space-y-2">
                                    @foreach ($options as $index => $option)
                                        <div class="flex items-center gap-2">
                                            <input
                                                type="radio"
                                                name="correct-option"
                                                wire:click="markCorrectOption({{ $index }})"
                                                @checked($option['is_correct'] ?? false)
                                                class="accent-brand-600"
                                            >
                                            <flux:input wire:model="options.{{ $index }}.text" placeholder="Teks pilihan..." class="flex-1" />
                                            <div class="flex items-center gap-0.5">
                                                <flux:button
                                                    type="button"
                                                    wire:click="moveOptionUp({{ $index }})"
                                                    size="sm" variant="ghost" icon="chevron-up"
                                                    :disabled="$index === 0"
                                                />
                                                <flux:button
                                                    type="button"
                                                    wire:click="moveOptionDown({{ $index }})"
                                                    size="sm" variant="ghost" icon="chevron-down"
                                                    :disabled="$index === count($options) - 1"
                                                />
                                            </div>
                                            <flux:button
                                                type="button"
                                                wire:click="removeOption({{ $index }})"
                                                size="sm" variant="ghost" icon="x-mark"
                                                :disabled="count($options) <= 2"
                                            />
                                        </div>
                                    @endforeach
                                </div>
                                @error('options') <flux:error>{{ $message }}</flux:error> @enderror
                                <flux:button
                                    type="button"
                                    wire:click="addOption"
                                    size="sm" variant="ghost" icon="plus"
                                    class="mt-2"
                                    :disabled="count($options) >= 6"
                                >
                                    Tambah Pilihan
                                </flux:button>
                            </div>
                        @elseif ($questionType === 'benar_salah')
                            <div>
                                <flux:label class="mb-2 block">Jawaban Benar</flux:label>
                                <flux:switch wire:model="correctAnswer" :label="$correctAnswer ? 'Benar' : 'Salah'" />
                            </div>
                        @elseif ($questionType === 'esai')
                            <flux:textarea
                                wire:model="kunciJawaban"
                                label="Kunci Jawaban / Catatan Penilai (opsional)"
                                placeholder="Referensi jawaban untuk membantu penilaian manual..."
                                rows="3"
                            />
                        @endif
                    </div>
                    <x-modal.footer :submit="$editingQuestionId ? 'Perbarui Soal' : 'Simpan Soal'" guarded />
                </form>
            </div>
        </div>
    </flux:modal>

    <x-modal.confirm-leave name="confirm-leave-question" />
    </div>

    <flux:modal name="confirm-delete-pelatihan-question" class="min-w-88">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">Hapus Soal?</flux:heading>
                <flux:text class="mt-2">
                    Yakin ingin menghapus soal <strong>"{{ $deletingQuestionText }}"</strong>? Tindakan ini tidak dapat dibatalkan.
                </flux:text>
            </div>
            <div class="flex gap-2">
                <flux:spacer />
                <flux:modal.close>
                    <flux:button variant="ghost">Batal</flux:button>
                </flux:modal.close>
                <flux:button wire:click="deleteQuestion" variant="danger">Hapus</flux:button>
            </div>
        </div>
    </flux:modal>
</div>
