<?php

namespace Database\Seeders;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $author = User::first();

        $posts = [
            [
                'title' => 'Pembukaan Pendaftaran Pelatihan Triwulan III 2026',
                'excerpt' => 'Katalog pelatihan periode Juli–September telah resmi dibuka untuk seluruh kategori peserta. Daftar segera sebelum kuota terpenuhi.',
                'content' => '<p>Diklat RS Adam Malik dengan bangga mengumumkan pembukaan pendaftaran program pelatihan Triwulan III Tahun 2026. Seluruh tenaga kesehatan dan non-kesehatan RS Adam Malik, serta peserta eksternal dari institusi mitra, dapat mendaftarkan diri melalui platform SIPAHAM.</p><p>Program pelatihan tersedia untuk semua kategori: Pelatihan Medis, Penunjang & Lab, Manajemen & Mutu, Non-Medis & Umum, K3 & Keselamatan, serta Pelatihan Eksternal. Kuota terbatas, segera login dan pilih program yang sesuai kompetensi Anda.</p>',
                'status' => PostStatus::Published,
                'published_at' => now()->subDays(7),
                'category' => 'Pengumuman',
                'tags' => ['E-Learning'],
            ],
            [
                'title' => 'Integrasi Sertifikat SIPAHAM dengan SISDMK Resmi Aktif',
                'excerpt' => 'Seluruh sertifikat digital yang diterbitkan melalui SIPAHAM kini tersinkron otomatis ke sistem SISDMK Kementerian Kesehatan.',
                'content' => '<p>Mulai 1 Juli 2026, seluruh sertifikat digital yang diterbitkan melalui platform SIPAHAM akan tersinkron secara otomatis ke sistem SISDMK (Sistem Informasi SDM Kesehatan) milik Kementerian Kesehatan RI. Integrasi ini memastikan data kompetensi tenaga kesehatan tercatat secara real-time tanpa perlu pengajuan manual.</p><p>Tenaga kesehatan tidak perlu lagi mengunggah sertifikat secara terpisah ke SISDMK. Cukup selesaikan modul dan evaluasi di SIPAHAM, sertifikat akan terbit dan langsung terdaftar di sistem nasional.</p>',
                'status' => PostStatus::Published,
                'published_at' => now()->subDays(12),
                'category' => 'Sertifikasi',
                'tags' => ['SISDMK', 'E-Learning'],
            ],
            [
                'title' => 'RS Adam Malik Raih Predikat Institusi Diklat Terbaik 2026',
                'excerpt' => 'Capaian kompetensi SDM meningkat 32% sepanjang semester pertama 2026. RS Adam Malik dinobatkan sebagai institusi diklat terbaik Wilayah Sumatera.',
                'content' => '<p>RS Haji Adam Malik Medan kembali menorehkan prestasi gemilang. Pada ajang evaluasi program diklat semester I 2026, RS Adam Malik dinobatkan sebagai Institusi Diklat Terbaik se-Wilayah Sumatera oleh Kementerian Kesehatan RI.</p><p>Penghargaan ini diraih berkat konsistensi dalam meningkatkan kompetensi SDM. Sepanjang Januari hingga Juni 2026, capaian kompetensi tenaga kesehatan meningkat sebesar 32% dibanding periode yang sama tahun sebelumnya, dengan lebih dari 1.200 sertifikat digital yang telah diterbitkan.</p>',
                'status' => PostStatus::Published,
                'published_at' => now()->subDays(17),
                'category' => 'Prestasi',
                'tags' => ['Akreditasi'],
            ],
            [
                'title' => 'Panduan Lengkap Menggunakan Fitur E-Learning SIPAHAM',
                'excerpt' => 'Pelajari cara mengakses modul, mengerjakan evaluasi, dan mengunduh sertifikat melalui panduan resmi platform SIPAHAM.',
                'content' => '<p>Untuk membantu peserta dalam memaksimalkan penggunaan platform SIPAHAM, tim Diklat RS Adam Malik menerbitkan panduan lengkap penggunaan fitur e-learning. Panduan ini mencakup langkah-langkah mulai dari pendaftaran akun, memilih program pelatihan, mengakses modul pembelajaran, hingga mengunduh sertifikat digital.</p><ol><li>Login ke SIPAHAM menggunakan NIP/NIK dan kata sandi.</li><li>Pilih menu "Katalog Pelatihan" dan cari program yang sesuai.</li><li>Klik "Daftar" dan ikuti instruksi pendaftaran.</li><li>Akses modul melalui menu "Pelatihan Saya" dan selesaikan semua materi.</li><li>Kerjakan evaluasi akhir dengan nilai minimal kelulusan 70.</li><li>Sertifikat digital otomatis terbit dan dapat diunduh dari profil Anda.</li></ol>',
                'status' => PostStatus::Published,
                'published_at' => now()->subDays(20),
                'category' => 'Panduan',
                'tags' => ['E-Learning'],
            ],
            [
                'title' => 'Kebijakan Baru Wajib Pelatihan BHD bagi Seluruh Tenaga Medis',
                'excerpt' => 'Manajemen RS Adam Malik menetapkan kewajiban pelatihan Bantuan Hidup Dasar (BHD) bagi seluruh tenaga medis sesuai Permenkes terbaru.',
                'content' => '<p>Mengacu pada Peraturan Menteri Kesehatan Nomor 47 Tahun 2025 tentang Standar Kompetensi Tenaga Kesehatan, Manajemen RS Haji Adam Malik Medan menetapkan kebijakan baru yang mewajibkan seluruh tenaga medis — dokter, perawat, dan tenaga kesehatan lainnya — untuk mengikuti pelatihan Bantuan Hidup Dasar (BHD) minimal satu kali dalam dua tahun.</p><p>Pelatihan BHD mencakup resusitasi jantung paru (RJP), penggunaan AED, serta penanganan sumbatan jalan napas. Jadwal pelatihan dapat dilihat dan diikuti langsung melalui platform SIPAHAM. Peserta yang belum memenuhi kewajiban ini diminta segera mendaftar.</p>',
                'status' => PostStatus::Published,
                'published_at' => now()->subDays(25),
                'category' => 'Kebijakan',
                'tags' => ['BHD'],
            ],
            [
                'title' => 'Pelatihan Pencegahan dan Pengendalian Infeksi Angkatan 8 Dibuka',
                'excerpt' => 'Program PPI Angkatan 8 kembali dibuka dengan format blended learning — gabungan modul e-learning dan sesi praktik luring di RS.',
                'content' => '<p>Komite PPI RS Haji Adam Malik bekerja sama dengan tim Diklat membuka kembali program Pelatihan Pencegahan dan Pengendalian Infeksi (PPI) Angkatan 8. Program ini tersedia dalam format blended learning yang menggabungkan modul e-learning selama 3 hari dengan sesi praktik luring di lingkungan rumah sakit.</p><p>Pelatihan PPI wajib diikuti oleh seluruh tenaga kesehatan yang berinteraksi langsung dengan pasien. Materi mencakup kewaspadaan standar, APD, kebersihan tangan, dan pengelolaan limbah medis. Kuota terbatas 30 peserta per angkatan. Pendaftaran melalui SIPAHAM.</p>',
                'status' => PostStatus::Published,
                'published_at' => now()->subDays(3),
                'category' => 'Berita',
                'tags' => ['PPI', 'K3RS', 'E-Learning'],
            ],
        ];

        foreach ($posts as $data) {
            $post = Post::create([
                'author_id' => $author->id,
                'title' => $data['title'],
                'slug' => Str::slug($data['title']),
                'excerpt' => $data['excerpt'],
                'content' => $data['content'],
                'status' => $data['status'],
                'published_at' => $data['published_at'],
            ]);

            $category = Category::where('name', $data['category'])->first();
            if ($category) {
                $post->categories()->attach($category->id);
            }

            $tagIds = Tag::whereIn('name', $data['tags'])->pluck('id');
            $post->tags()->attach($tagIds);
        }
    }
}
