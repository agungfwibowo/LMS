<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TagSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $tags = [
            'BHD',
            'PPI',
            'Akreditasi',
            'SISDMK',
            'K3RS',
            'E-Learning',
        ];

        foreach ($tags as $name) {
            Tag::create([
                'name' => $name,
                'slug' => Str::slug($name),
            ]);
        }
    }
}
