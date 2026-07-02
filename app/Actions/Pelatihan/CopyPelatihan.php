<?php

namespace App\Actions\Pelatihan;

use App\Actions\Concerns\CopiesStorageFile;
use App\Enums\PelatihanStatus;
use App\Enums\PelatihanVideoSourceType;
use App\Models\Pelatihan;
use App\Models\PelatihanModuleVideo;
use Illuminate\Support\Facades\DB;

class CopyPelatihan
{
    use CopiesStorageFile;

    public function handle(Pelatihan $pelatihan): Pelatihan
    {
        return DB::transaction(function () use ($pelatihan) {
            $copy = Pelatihan::create([
                'pelatihan_category_id' => $pelatihan->pelatihan_category_id,
                'title' => $pelatihan->title.' (Salinan)',
                'slug' => $this->uniqueSlug($pelatihan->slug),
                'description' => $pelatihan->description,
                'thumbnail' => $this->copyStorageFile($pelatihan->thumbnail),
                'status' => PelatihanStatus::Draft,
                'is_active' => false,
                'start_date' => $pelatihan->start_date,
                'end_date' => $pelatihan->end_date,
                'location' => $pelatihan->location,
                'mode' => $pelatihan->mode,
                'instructor' => $pelatihan->instructor,
                'quota' => $pelatihan->quota,
                'price' => $pelatihan->price,
            ]);

            $pelatihan->load('modules.videos', 'modules.questions.options');

            foreach ($pelatihan->modules as $module) {
                $moduleCopy = $copy->modules()->create([
                    'title' => $module->title,
                    'description' => $module->description,
                    'urutan' => $module->urutan,
                ]);

                foreach ($module->videos as $video) {
                    $moduleCopy->videos()->create([
                        'title' => $video->title,
                        'source_type' => $video->source_type,
                        'url' => $video->url,
                        'file_path' => $this->copyVideoFile($video),
                        'duration_seconds' => $video->duration_seconds,
                        'urutan' => $video->urutan,
                    ]);
                }

                foreach ($module->questions as $question) {
                    $questionCopy = $moduleCopy->questions()->create([
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
            }

            return $copy;
        });
    }

    private function uniqueSlug(string $baseSlug): string
    {
        $slug = $baseSlug.'-salinan';
        $counter = 2;

        while (Pelatihan::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-salinan-'.$counter;
            $counter++;
        }

        return $slug;
    }

    private function copyVideoFile(PelatihanModuleVideo $video): ?string
    {
        if ($video->source_type !== PelatihanVideoSourceType::Upload) {
            return $video->file_path;
        }

        return $this->copyStorageFile($video->file_path);
    }
}
