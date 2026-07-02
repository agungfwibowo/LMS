<?php

namespace App\Actions\Pelatihan;

use App\Models\PelatihanQuestion;
use Illuminate\Support\Facades\DB;

class CopyPelatihanQuestion
{
    public function handle(PelatihanQuestion $question): PelatihanQuestion
    {
        return DB::transaction(function () use ($question) {
            $question->loadMissing('options');
            $module = $question->module;

            $copy = $module->questions()->create([
                'tipe' => $question->tipe,
                'pertanyaan' => $question->pertanyaan.' (Salinan)',
                'correct_answer' => $question->correct_answer,
                'kunci_jawaban' => $question->kunci_jawaban,
                'bobot' => $question->bobot,
                'urutan' => $module->questions()->count(),
            ]);

            foreach ($question->options as $option) {
                $copy->options()->create([
                    'teks_pilihan' => $option->teks_pilihan,
                    'is_correct' => $option->is_correct,
                    'urutan' => $option->urutan,
                ]);
            }

            return $copy;
        });
    }
}
