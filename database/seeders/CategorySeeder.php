<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            [
                'name' => 'Pengumuman',
                'description' => 'Informasi resmi dan pengumuman penting dari Diklat RS Adam Malik.',
            ],
            [
                'name' => 'Berita',
                'description' => 'Liputan kegiatan, agenda, dan kabar terkini seputar program pelatihan.',
            ],
            [
                'name' => 'Prestasi',
                'description' => 'Capaian dan penghargaan yang diraih peserta maupun institusi.',
            ],
            [
                'name' => 'Kebijakan',
                'description' => 'Regulasi, kebijakan internal, dan peraturan terkait pelaksanaan diklat.',
            ],
            [
                'name' => 'Panduan',
                'description' => 'Tutorial, petunjuk teknis, dan panduan penggunaan platform SIPAHAM.',
            ],
            [
                'name' => 'Sertifikasi',
                'description' => 'Informasi penerbitan sertifikat, integrasi SISDMK, dan rekognisi kompetensi.',
            ],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
            ]);
        }
    }
}
