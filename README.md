# CMS

A Laravel + Livewire content management system for managing training programs (Pelatihan), news/articles (Berita), FAQs, and testimonials.

## Tech Stack

- **PHP** 8.5 / **Laravel** 13
- **Livewire** 4 with **Flux UI** 2
- **Laravel Fortify** for authentication
- **Tailwind CSS** 4 + Vite
- **Pest** 4 for testing, **Larastan** for static analysis, **Pint** for code style

## Features

- **Pelatihan (Training)** — categories, modules, videos, quiz questions/options, and a public calendar view
- **Berita (News/Posts)** — categories, tags, rich-text content with image uploads
- **Konten (Content)** — FAQs and testimonials
- **Admin dashboard** protected by auth + email verification

## Requirements

- PHP >= 8.3
- Composer
- Node.js + npm
- SQLite/MySQL (or another Laravel-supported database)

## Setup

```bash
composer install
npm install

cp .env.example .env
php artisan key:generate

php artisan migrate

npm run build
```

## Development

Run the full dev stack (server, queue listener, log tailer, and Vite) concurrently:

```bash
composer run dev
```

Or run pieces individually:

```bash
php artisan serve
npm run dev
php artisan queue:listen
php artisan pail
```

## Testing & Quality

```bash
composer test          # config clear + lint check + static analysis + tests
php artisan test --compact
composer lint          # fix code style with Pint
composer lint:check    # check code style without fixing
composer types:check   # run Larastan
```

## Project Structure

- `app/Livewire` — Livewire components (grouped under `Actions/`)
- `app/Models` — Eloquent models (Pelatihan, Post, Category, Tag, Faq, Testimonial, etc.)
- `app/Enums` — status and type enums
- `routes/` — routes split by domain (`pelatihan.php`, `berita.php`, `konten.php`, `settings.php`)
- `resources/views` — Blade views
