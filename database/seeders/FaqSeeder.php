<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $faqs = [
            ['question' => 'Siapa saja yang bisa mendaftar?',                   'answer' => 'Seluruh pegawai RSUP H. Adam Malik (medis, non-medis, manajemen) serta peserta eksternal yang memenuhi syarat dapat mendaftar melalui SIPAHAM.'],
            ['question' => 'Bagaimana cara masuk ke sistem?',                   'answer' => 'Pegawai dapat masuk menggunakan NIP dan kata sandi terdaftar. Peserta eksternal menggunakan email yang didaftarkan saat registrasi.'],
            ['question' => 'Apakah pelatihan dipungut biaya?',                  'answer' => 'Sebagian besar pelatihan internal gratis bagi pegawai. Pelatihan tertentu untuk peserta eksternal dapat dikenakan biaya yang tertera pada detail modul.'],
            ['question' => 'Apakah sertifikat diakui resmi?',                   'answer' => 'Ya. Sertifikat diterbitkan secara digital oleh Bagian Diklat RS Adam Malik dan dapat terintegrasi dengan SISDMK.'],
            ['question' => 'Bagaimana jika berhalangan hadir pada sesi luring?', 'answer' => 'Anda dapat mengajukan penjadwalan ulang melalui akun selama kuota batch berikutnya masih tersedia.'],
        ];

        foreach ($faqs as $i => $faq) {
            Faq::create([...$faq, 'order' => $i + 1]);
        }
    }
}
