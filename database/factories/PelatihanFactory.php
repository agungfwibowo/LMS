<?php

namespace Database\Factories;

use App\Enums\PelatihanStatus;
use App\Models\Pelatihan;
use App\Models\PelatihanCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Pelatihan>
 */
class PelatihanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->sentence(4);
        $start = fake()->dateTimeBetween('now', '+2 months');

        return [
            'pelatihan_category_id' => PelatihanCategory::factory(),
            'title' => rtrim($title, '.'),
            'slug' => Str::slug($title),
            'description' => fake()->paragraphs(3, true),
            'thumbnail' => null,
            'status' => fake()->randomElement(PelatihanStatus::cases()),
            'is_active' => fake()->boolean(80),
            'start_date' => $start,
            'end_date' => (clone $start)->modify('+2 days'),
            'location' => fake()->city(),
            'mode' => fake()->randomElement(['online', 'offline', 'hybrid']),
            'instructor' => fake()->name(),
            'quota' => fake()->numberBetween(10, 100),
            'price' => fake()->randomElement([0, 250000, 500000, 1000000]),
        ];
    }
}
