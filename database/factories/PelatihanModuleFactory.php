<?php

namespace Database\Factories;

use App\Models\Pelatihan;
use App\Models\PelatihanModule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PelatihanModule>
 */
class PelatihanModuleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'pelatihan_id' => Pelatihan::factory(),
            'title' => 'Modul: '.fake()->words(3, true),
            'description' => fake()->sentence(),
            'urutan' => 0,
        ];
    }
}
