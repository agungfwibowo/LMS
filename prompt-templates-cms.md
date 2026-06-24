# Prompt Templates — General CMS (Laravel 13 + Filament + Livewire)

> Strategi hemat token. Copy-paste tiap blok sesuai fase. Ganti bagian `[...]` sesuai kebutuhan.

**Keputusan dasar:**
- Models: Post, Category, Tag, User, Comment, Setting
- Pages: Home, PostList, SinglePost, Contact
- Components reusable: PostCard, Pagination, SearchFilter, CommentForm
- Design: pakai Flowbite/Preline (jangan generate dari nol)
- Frontend: statis dulu, realtime ditunda

---

## FASE 0 — Yang DIKERJAKAN SENDIRI (0 token)

Jalankan langsung di terminal, jangan tanya AI:

```bash
php artisan make:model Post -m
php artisan make:model Category -m
php artisan make:model Tag -m
php artisan make:model Comment -m
php artisan make:model Setting -m
# User sudah ada bawaan Laravel — extend saja
```

Isi field migration sendiri (Anda lebih cepat dari AI untuk ini).

---

## FASE 1 — Relationships & Model Logic (1 batch prompt)

Pakai prompt ini SEKALI untuk semua model:

```
Saya punya 6 model Laravel 13 untuk general CMS: Post, Category, Tag, User, Comment, Setting.

Relasi:
- Post belongsTo User (author), belongsTo Category, belongsToMany Tag, hasMany Comment
- Category hasMany Post
- Tag belongsToMany Post
- Comment belongsTo Post, belongsTo User
- Setting standalone (key-value)

Tugas: tulis isi LENGKAP ke-6 file model dalam SATU response.
Untuk tiap model sertakan: $fillable, $casts, relationship methods, dan scope yang relevan (mis. scopePublished di Post).
Jangan tulis penjelasan panjang — langsung kode per file, dipisah heading nama file.
```

---

## FASE 2 — Filament Admin (mayoritas tanpa AI)

Generate dulu via artisan (0 token):

```bash
php artisan make:filament-resource Post --generate
php artisan make:filament-resource Category --generate
php artisan make:filament-resource Tag --generate
php artisan make:filament-resource Comment --generate
php artisan make:filament-resource Setting --generate
```

AI HANYA untuk custom logic yang tidak di-handle scaffolding. Contoh prompt (pakai seperlunya):

```
Di Filament PostResource, tambahkan:
1. Field 'status' sebagai Select (draft/published/scheduled)
2. Field 'published_at' DateTimePicker, hanya muncul kalau status = scheduled
3. Bulk action "Publish sekarang" untuk set status=published

Tulis hanya bagian form() dan bulkActions() yang berubah. Jangan tulis ulang seluruh resource.
```

---

## FASE 3 — Livewire Components Reusable (1 batch prompt)

> Tips hemat token: ambil markup dari Flowbite/Preline dulu, tempel di bawah, minta AI WIRING saja — bukan mendesain.

```
Generate 4 reusable Livewire components untuk frontend CMS (Laravel 13 + Livewire 3, Tailwind, Alpine bila perlu).

1. PostCard — props: Post $post. Tampilkan thumbnail, title, excerpt, category, tanggal. Link ke single post.
2. Pagination — wrapper paginasi Livewire standar, gaya Tailwind.
3. SearchFilter — input search (debounce 300ms) + dropdown filter kategori. Emit event ke parent, jangan query sendiri.
4. CommentForm — props: Post $post. Submit komentar biasa (non-realtime), validasi body required min:3, reset setelah submit.

Markup styling pakai struktur ini (jangan rancang ulang dari nol):
[TEMPEL MARKUP FLOWBITE/PRELINE DI SINI, atau tulis "pakai Tailwind sederhana" kalau belum ada]

Output: tiap component pisahkan jadi class PHP + blade view, diberi heading nama file. Tanpa penjelasan panjang.
```

---

## FASE 4 — Livewire Pages (1 batch prompt)

```
Generate 4 Livewire page components, MEMANFAATKAN component dari fase sebelumnya (PostCard, Pagination, SearchFilter, CommentForm). Jangan duplikasi logic mereka.

1. HomePage — hero + daftar latest/featured post (pakai PostCard).
2. PostListPage — terima query param ?category= dan ?search=. Pakai SearchFilter + PostCard + Pagination. Satu page ini menangani archive, kategori, dan pencarian sekaligus.
3. SinglePostPage — detail post + CommentForm + related posts (pakai PostCard).
4. ContactPage — form kontak sederhana, validasi, kirim ke email/log.

Output: class PHP + blade view per page, heading nama file. Tanpa penjelasan panjang.
```

---

## TEMPLATE REUSABLE — Tambah Model Baru Nanti

Simpan ini. Saat butuh model baru tinggal ganti variabel:

```
Generate untuk model [NAMA_MODEL] di CMS Laravel 13 saya:
- Migration fields: [LIST FIELD + TIPE]
- Relationships: [LIST RELASI]
- Filament resource: generate standar, lalu tambahkan [CUSTOM KALAU ADA]

Ikuti pattern model Post saya yang sudah ada:
[TEMPEL ISI Post.php SEBAGAI CONTOH]

Output langsung kode, tanpa penjelasan.
```

---

## CHEAT SHEET — Aturan Hemat Token

| Aturan | Kenapa |
|--------|--------|
| Batch > single request | Kurangi overhead konteks berulang |
| Artisan dulu, AI belakangan | Scaffolding gratis, AI cuma untuk custom |
| Tempel markup jadi, minta wiring | AI tak perlu "mendesain" = jauh lebih hemat |
| "Tanpa penjelasan panjang" di tiap prompt | Output to-the-point, hemat token keluaran |
| "Tulis hanya bagian yang berubah" | Hindari AI menulis ulang file penuh |
| Satu page multi-fungsi (param) | Kurangi jumlah komponen yang digenerate |
| Statis dulu, realtime nanti | Hindari debugging broadcasting yang mahal |
