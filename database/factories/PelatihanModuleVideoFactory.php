<?php

namespace Database\Factories;

use App\Enums\PelatihanVideoSourceType;
use App\Models\PelatihanModule;
use App\Models\PelatihanModuleVideo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PelatihanModuleVideo>
 */
class PelatihanModuleVideoFactory extends Factory
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
            'title' => fake()->sentence(3),
            'source_type' => PelatihanVideoSourceType::Embed,
            'url' => 'https://www.youtube.com/watch?v='.fake()->regexify('[A-Za-z0-9_-]{11}'),
            'file_path' => null,
            'duration_seconds' => fake()->numberBetween(120, 1800),
            'urutan' => 0,
        ];
    }

    public function uploaded(): static
    {
        return $this->state([
            'source_type' => PelatihanVideoSourceType::Upload,
            'url' => null,
            'file_path' => 'uploads/pelatihan-videos/'.fake()->uuid().'.mp4',
        ]);
    }
}
