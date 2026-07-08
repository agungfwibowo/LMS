# ADR-001: Panel Peserta (LMS)

**Status:** Proposed
**Tanggal:** 8 Juli 2026
**Pengambil keputusan:** Pemilik proyek (simrs3)

## Konteks

CMS sudah memiliki panel admin (berita, pelatihan, konten) dengan sistem role `peserta`/`admin` dan alur persetujuan akun. Skema pelatihan lengkap (kategori → pelatihan → modul → video & soal kuis → opsi jawaban) sudah ada, tetapi belum ada satu pun fitur yang menghadap peserta. Role `peserta` saat ini hanya bisa mengakses dashboard kosong dan settings.

Keputusan produk yang sudah ditetapkan pemilik proyek:

1. Pendaftaran pelatihan **langsung terdaftar selama kuota tersedia** (tanpa persetujuan admin per pendaftaran), dengan **verifikasi email diaktifkan** sebagai gerbang akun.
2. Syarat selesai pelatihan: **menonton semua video + mengerjakan semua kuis + mencapai skor minimal**.
3. Percobaan kuis **diatur admin per pelatihan, default bebas mengulang** (skor terbaik yang dihitung).
4. Sertifikat dan laporan untuk admin dibutuhkan (fase lanjut).

## Keputusan

Membangun panel peserta sebagai **grup route `/peserta` di aplikasi monolit yang sama**, dengan layout Blade/Livewire sendiri, dipisahkan dari panel admin oleh middleware role. Data keikutsertaan disimpan di tabel baru (`pelatihan_user`, progres video, attempt kuis) tanpa mengubah skema pelatihan yang ada.

## Opsi yang Dipertimbangkan

### Opsi A: Grup route `/peserta` di monolit yang sama (dipilih)

| Dimensi | Penilaian |
|---------|-----------|
| Kompleksitas | Rendah |
| Biaya | Rendah — pakai stack & pola yang sudah ada (Livewire, Flux) |
| Skalabilitas | Cukup untuk skala internal RS |
| Familiaritas tim | Tinggi — pola sama dengan panel admin |

**Pro:** satu codebase, satu auth, satu deploy; test dan konvensi yang sudah ada langsung terpakai; tercepat mencapai nilai.
**Kontra:** UI peserta dan admin berbagi aset & sesi; jika kelak peserta ribuan dan butuh mobile app, perlu ekspos API terpisah.

### Opsi B: Aplikasi/SPA terpisah (frontend sendiri + API)

**Pro:** pemisahan total, siap mobile, bisa diskalakan terpisah.
**Kontra:** duplikasi auth, biaya bangun & rawat jauh lebih besar, melenceng dari keahlian stack saat ini. Prematur untuk kebutuhan sekarang.

## Rancangan Data

Tabel baru (semua FK `cascadeOnDelete` kecuali disebut lain):

- **pelatihan_user** — pendaftaran. Kolom: `pelatihan_id`, `user_id`, `status` (enum: `terdaftar`, `selesai`, `lulus`), `registered_at`, `completed_at` nullable. Unique `(pelatihan_id, user_id)`. Kuota dicek saat mendaftar di dalam transaction + `lockForUpdate()` untuk mencegah race melebihi kuota.
- **pelatihan_video_progress** — `user_id`, `pelatihan_module_video_id`, `watched_at`. Unique pasangan.
- **pelatihan_quiz_attempts** — `user_id`, `pelatihan_module_id`, `score`, `max_score`, `started_at`, `finished_at` nullable.
- **pelatihan_quiz_answers** — `attempt_id`, `pelatihan_question_id`, `answer` (text/json sesuai tipe soal), `is_correct`, `points`.

Kolom baru di **pelatihans**: `max_quiz_attempts` (unsigned int, `NULL` = bebas — default), `passing_score` (persen, nullable; `NULL` = tanpa skor minimal).

Kelulusan dihitung dari: semua video pelatihan punya baris progress + tiap modul punya attempt selesai + skor terbaik agregat ≥ `passing_score`.

## Trade-off Kunci

Pendaftaran otomatis + kuota memakai lock DB, bukan antrian — sederhana dan cukup untuk trafik internal; jika kelak pendaftaran serentak ribuan orang, pindah ke reservasi berbasis queue. Percobaan kuis "bebas dengan skor terbaik" memaksimalkan pembelajaran tapi membuat skor kurang diskriminatif — dimitigasi dengan `max_quiz_attempts` per pelatihan yang bisa diketatkan admin.

## Konsekuensi

- Lebih mudah: fitur peserta berikutnya (sertifikat, laporan) tinggal membaca tabel attempt/enrollment; panel admin mendapat data kepesertaan gratis untuk laporan.
- Lebih sulit: `MustVerifyEmail` wajib diaktifkan → **butuh konfigurasi mail produksi yang berfungsi** (saat ini belum diverifikasi); user lama harus di-backfill `email_verified_at`.
- Perlu revisit: mode pendaftaran per pelatihan (opsi "perlu persetujuan") sengaja tidak dibangun sekarang — tambahkan kolom `enrollment_mode` nanti jika kebutuhan muncul.

## Rencana Implementasi (per fase, satu sesi per fase)

1. [ ] **Fase 0 — Prasyarat:** aktifkan `MustVerifyEmail` + backfill user lama; siapkan konfigurasi mail; redirect per role di `LoginResponse` (admin → `/admin/dashboard`, peserta → `/peserta`).
2. [ ] **Fase 1 — Katalog & pendaftaran:** layout `/peserta`, katalog pelatihan published, daftar/batal dengan lock kuota, tabel `pelatihan_user`.
3. [ ] **Fase 2 — Belajar:** halaman modul, pemutar video + progres tontonan.
4. [ ] **Fase 3 — Kuis:** attempt, penilaian otomatis per tipe soal, batas percobaan per pelatihan, skor terbaik, status lulus.
5. [ ] **Fase 4 — Sertifikat & laporan admin:** sertifikat PDF untuk yang lulus; halaman laporan kepesertaan per pelatihan di panel admin.
