# SIPAHAM — Sistem Pelatihan RS Adam Malik

## Deskripsi Aplikasi
SIPAHAM (Sistem Pelatihan RSUP H. Adam Malik) adalah platform e-learning internal RSUP H. Adam Malik, Medan. Mengelola seluruh siklus pelatihan tenaga kesehatan — dari pendaftaran, pelaksanaan modul, hingga penerbitan sertifikat digital.

## Pengguna
- **Tenaga medis** (dokter, perawat) — ikut pelatihan klinis
- **Tenaga non-medis / administrasi** — ikut pelatihan soft skill & admin
- **Manajemen RS** — pantau rekap kompetensi SDM
- **Peserta eksternal** — daftar mandiri, beberapa pelatihan berbayar

## Fitur Utama
- Katalog pelatihan per kategori
- Jadwal sesi (luring & daring)
- Alur pendaftaran self-service
- Sertifikat digital otomatis (terintegrasi SISDMK)
- Berita & pengumuman dari admin Diklat
- FAQ publik
- CMS admin (tambah pelatihan, berita, kelola peserta) — dibangun nanti

## Stack Teknologi
- **Laravel** (backend)
- **Livewire** (interaktivitas)
- **Tailwind CSS** (styling)
- Admin panel: **Filament** (rencana)

## Warna Brand (dari logo)
- Primary dark  : `#0e4f4d` (teal tua)
- Primary       : `#1f9b9b` (teal)
- Primary light : `#0e6562`
- Accent lime   : `#c4d41a`
- Background    : `#f5f8f8`

## Halaman Publik yang Perlu Didesain
1. **Landing Page** ✅ selesai (`SIPAHAM Landing.dc.html`)
2. **Katalog** — daftar semua pelatihan, filter kategori
3. **Cara Daftar** — step-by-step visual
4. **Jadwal** — kalender / list jadwal lengkap
5. **Berita** — list artikel & detail artikel
6. **Tentang Kami** — profil RS Adam Malik, Bagian Diklat, visi misi

## Catatan
- Desain: bersih, minimalis, profesional (instansi kesehatan pemerintah)
- Font: Plus Jakarta Sans (heading) + Public Sans (body)
- Semua desain pakai inline styles (DC system), siap dikonversi ke Tailwind class
- Standalone HTML tersedia di `SIPAHAM Landing - standalone.html`
