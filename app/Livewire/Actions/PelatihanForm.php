<?php

namespace App\Livewire\Actions;

use App\Actions\Pelatihan\CopyPelatihanModule;
use App\Actions\Pelatihan\CopyPelatihanModuleVideo;
use App\Actions\Pelatihan\CopyPelatihanQuestion;
use App\Actions\Pelatihan\CreatePelatihan;
use App\Actions\Pelatihan\SavePelatihanModuleVideo;
use App\Actions\Pelatihan\SavePelatihanQuestion;
use App\Actions\Pelatihan\UpdatePelatihan;
use App\Enums\PelatihanQuestionType;
use App\Enums\PelatihanStatus;
use App\Enums\PelatihanVideoSourceType;
use App\Models\Pelatihan as PelatihanModel;
use App\Models\PelatihanCategory;
use App\Models\PelatihanModule;
use App\Models\PelatihanModuleVideo;
use App\Models\PelatihanQuestion;
use Flux\Flux;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;

class PelatihanForm extends Component
{
    use WithFileUploads;

    public ?PelatihanModel $pelatihan = null;

    public ?int $pelatihanCategoryId = null;

    public string $title = '';

    public string $slug = '';

    public string $description = '';

    public string $status = 'draft';

    public bool $isActive = true;

    public string $startDate = '';

    public string $endDate = '';

    public string $location = '';

    public string $mode = 'offline';

    public string $instructor = '';

    public ?int $quota = null;

    public ?int $price = null;

    public mixed $uploadedThumbnail = null;

    public ?string $existingThumbnail = null;

    // Modul form state
    public ?int $editingModuleId = null;

    public bool $showModuleForm = false;

    public string $moduleTitle = '';

    public string $moduleDescription = '';

    public ?int $deletingModuleId = null;

    public string $deletingModuleTitle = '';

    // Video form state
    public ?int $currentModuleId = null;

    public ?int $editingVideoId = null;

    public bool $showVideoForm = false;

    public string $videoTitle = '';

    public string $videoSourceType = 'embed';

    public string $videoUrl = '';

    public mixed $uploadedVideo = null;

    public ?string $existingVideoFile = null;

    public ?int $videoDurationSeconds = null;

    public ?int $deletingVideoId = null;

    public string $deletingVideoTitle = '';

    // Question (soal) form state
    public ?int $currentModuleIdForQuestion = null;

    public ?int $editingQuestionId = null;

    public bool $showQuestionForm = false;

    public string $questionType = 'pilihan_ganda';

    public string $questionText = '';

    public int $bobot = 1;

    public bool $correctAnswer = true;

    public string $kunciJawaban = '';

    public array $options = [];

    public ?int $deletingQuestionId = null;

    public string $deletingQuestionText = '';

    /**
     * Referensi modul/video/soal yang sedang dibuka di URL: '' (tertutup),
     * 'module-create', 'module-{id}', 'video-create-{moduleId}', 'video-{id}',
     * 'question-create-{moduleId}', atau 'question-{id}'.
     */
    #[Url(as: 'form', except: '')]
    public string $form = '';

    public function mount(?PelatihanModel $pelatihan = null): void
    {
        if ($pelatihan) {
            $this->pelatihan = $pelatihan;
            $this->pelatihanCategoryId = $pelatihan->pelatihan_category_id;
            $this->title = $pelatihan->title;
            $this->slug = $pelatihan->slug;
            $this->description = $pelatihan->description ?? '';
            $this->status = $pelatihan->status->value;
            $this->isActive = $pelatihan->is_active;
            $this->startDate = $pelatihan->start_date?->format('Y-m-d\TH:i') ?? '';
            $this->endDate = $pelatihan->end_date?->format('Y-m-d\TH:i') ?? '';
            $this->location = $pelatihan->location ?? '';
            $this->mode = $pelatihan->mode;
            $this->instructor = $pelatihan->instructor ?? '';
            $this->quota = $pelatihan->quota;
            $this->price = $pelatihan->price;
            $this->existingThumbnail = $pelatihan->thumbnail;

            $this->restoreFormFromUrl();
        }
    }

    private function restoreFormFromUrl(): void
    {
        if ($this->form === '') {
            return;
        }

        if ($this->form === 'module-create') {
            $this->openModuleCreate();

            return;
        }

        if (str_starts_with($this->form, 'module-') && ($module = PelatihanModule::find((int) substr($this->form, 7)))) {
            $this->editModule($module->id);

            return;
        }

        if (str_starts_with($this->form, 'video-create-') && PelatihanModule::find((int) substr($this->form, 13))) {
            $this->openVideoCreate((int) substr($this->form, 13));

            return;
        }

        if (str_starts_with($this->form, 'video-') && ($video = PelatihanModuleVideo::find((int) substr($this->form, 6)))) {
            $this->editVideo($video->id);

            return;
        }

        if (str_starts_with($this->form, 'question-create-') && PelatihanModule::find((int) substr($this->form, 16))) {
            $this->openQuestionCreate((int) substr($this->form, 16));

            return;
        }

        if (str_starts_with($this->form, 'question-') && ($question = PelatihanQuestion::find((int) substr($this->form, 9)))) {
            $this->editQuestion($question->id);

            return;
        }

        $this->form = '';
    }

    #[Computed]
    public function categories()
    {
        return PelatihanCategory::orderBy('name')->get();
    }

    #[Computed]
    public function modules()
    {
        if (! $this->pelatihan) {
            return collect();
        }

        return $this->pelatihan->modules()->with(['videos', 'questions.options'])->get();
    }

    public function updatedTitle(): void
    {
        if (! $this->pelatihan) {
            $this->slug = Str::slug($this->title);
        }
    }

    public function save(CreatePelatihan $createPelatihan, UpdatePelatihan $updatePelatihan): void
    {
        $this->validate([
            'pelatihanCategoryId' => ['nullable', 'exists:pelatihan_categories,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('pelatihans', 'slug')->ignore($this->pelatihan?->id)],
            'description' => ['nullable', 'string'],
            'status' => ['required', Rule::enum(PelatihanStatus::class)],
            'isActive' => ['boolean'],
            'startDate' => ['nullable', 'date'],
            'endDate' => ['nullable', 'date', 'after_or_equal:startDate'],
            'location' => ['nullable', 'string', 'max:255'],
            'mode' => ['required', 'in:online,offline,hybrid'],
            'instructor' => ['nullable', 'string', 'max:255'],
            'quota' => ['nullable', 'integer', 'min:0'],
            'price' => ['nullable', 'integer', 'min:0'],
            'uploadedThumbnail' => ['nullable', 'image', 'max:5120'],
        ]);

        $input = [
            'pelatihan_category_id' => $this->pelatihanCategoryId,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'thumbnail' => $this->uploadedThumbnail,
            'existing_thumbnail' => $this->existingThumbnail,
            'status' => $this->status,
            'is_active' => $this->isActive,
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'location' => $this->location,
            'mode' => $this->mode,
            'instructor' => $this->instructor,
            'quota' => $this->quota,
            'price' => $this->price,
        ];

        if ($this->pelatihan) {
            $updatePelatihan->handle($this->pelatihan, $input);
            Flux::toast(variant: 'success', text: 'Pelatihan berhasil diperbarui.');
        } else {
            $this->pelatihan = $createPelatihan->handle($input);
            Flux::toast(variant: 'success', text: 'Pelatihan berhasil disimpan.');
        }

        $this->redirectRoute('pelatihan.edit', ['pelatihan' => $this->pelatihan], navigate: true);
    }

    // === Modul ===

    public function openModuleCreate(): void
    {
        $this->resetModuleForm();
        $this->showModuleForm = true;
        $this->form = 'module-create';
    }

    public function editModule(int $id): void
    {
        $module = PelatihanModule::findOrFail($id);
        $this->editingModuleId = $module->id;
        $this->moduleTitle = $module->title;
        $this->moduleDescription = $module->description ?? '';
        $this->showModuleForm = true;
        $this->form = 'module-'.$module->id;
    }

    public function saveModule(): void
    {
        $this->validate([
            'moduleTitle' => ['required', 'string', 'max:255'],
            'moduleDescription' => ['nullable', 'string'],
        ]);

        if ($this->editingModuleId) {
            PelatihanModule::findOrFail($this->editingModuleId)->update([
                'title' => $this->moduleTitle,
                'description' => $this->moduleDescription ?: null,
            ]);
            Flux::toast(variant: 'success', text: 'Modul berhasil diperbarui.');
        } else {
            PelatihanModule::create([
                'pelatihan_id' => $this->pelatihan->id,
                'title' => $this->moduleTitle,
                'description' => $this->moduleDescription ?: null,
                'urutan' => $this->pelatihan->modules()->count(),
            ]);
            Flux::toast(variant: 'success', text: 'Modul berhasil ditambahkan.');
        }

        $this->resetModuleForm();
        $this->showModuleForm = false;
        $this->form = '';
        unset($this->modules);
    }

    public function cancelModuleForm(): void
    {
        $this->resetModuleForm();
        $this->showModuleForm = false;
        $this->form = '';
    }

    private function resetModuleForm(): void
    {
        $this->reset(['editingModuleId', 'moduleTitle', 'moduleDescription']);
        $this->resetErrorBag();
    }

    public function moveModuleUp(int $id): void
    {
        $module = PelatihanModule::findOrFail($id);
        $previous = PelatihanModule::where('pelatihan_id', $module->pelatihan_id)
            ->where('urutan', '<', $module->urutan)
            ->orderByDesc('urutan')
            ->first();

        if ($previous) {
            [$module->urutan, $previous->urutan] = [$previous->urutan, $module->urutan];
            $module->save();
            $previous->save();
            unset($this->modules);
        }
    }

    public function moveModuleDown(int $id): void
    {
        $module = PelatihanModule::findOrFail($id);
        $next = PelatihanModule::where('pelatihan_id', $module->pelatihan_id)
            ->where('urutan', '>', $module->urutan)
            ->orderBy('urutan')
            ->first();

        if ($next) {
            [$module->urutan, $next->urutan] = [$next->urutan, $module->urutan];
            $module->save();
            $next->save();
            unset($this->modules);
        }
    }

    public function copyModule(int $id, CopyPelatihanModule $copyPelatihanModule): void
    {
        $module = PelatihanModule::findOrFail($id);
        $copyPelatihanModule->handle($module);
        unset($this->modules);
        Flux::toast(variant: 'success', text: 'Modul berhasil disalin.');
    }

    public function confirmDeleteModule(int $id): void
    {
        $module = PelatihanModule::findOrFail($id);
        $this->deletingModuleId = $id;
        $this->deletingModuleTitle = $module->title;
        $this->modal('confirm-delete-pelatihan-module')->show();
    }

    public function deleteModule(): void
    {
        if (! $this->deletingModuleId) {
            return;
        }

        $module = PelatihanModule::findOrFail($this->deletingModuleId);

        foreach ($module->videos as $video) {
            if ($video->file_path) {
                Storage::disk('public')->delete($video->file_path);
            }
        }

        $module->delete();
        $this->deletingModuleId = null;
        $this->deletingModuleTitle = '';
        unset($this->modules);
        $this->modal('confirm-delete-pelatihan-module')->close();
        Flux::toast(variant: 'success', text: 'Modul berhasil dihapus.');
    }

    // === Video ===

    public function openVideoCreate(int $moduleId): void
    {
        $this->resetVideoForm();
        $this->currentModuleId = $moduleId;
        $this->showVideoForm = true;
        $this->form = 'video-create-'.$moduleId;
    }

    public function editVideo(int $id): void
    {
        $video = PelatihanModuleVideo::findOrFail($id);
        $this->currentModuleId = $video->pelatihan_module_id;
        $this->editingVideoId = $video->id;
        $this->videoTitle = $video->title;
        $this->videoSourceType = $video->source_type->value;
        $this->videoUrl = $video->url ?? '';
        $this->existingVideoFile = $video->file_path;
        $this->videoDurationSeconds = $video->duration_seconds;
        $this->showVideoForm = true;
        $this->form = 'video-'.$video->id;
    }

    public function saveVideo(SavePelatihanModuleVideo $savePelatihanModuleVideo): void
    {
        $rules = [
            'videoTitle' => ['required', 'string', 'max:255'],
            'videoSourceType' => ['required', Rule::enum(PelatihanVideoSourceType::class)],
            'videoDurationSeconds' => ['nullable', 'integer', 'min:0'],
        ];

        if ($this->videoSourceType === PelatihanVideoSourceType::Embed->value) {
            $rules['videoUrl'] = ['required', 'url', 'max:500'];
        } else {
            $rules['uploadedVideo'] = [
                $this->editingVideoId && $this->existingVideoFile ? 'nullable' : 'required',
                'file', 'mimes:mp4,mov,avi,webm', 'max:102400',
            ];
        }

        $this->validate($rules);

        $module = PelatihanModule::findOrFail($this->currentModuleId);
        $video = $this->editingVideoId ? PelatihanModuleVideo::findOrFail($this->editingVideoId) : null;

        $savePelatihanModuleVideo->handle($video, $module, [
            'title' => $this->videoTitle,
            'source_type' => $this->videoSourceType,
            'url' => $this->videoUrl,
            'uploaded_file' => $this->uploadedVideo,
            'existing_file_path' => $this->existingVideoFile,
            'duration_seconds' => $this->videoDurationSeconds,
        ]);

        Flux::toast(variant: 'success', text: 'Video berhasil disimpan.');
        $this->resetVideoForm();
        $this->showVideoForm = false;
        $this->form = '';
        unset($this->modules);
    }

    public function cancelVideoForm(): void
    {
        $this->resetVideoForm();
        $this->showVideoForm = false;
        $this->form = '';
    }

    private function resetVideoForm(): void
    {
        $this->reset([
            'currentModuleId', 'editingVideoId', 'videoTitle', 'videoUrl',
            'uploadedVideo', 'existingVideoFile', 'videoDurationSeconds',
        ]);
        $this->videoSourceType = 'embed';
        $this->resetErrorBag();
    }

    public function moveVideoUp(int $id): void
    {
        $video = PelatihanModuleVideo::findOrFail($id);
        $previous = PelatihanModuleVideo::where('pelatihan_module_id', $video->pelatihan_module_id)
            ->where('urutan', '<', $video->urutan)
            ->orderByDesc('urutan')
            ->first();

        if ($previous) {
            [$video->urutan, $previous->urutan] = [$previous->urutan, $video->urutan];
            $video->save();
            $previous->save();
            unset($this->modules);
        }
    }

    public function moveVideoDown(int $id): void
    {
        $video = PelatihanModuleVideo::findOrFail($id);
        $next = PelatihanModuleVideo::where('pelatihan_module_id', $video->pelatihan_module_id)
            ->where('urutan', '>', $video->urutan)
            ->orderBy('urutan')
            ->first();

        if ($next) {
            [$video->urutan, $next->urutan] = [$next->urutan, $video->urutan];
            $video->save();
            $next->save();
            unset($this->modules);
        }
    }

    public function copyVideo(int $id, CopyPelatihanModuleVideo $copyPelatihanModuleVideo): void
    {
        $video = PelatihanModuleVideo::findOrFail($id);
        $copyPelatihanModuleVideo->handle($video);
        unset($this->modules);
        Flux::toast(variant: 'success', text: 'Video berhasil disalin.');
    }

    public function confirmDeleteVideo(int $id): void
    {
        $video = PelatihanModuleVideo::findOrFail($id);
        $this->deletingVideoId = $id;
        $this->deletingVideoTitle = $video->title;
        $this->modal('confirm-delete-pelatihan-video')->show();
    }

    public function deleteVideo(): void
    {
        if (! $this->deletingVideoId) {
            return;
        }

        $video = PelatihanModuleVideo::findOrFail($this->deletingVideoId);

        if ($video->file_path) {
            Storage::disk('public')->delete($video->file_path);
        }

        $video->delete();
        $this->deletingVideoId = null;
        $this->deletingVideoTitle = '';
        unset($this->modules);
        $this->modal('confirm-delete-pelatihan-video')->close();
        Flux::toast(variant: 'success', text: 'Video berhasil dihapus.');
    }

    // === Soal ===

    public function openQuestionCreate(int $moduleId): void
    {
        $this->resetQuestionForm();
        $this->currentModuleIdForQuestion = $moduleId;
        $this->showQuestionForm = true;
        $this->form = 'question-create-'.$moduleId;
    }

    public function editQuestion(int $id): void
    {
        $question = PelatihanQuestion::with('options')->findOrFail($id);
        $this->currentModuleIdForQuestion = $question->pelatihan_module_id;
        $this->editingQuestionId = $question->id;
        $this->questionType = $question->tipe->value;
        $this->questionText = $question->pertanyaan;
        $this->bobot = $question->bobot;
        $this->correctAnswer = $question->correct_answer ?? true;
        $this->kunciJawaban = $question->kunci_jawaban ?? '';
        $this->options = $question->options->isNotEmpty()
            ? $question->options->map(fn ($option) => [
                'text' => $option->teks_pilihan,
                'is_correct' => $option->is_correct,
            ])->toArray()
            : $this->defaultOptions();
        $this->showQuestionForm = true;
        $this->form = 'question-'.$question->id;
    }

    public function addOption(): void
    {
        if (count($this->options) < 6) {
            $this->options[] = ['text' => '', 'is_correct' => false];
        }
    }

    public function removeOption(int $index): void
    {
        if (count($this->options) > 2) {
            array_splice($this->options, $index, 1);
        }
    }

    public function markCorrectOption(int $index): void
    {
        foreach ($this->options as $i => $option) {
            $this->options[$i]['is_correct'] = $i === $index;
        }
    }

    public function moveOptionUp(int $index): void
    {
        if ($index > 0) {
            [$this->options[$index], $this->options[$index - 1]] = [$this->options[$index - 1], $this->options[$index]];
        }
    }

    public function moveOptionDown(int $index): void
    {
        if ($index < count($this->options) - 1) {
            [$this->options[$index], $this->options[$index + 1]] = [$this->options[$index + 1], $this->options[$index]];
        }
    }

    public function saveQuestion(SavePelatihanQuestion $savePelatihanQuestion): void
    {
        $this->validate([
            'questionType' => ['required', Rule::enum(PelatihanQuestionType::class)],
            'questionText' => ['required', 'string'],
            'bobot' => ['required', 'integer', 'min:1'],
            'kunciJawaban' => ['nullable', 'string'],
        ]);

        if ($this->questionType === PelatihanQuestionType::PilihanGanda->value) {
            $this->validate([
                'options' => ['required', 'array', 'min:2'],
                'options.*.text' => ['required', 'string'],
            ]);

            if (collect($this->options)->where('is_correct', true)->count() !== 1) {
                $this->addError('options', 'Pilih tepat satu jawaban yang benar.');

                return;
            }
        }

        $module = PelatihanModule::findOrFail($this->currentModuleIdForQuestion);
        $question = $this->editingQuestionId ? PelatihanQuestion::findOrFail($this->editingQuestionId) : null;

        $savePelatihanQuestion->handle($question, $module, [
            'tipe' => $this->questionType,
            'pertanyaan' => $this->questionText,
            'bobot' => $this->bobot,
            'correct_answer' => $this->correctAnswer,
            'kunci_jawaban' => $this->kunciJawaban,
            'options' => $this->options,
        ]);

        Flux::toast(variant: 'success', text: 'Soal berhasil disimpan.');
        $this->resetQuestionForm();
        $this->showQuestionForm = false;
        $this->form = '';
        unset($this->modules);
    }

    public function cancelQuestionForm(): void
    {
        $this->resetQuestionForm();
        $this->showQuestionForm = false;
        $this->form = '';
    }

    private function resetQuestionForm(): void
    {
        $this->reset(['currentModuleIdForQuestion', 'editingQuestionId', 'questionText', 'kunciJawaban']);
        $this->questionType = 'pilihan_ganda';
        $this->bobot = 1;
        $this->correctAnswer = true;
        $this->options = $this->defaultOptions();
        $this->resetErrorBag();
    }

    private function defaultOptions(): array
    {
        return [
            ['text' => '', 'is_correct' => true],
            ['text' => '', 'is_correct' => false],
        ];
    }

    public function copyQuestion(int $id, CopyPelatihanQuestion $copyPelatihanQuestion): void
    {
        $question = PelatihanQuestion::findOrFail($id);
        $copyPelatihanQuestion->handle($question);
        unset($this->modules);
        Flux::toast(variant: 'success', text: 'Soal berhasil disalin.');
    }

    public function confirmDeleteQuestion(int $id): void
    {
        $question = PelatihanQuestion::findOrFail($id);
        $this->deletingQuestionId = $id;
        $this->deletingQuestionText = Str::limit($question->pertanyaan, 60);
        $this->modal('confirm-delete-pelatihan-question')->show();
    }

    public function deleteQuestion(): void
    {
        if (! $this->deletingQuestionId) {
            return;
        }

        PelatihanQuestion::findOrFail($this->deletingQuestionId)->delete();
        $this->deletingQuestionId = null;
        $this->deletingQuestionText = '';
        unset($this->modules);
        $this->modal('confirm-delete-pelatihan-question')->close();
        Flux::toast(variant: 'success', text: 'Soal berhasil dihapus.');
    }

    public function moveQuestionUp(int $id): void
    {
        $question = PelatihanQuestion::findOrFail($id);
        $previous = PelatihanQuestion::where('pelatihan_module_id', $question->pelatihan_module_id)
            ->where('urutan', '<', $question->urutan)
            ->orderByDesc('urutan')
            ->first();

        if ($previous) {
            [$question->urutan, $previous->urutan] = [$previous->urutan, $question->urutan];
            $question->save();
            $previous->save();
            unset($this->modules);
        }
    }

    public function moveQuestionDown(int $id): void
    {
        $question = PelatihanQuestion::findOrFail($id);
        $next = PelatihanQuestion::where('pelatihan_module_id', $question->pelatihan_module_id)
            ->where('urutan', '>', $question->urutan)
            ->orderBy('urutan')
            ->first();

        if ($next) {
            [$question->urutan, $next->urutan] = [$next->urutan, $question->urutan];
            $question->save();
            $next->save();
            unset($this->modules);
        }
    }

    public function render(): View
    {
        return view('livewire.pelatihan-form')->title(
            $this->pelatihan
                ? 'Edit Pelatihan - '.$this->pelatihan->title
                : 'Tambah Pelatihan'
        );
    }
}
