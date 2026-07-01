<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $testimonials = [
            ['name' => 'Siti Rahmawati', 'role' => 'Perawat Pelaksana', 'avatar_color' => 'brand', 'rating' => 5.0, 'quote' => 'Materi pelatihannya relevan dengan tugas harian saya di bangsal. Sertifikatnya juga langsung terbit di akun.'],
            ['name' => 'Budi Hartono',   'role' => 'Kepala Instalasi',   'avatar_color' => 'lime',  'rating' => 4.5, 'quote' => 'Sebagai kepala unit, SIPAHAM memudahkan saya memantau kompetensi seluruh anggota tim secara terpusat.'],
            ['name' => 'Dewi Anggraini', 'role' => 'Staf Administrasi',  'avatar_color' => 'brand', 'rating' => 4.5, 'quote' => 'Proses pendaftaran cepat dan jadwalnya jelas. Pelatihan daringnya fleksibel di sela jam kerja.'],
        ];

        foreach ($testimonials as $data) {
            Testimonial::create($data);
        }
    }
}
