<?php

namespace App\Actions\Pelatihan;

use App\Actions\Concerns\CopiesStorageFile;
use App\Enums\PelatihanVideoSourceType;
use App\Models\PelatihanModule;
use App\Models\PelatihanModuleVideo;
use Illuminate\Support\Facades\DB;

class CopyPelatihanModule
{
    use CopiesStorageFile;

    public function handle(PelatihanModule $module): PelatihanModule
    {
        return DB::transaction(function () use ($module) {
            $module->loadMissing('videos', 'questions.options');

            $copy = PelatihanModule::create([
                'pelatihan_id' => $module->pelatihan_id,
                'title' => $module->title.' (Salinan)',
                'description' => $module->description,
                'urutan' => PelatihanModule::where('pelatihan_id', $module->pelatihan_id)->count(),
            ]);

            foreach ($module->videos as $video) {
                $copy->videos()->create([
                    'title' => $video->title,
                    'source_type' => $video->source_type,
                    'url' => $video->url,
                    'file_path' => $this->copyVideoFile($video),
                    'duration_seconds' => $video->duration_seconds,
                    'urutan' => $video->urutan,
                ]);
            }

            foreach ($module->questions as $question) {
                $questionCopy = $copy->questions()->create([
                    'tipe' => $question->tipe,
                    'pertanyaan' => $question->pertanyaan,
                    'correct_answer' => $question->correct_answer,
                    'kunci_jawaban' => $question->kunci_jawaban,
                    'bobot' => $question->bobot,
                    'urutan' => $question->urutan,
                ]);

                foreach ($question->options as $option) {
                    $questionCopy->options()->create([
                        'teks_pilihan' => $option->teks_pilihan,
                        'is_correct' => $option->is_correct,
                        'urutan' => $option->urutan,
                    ]);
                }
            }

            return $copy;
        });
    }

    private function copyVideoFile(PelatihanModuleVideo $video): ?string
    {
        if ($video->source_type !== PelatihanVideoSourceType::Upload) {
            return $video->file_path;
        }

        return $this->copyStorageFile($video->file_path);
    }
}
