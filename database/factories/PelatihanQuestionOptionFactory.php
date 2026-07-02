<?php

namespace Database\Factories;

use App\Models\PelatihanQuestion;
use App\Models\PelatihanQuestionOption;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PelatihanQuestionOption>
 */
class PelatihanQuestionOptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pelatihan_question_id' => PelatihanQuestion::factory(),
            'teks_pilihan' => fake()->words(3, true),
            'is_correct' => false,
            'urutan' => 0,
        ];
    }
}
