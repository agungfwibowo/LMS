<?php

namespace App\Actions\Pelatihan;

use App\Enums\PelatihanQuestionType;
use App\Models\PelatihanModule;
use App\Models\PelatihanQuestion;
use Illuminate\Support\Facades\DB;

class SavePelatihanQuestion
{
    public function handle(?PelatihanQuestion $question, PelatihanModule $module, array $input): PelatihanQuestion
    {
        return DB::transaction(function () use ($question, $module, $input) {
            $data = [
                'tipe' => $input['tipe'],
                'pertanyaan' => $input['pertanyaan'],
                'bobot' => $input['bobot'],
                'correct_answer' => $input['tipe'] === PelatihanQuestionType::BenarSalah->value
                    ? $input['correct_answer']
                    : null,
                'kunci_jawaban' => $input['tipe'] === PelatihanQuestionType::Esai->value
                    ? ($input['kunci_jawaban'] ?: null)
                    : null,
            ];

            if ($question) {
                $question->update($data);
            } else {
                $data['pelatihan_module_id'] = $module->id;
                $data['urutan'] = $module->questions()->count();
                $question = PelatihanQuestion::create($data);
            }

            $question->options()->delete();

            if ($input['tipe'] === PelatihanQuestionType::PilihanGanda->value) {
                foreach (array_values($input['options']) as $index => $option) {
                    $question->options()->create([
                        'teks_pilihan' => $option['text'],
                        'is_correct' => (bool) $option['is_correct'],
                        'urutan' => $index,
                    ]);
                }
            }

            return $question;
        });
    }
}
