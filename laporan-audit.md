# Laporan Audit — Keamanan & Kualitas Kode

**Proyek:** CMS (Laravel 13, Livewire 4, Fortify, Flux UI)
**Tanggal audit:** 8 Juli 2026
**Cakupan:** Security review dan audit tech debt terhadap `app/`, `routes/`, `resources/views/`, `database/`, dan konfigurasi. Audit statis (pembacaan kode), tanpa penetration testing.

---

## Ringkasan Eksekutif

Fondasi keamanan aplikasi cukup baik: rate limiting login/2FA, password hashing, 2FA dengan konfirmasi, CSRF standar Laravel, dan validasi input yang konsisten. Namun ada **satu rantai kerentanan kritis**: registrasi terbuka + middleware `verified` yang tidak berfungsi + tidak adanya otorisasi/role, sehingga **siapa pun dapat mendaftar dan langsung mendapat akses penuh ke panel admin**. Dikombinasikan dengan konten berita yang dirender tanpa sanitasi, ini membuka jalur stored XSS di halaman publik.

Dari sisi kualitas kode, struktur proyek rapi dan cakupan test bagus (23 file feature test), tetapi ada duplikasi logika di banyak tempat, satu komponen berukuran 751 baris, dan 90 error PHPStan yang disembunyikan lewat baseline.

---

## Temuan Keamanan

### KRITIS-1: Registrasi terbuka → akses admin penuh tanpa hambatan

Tiga masalah yang masing-masing tampak kecil, tetapi bersama-sama membentuk celah kritis:

1. **Registrasi publik aktif.** `config/fortify.php` mengaktifkan `Features::registration()` dan `FortifyServiceProvider` menyediakan `registerView`. Siapa pun bisa membuat akun.
2. **Middleware `verified` tidak berfungsi.** Semua route admin memakai `middleware(['auth', 'verified'])`, tetapi `App\Models\User` **tidak** meng-implement `MustVerifyEmail` (import-nya masih dikomentari di `app/Models/User.php` baris 5). Middleware `verified` bawaan Laravel hanya memeriksa user yang meng-implement kontrak tersebut — tanpa itu, middleware lolos begitu saja. Verifikasi email efektif **tidak pernah dicek**.
3. **Tidak ada otorisasi.** Tidak ada Policy, Gate, atau kolom role. Setiap user terautentikasi bisa CRUD semua konten: berita, pelatihan, FAQ, testimoni — termasuk memilih `author_id` user lain di form berita.

**Dampak:** Orang asing dapat mendaftar dan dalam hitungan detik mengelola seluruh konten situs.

**Rekomendasi (pilih sesuai kebutuhan):**
- Jika aplikasi ini internal (admin dibuat manual): matikan `Features::registration()` — perbaikan satu baris yang menutup rantai ini.
- Jika registrasi memang dibutuhkan: implement `MustVerifyEmail` di model `User`, tambahkan kolom role + Policy/Gate, dan batasi route admin ke role tertentu.

### TINGGI-1: Stored XSS pada konten berita

`resources/views/pages/public/⚡berita-show.blade.php` baris 174 merender `{!! $post->content !!}` tanpa escaping. Konten berasal dari rich-text editor dan divalidasi hanya sebagai `['nullable', 'string']` — tanpa sanitasi HTML — lalu disimpan apa adanya (`CreatePost`/`UpdatePost`).

**Dampak:** Siapa pun yang bisa membuat/mengedit berita dapat menyisipkan `<script>` yang dieksekusi di browser semua pengunjung publik. Selama KRITIS-1 belum ditutup, ini praktis dapat dieksploitasi oleh siapa saja.

**Rekomendasi:** Sanitasi HTML saat menyimpan (mis. `mews/purifier` atau HTMLPurifier) dengan whitelist tag yang dihasilkan editor. Escaping saat render bukan opsi karena konten memang HTML.

Catatan serupa (risiko rendah): preview di `⚡form.blade.php` baris 472 juga memakai `{!! !!}` — self-XSS di area admin, prioritas rendah setelah sanitasi diterapkan.

### SEDANG-1: File upload yatim (orphaned)

Endpoint `POST admin/berita/upload-gambar` (`routes/berita.php`) menyimpan gambar ke disk publik dan mengembalikan URL, tetapi tidak ada mekanisme yang menautkan file dengan post. File tetap tersimpan selamanya jika: draft dibatalkan, gambar dihapus dari editor, atau post dihapus. Validasinya sendiri sudah benar (`required|image|max:5120` + auth).

**Rekomendasi:** Jadwalkan pembersihan berkala (scheduled command yang membandingkan file `uploads/` dengan referensi di `posts.content`), atau catat upload di tabel dan hapus saat tidak direferensikan.

### RENDAH-1: Pengecekan prefix `http` yang tidak konsisten

`Pelatihan::delete()` di `app/Livewire/Actions/Pelatihan.php` memakai `str_starts_with($thumbnail, 'http')`, sedangkan accessor model memakai `'http://'` / `'https://'`. Path lokal yang kebetulan diawali "http" (mis. `http-assets/x.jpg`) lolos dari penghapusan, dan sebaliknya. Konsisten­kan lewat satu helper/accessor.

### RENDAH-2: `APP_DEBUG=true`

Wajar untuk `.env` lokal (`APP_ENV=local`), tetapi pastikan pipeline deploy menjamin `APP_DEBUG=false` di produksi — debug page Laravel membocorkan env, path, dan query.

### Hal yang Sudah Baik

Rate limiting login (5/menit per email+IP) dan 2FA (5/menit per sesi); password `hashed` cast; atribut sensitif disembunyikan via `#[Hidden]`; 2FA dengan konfirmasi + confirmPassword; sesi database dengan invalidasi saat logout; validasi input konsisten di hampir semua komponen (unique slug, exists, enum `Rule::in`, batas ukuran file); mass assignment terkendali via `$fillable`/`#[Fillable]`; serta migrasi dengan FK constraint dan cascade yang tepat.

---

## Temuan Tech Debt

### TD-1: Duplikasi logika (prioritas tertinggi)

- **`uniqueSlug()` disalin di 5 file:** `Livewire/Actions/Category.php`, `PelatihanCategory.php`, `Tag.php`, `Actions/Posts/CopyPost.php`, `Actions/Pelatihan/CopyPelatihan.php`. Ekstrak ke satu trait (mis. `App\Concerns\GeneratesUniqueSlug`).
- **`resolveTagIds()` disalin di 2 file:** `CreatePost.php` dan `UpdatePost.php`.
- **Logika inisial nama ada 3 versi:** `User::initials()`, `User::getInitialsAttribute()` (dua-duanya di model yang sama, dengan hasil berbeda!), dan `Testimonial::getInitialsAttribute()`. Pilih satu implementasi, jadikan trait.

### TD-2: `PelatihanForm.php` — komponen 751 baris

Satu komponen Livewire menangani form pelatihan, CRUD modul, CRUD video, dan CRUD soal sekaligus. Sulit diuji dan di-maintain. Pertimbangkan memecah menjadi child component per concern (modul/video/soal), mengikuti pola yang sudah ada di `app/Actions/Pelatihan/`.

### TD-3: 90 error PHPStan disembunyikan di baseline

`phpstan-baseline.neon` berisi 90 error yang di-suppress pada level 7. Baseline wajar sebagai titik awal, tetapi perlu dicicil — targetkan mengurangi baseline setiap sprint agar tidak jadi utang permanen.

### TD-4: Skema `pelatihan_questions` menyimpan dua mekanisme jawaban

Kolom `correct_answer` (boolean, untuk benar/salah) dan `kunci_jawaban` (text, untuk isian) hidup berdampingan dan saling eksklusif tergantung `tipe`. Berfungsi, tetapi rawan data tidak konsisten (mis. soal pilihan ganda dengan `kunci_jawaban` terisi). Pertimbangkan validasi di level aksi/model yang menolkan kolom yang tidak relevan.

### TD-5: Penamaan campur bahasa

Kolom database campur Indonesia–Inggris: `urutan`, `tipe`, `pertanyaan`, `bobot`, `kunci_jawaban`, `teks_pilihan` berdampingan dengan `title`, `status`, `description`, `order`. Konsisten salah satu; kalau migrasi kolom terlalu mahal, minimal bakukan untuk tabel baru ke depannya.

### TD-6: Kebersihan riwayat git

9 commit terakhir semuanya berpesan "update". Menyulitkan pelacakan perubahan dan revert. Biasakan pesan deskriptif (`feat: ...`, `fix: ...`).

### TD-7: Lain-lain (kecil)

- Namespace `App\Livewire\Actions` berisi full-page component, bukan action — hanya `Logout` yang benar-benar action. Membingungkan; pertimbangkan `App\Livewire\Pages` atau sejenis.
- `Faq::moveUp()/moveDown()` menukar `order` dengan dua `save()` terpisah tanpa transaction — race condition kecil bisa menghasilkan order duplikat.
- Daftar FAQ memakai `get()` tanpa pagination — aman selama datanya sedikit.
- `AGENTS.md` dan `CLAUDE.md` identik (9.003 byte) — duplikasi yang harus dirawat ganda; pertimbangkan symlink atau satu sumber.

---

## Prioritas Tindakan

| # | Tindakan | Temuan | Perkiraan Usaha |
|---|----------|--------|-----------------|
| 1 | Matikan registrasi publik ATAU implement `MustVerifyEmail` + role/Policy | KRITIS-1 | 1 baris – 1 hari |
| 2 | Sanitasi HTML konten berita saat simpan | TINGGI-1 | ± setengah hari |
| 3 | Ekstrak `uniqueSlug`, `resolveTagIds`, `initials` ke trait | TD-1 | ± setengah hari |
| 4 | Pembersihan file upload yatim | SEDANG-1 | ± setengah hari |
| 5 | Pecah `PelatihanForm` + cicil baseline PHPStan | TD-2, TD-3 | bertahap |

---

*Laporan ini dibuat dari analisis statis kode. Skema database lengkap tersedia di `database-schema.xlsx` (sheet: Ringkasan, Kolom, Relasi).*
