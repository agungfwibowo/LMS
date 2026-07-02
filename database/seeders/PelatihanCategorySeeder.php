<?php

namespace Database\Seeders;

use App\Models\PelatihanCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PelatihanCategorySeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            ['name' => 'Keperawatan', 'icon' => 'heart', 'description' => 'Pelatihan asuhan keperawatan, kompetensi klinis, dan pengembangan profesi perawat.'],
            ['name' => 'Kegawatdaruratan', 'icon' => 'hand-raised', 'description' => 'Pelatihan penanganan kondisi gawat darurat, resusitasi, dan kode biru.'],
            ['name' => 'Pencegahan & Pengendalian Infeksi', 'icon' => 'shield-check', 'description' => 'Program PPI, kewaspadaan standar, dan pengendalian infeksi rumah sakit.'],
            ['name' => 'Keselamatan Pasien', 'icon' => 'clipboard-document-list', 'description' => 'Sasaran keselamatan pasien, manajemen risiko klinis, dan budaya keselamatan.'],
            ['name' => 'Manajemen Rumah Sakit', 'icon' => 'briefcase', 'description' => 'Manajemen bangsal, mutu pelayanan, dan tata kelola unit rumah sakit.'],
            ['name' => 'Farmasi Klinik', 'icon' => 'beaker', 'description' => 'Pelayanan kefarmasian, medication safety, dan pengelolaan obat.'],
            ['name' => 'Rekam Medis & Informasi Kesehatan', 'icon' => 'document-text', 'description' => 'Rekam medis elektronik, koding INA-CBG, dan manajemen informasi kesehatan.'],
            ['name' => 'Laboratorium & Patologi', 'icon' => 'chart-bar', 'description' => 'Flebotomi, keamanan spesimen, dan mutu pemeriksaan laboratorium.'],
            ['name' => 'Radiologi & Pencitraan', 'icon' => 'computer-desktop', 'description' => 'Proteksi radiasi, keselamatan pasien radiologi, dan pencitraan diagnostik.'],
            ['name' => 'Gizi Klinik', 'icon' => 'book-open', 'description' => 'Asuhan gizi terstandar, terapi diet, dan skrining status gizi pasien.'],
            ['name' => 'Komunikasi & Pelayanan Prima', 'icon' => 'user-group', 'description' => 'Komunikasi efektif SBAR, service excellence, dan penanganan komplain.'],
            ['name' => 'Kesehatan & Keselamatan Kerja (K3RS)', 'icon' => 'shield-check', 'description' => 'Keselamatan kerja, penanganan B3, dan tanggap darurat bencana rumah sakit.'],
        ];

        foreach ($categories as $category) {
            PelatihanCategory::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
                'icon' => $category['icon'],
            ]);
        }
    }
}
