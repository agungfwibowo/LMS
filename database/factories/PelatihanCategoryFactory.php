<?php

namespace Database\Factories;

use App\Enums\PelatihanCategoryIcon;
use App\Models\PelatihanCategory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<PelatihanCategory>
 */
class PelatihanCategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name),
            'description' => fake()->sentence(),
            'icon' => fake()->randomElement(PelatihanCategoryIcon::cases()),
        ];
    }
}
