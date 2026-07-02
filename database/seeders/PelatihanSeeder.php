<?php

namespace Database\Seeders;

use App\Enums\PelatihanStatus;
use App\Models\Pelatihan;
use App\Models\PelatihanCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class PelatihanSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = PelatihanCategory::pluck('id', 'slug');

        $pelatihans = [
            [
                'category' => 'kegawatdaruratan',
                'title' => 'Bantuan Hidup Dasar (BHD) untuk Tenaga Kesehatan',
                'description' => 'Pelatihan resusitasi jantung paru (RJP) dan penggunaan AED sesuai pedoman AHA terbaru. Peserta dilatih mengenali henti jantung, melakukan kompresi berkualitas, dan bantuan napas untuk pasien dewasa, anak, dan bayi.',
                'status' => PelatihanStatus::Published,
                'mode' => 'offline',
                'location' => 'Aula Diklat RSUP H. Adam Malik, Medan',
                'instructor' => 'dr. Rizky Pratama, Sp.EM',
                'days_from_now' => 7,
                'duration_days' => 1,
                'quota' => 40,
                'price' => 350000,
            ],
            [
                'category' => 'kegawatdaruratan',
                'title' => 'Basic Trauma & Cardiac Life Support (BTCLS)',
                'description' => 'Pelatihan penanganan pasien trauma dan kegawatan kardiovaskular selama 5 hari. Mencakup triase, airway management, penanganan syok, dan stabilisasi pasien pra-rujukan. Bersertifikat dan terintegrasi SISDMK.',
                'status' => PelatihanStatus::Published,
                'mode' => 'offline',
                'location' => 'Gedung Diklat Lantai 3, RSUP H. Adam Malik',
                'instructor' => 'Ns. Bagus Wicaksono, S.Kep., M.Kep',
                'days_from_now' => 21,
                'duration_days' => 5,
                'quota' => 30,
                'price' => 2500000,
            ],
            [
                'category' => 'kegawatdaruratan',
                'title' => 'Advanced Cardiac Life Support (ACLS)',
                'description' => 'Pelatihan lanjutan tata laksana kegawatan jantung untuk dokter dan perawat mahir. Meliputi interpretasi EKG aritmia, algoritma henti jantung, dan farmakologi resusitasi.',
                'status' => PelatihanStatus::Draft,
                'mode' => 'hybrid',
                'location' => 'Ruang Simulasi Klinik & Zoom Meeting',
                'instructor' => 'dr. Hendra Gunawan, Sp.JP(K)',
                'days_from_now' => 35,
                'duration_days' => 3,
                'quota' => 24,
                'price' => 3000000,
            ],
            [
                'category' => 'pencegahan-pengendalian-infeksi',
                'title' => 'Pelatihan PPI Dasar bagi Petugas Kesehatan',
                'description' => 'Pengenalan program Pencegahan dan Pengendalian Infeksi (PPI) di fasilitas kesehatan: kewaspadaan standar, kewaspadaan berdasarkan transmisi, serta surveilans infeksi terkait pelayanan kesehatan (HAIs).',
                'status' => PelatihanStatus::Published,
                'mode' => 'offline',
                'location' => 'Aula Diklat RSUP H. Adam Malik, Medan',
                'instructor' => 'Ns. Sari Melati, S.Kep., IPCN',
                'days_from_now' => 10,
                'duration_days' => 2,
                'quota' => 50,
                'price' => 500000,
            ],
            [
                'category' => 'pencegahan-pengendalian-infeksi',
                'title' => 'Hand Hygiene & Kewaspadaan Standar',
                'description' => 'Workshop kebersihan tangan 5 momen menurut WHO, penggunaan APD yang benar, dan pengelolaan limbah medis. Praktik langsung dengan glo-germ untuk evaluasi kepatuhan.',
                'status' => PelatihanStatus::Published,
                'mode' => 'online',
                'location' => 'Zoom Meeting (Daring)',
                'instructor' => 'Tim Komite PPI RSUP H. Adam Malik',
                'days_from_now' => 5,
                'duration_days' => 1,
                'quota' => 100,
                'price' => 0,
            ],
            [
                'category' => 'keselamatan-pasien',
                'title' => 'Sasaran Keselamatan Pasien (Patient Safety)',
                'description' => 'Pelatihan enam sasaran keselamatan pasien: identifikasi pasien, komunikasi efektif, keamanan obat high-alert, tepat lokasi-prosedur-pasien operasi, pengurangan risiko infeksi, dan pencegahan pasien jatuh.',
                'status' => PelatihanStatus::Published,
                'mode' => 'offline',
                'location' => 'Ruang Rapat Direksi, RSUP H. Adam Malik',
                'instructor' => 'dr. Anisa Rahmawati, MARS',
                'days_from_now' => 14,
                'duration_days' => 2,
                'quota' => 45,
                'price' => 600000,
            ],
            [
                'category' => 'keperawatan',
                'title' => 'Manajemen Nyeri untuk Perawat Klinis',
                'description' => 'Pelatihan asesmen dan tata laksana nyeri berbasis bukti: skala nyeri, intervensi farmakologis dan non-farmakologis, serta dokumentasi asuhan keperawatan nyeri.',
                'status' => PelatihanStatus::Published,
                'mode' => 'offline',
                'location' => 'Aula Diklat RSUP H. Adam Malik, Medan',
                'instructor' => 'Ns. Dewi Anggraini, S.Kep., M.Kep',
                'days_from_now' => 18,
                'duration_days' => 2,
                'quota' => 40,
                'price' => 450000,
            ],
            [
                'category' => 'keperawatan',
                'title' => 'Perawatan Luka Modern (Modern Wound Care)',
                'description' => 'Pelatihan manajemen luka kronis dan akut menggunakan prinsip TIME dan pemilihan balutan modern. Praktik perawatan luka diabetik, dekubitus, dan luka pasca operasi.',
                'status' => PelatihanStatus::Draft,
                'mode' => 'offline',
                'location' => 'Ruang Skill Lab Keperawatan',
                'instructor' => 'Ns. Fitri Handayani, S.Kep., CWCC',
                'days_from_now' => 40,
                'duration_days' => 3,
                'quota' => 30,
                'price' => 1200000,
            ],
            [
                'category' => 'manajemen-rumah-sakit',
                'title' => 'Manajemen Bangsal bagi Kepala Ruangan',
                'description' => 'Pelatihan kepemimpinan dan manajemen unit rawat inap: penjadwalan tenaga, manajemen logistik, indikator mutu ruangan, dan penyelesaian konflik tim.',
                'status' => PelatihanStatus::Published,
                'mode' => 'hybrid',
                'location' => 'Gedung Diklat Lantai 2 & Zoom Meeting',
                'instructor' => 'Ns. Joko Susilo, S.Kep., MARS',
                'days_from_now' => 28,
                'duration_days' => 3,
                'quota' => 25,
                'price' => 1500000,
            ],
            [
                'category' => 'farmasi-klinik',
                'title' => 'Pelayanan Kefarmasian & Medication Safety',
                'description' => 'Pelatihan pharmaceutical care, rekonsiliasi obat, pengelolaan obat high-alert dan LASA, serta pelaporan kejadian medication error di rumah sakit.',
                'status' => PelatihanStatus::Published,
                'mode' => 'offline',
                'location' => 'Aula Instalasi Farmasi RSUP H. Adam Malik',
                'instructor' => 'apt. Nur Aini, S.Farm., M.Clin.Pharm',
                'days_from_now' => 24,
                'duration_days' => 2,
                'quota' => 35,
                'price' => 700000,
            ],
            [
                'category' => 'rekam-medis-informasi-kesehatan',
                'title' => 'Koding INA-CBG & Rekam Medis Elektronik',
                'description' => 'Pelatihan koding diagnosis ICD-10 dan tindakan ICD-9-CM, penerapan grouper INA-CBG untuk klaim BPJS, serta transisi ke rekam medis elektronik (RME) sesuai regulasi Kemenkes.',
                'status' => PelatihanStatus::Published,
                'mode' => 'online',
                'location' => 'Zoom Meeting (Daring)',
                'instructor' => 'Rina Kartika, A.Md.PK., S.KM',
                'days_from_now' => 12,
                'duration_days' => 2,
                'quota' => 60,
                'price' => 400000,
            ],
            [
                'category' => 'laboratorium-patologi',
                'title' => 'Flebotomi & Keamanan Spesimen Laboratorium',
                'description' => 'Pelatihan teknik pengambilan darah yang aman dan benar, penanganan pra-analitik spesimen, pelabelan, serta pencegahan kesalahan identifikasi sampel.',
                'status' => PelatihanStatus::Published,
                'mode' => 'offline',
                'location' => 'Instalasi Patologi Klinik RSUP H. Adam Malik',
                'instructor' => 'dr. Maya Puspita, Sp.PK',
                'days_from_now' => 16,
                'duration_days' => 2,
                'quota' => 28,
                'price' => 550000,
            ],
            [
                'category' => 'radiologi-pencitraan',
                'title' => 'Proteksi Radiasi bagi Petugas Radiologi',
                'description' => 'Pelatihan keselamatan radiasi sesuai prinsip ALARA, penggunaan alat proteksi diri, pemantauan dosis, serta manajemen keselamatan pasien pada pemeriksaan pencitraan.',
                'status' => PelatihanStatus::Draft,
                'mode' => 'offline',
                'location' => 'Instalasi Radiologi RSUP H. Adam Malik',
                'instructor' => 'Ahmad Fauzi, S.Si., M.Si (Fisikawan Medis)',
                'days_from_now' => 45,
                'duration_days' => 2,
                'quota' => 20,
                'price' => 800000,
            ],
            [
                'category' => 'komunikasi-pelayanan-prima',
                'title' => 'Komunikasi Efektif SBAR & Service Excellence',
                'description' => 'Pelatihan komunikasi antar-profesi dengan metode SBAR, handover pasien yang aman, penanganan komplain, dan pelayanan prima untuk meningkatkan kepuasan pasien.',
                'status' => PelatihanStatus::Archived,
                'mode' => 'offline',
                'location' => 'Aula Diklat RSUP H. Adam Malik, Medan',
                'instructor' => 'Dra. Ratna Wulandari, M.Psi',
                'days_from_now' => -30,
                'duration_days' => 1,
                'quota' => 50,
                'price' => 300000,
            ],
        ];

        foreach ($pelatihans as $data) {
            $start = Carbon::now()->addDays($data['days_from_now'])->setTime(8, 0);
            $end = (clone $start)->addDays($data['duration_days'] - 1)->setTime(16, 0);

            Pelatihan::create([
                'pelatihan_category_id' => $categories[$data['category']] ?? null,
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'description' => $data['description'],
                'thumbnail' => null,
                'status' => $data['status'],
                'is_active' => $data['status'] === PelatihanStatus::Published,
                'start_date' => $start,
                'end_date' => $end,
                'location' => $data['location'],
                'mode' => $data['mode'],
                'instructor' => $data['instructor'],
                'quota' => $data['quota'],
                'price' => $data['price'],
            ]);
        }
    }
}
