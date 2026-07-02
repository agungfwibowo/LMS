<?php

namespace Database\Factories;

use App\Enums\PelatihanQuestionType;
use App\Models\PelatihanModule;
use App\Models\PelatihanQuestion;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PelatihanQuestion>
 */
class PelatihanQuestionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pelatihan_module_id' => PelatihanModule::factory(),
            'tipe' => PelatihanQuestionType::PilihanGanda,
            'pertanyaan' => fake()->sentence().'?',
            'correct_answer' => null,
            'kunci_jawaban' => null,
            'bobot' => 1,
            'urutan' => 0,
        ];
    }

    public function benarSalah(): static
    {
        return $this->state([
            'tipe' => PelatihanQuestionType::BenarSalah,
            'correct_answer' => fake()->boolean(),
        ]);
    }

    public function esai(): static
    {
        return $this->state([
            'tipe' => PelatihanQuestionType::Esai,
            'kunci_jawaban' => fake()->sentence(),
        ]);
    }
}
