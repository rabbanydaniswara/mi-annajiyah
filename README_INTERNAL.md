# SPMB Annajiyah Laravel

Aplikasi web SPMB/PPDB MI Annajiyah berbasis Laravel.

## Untuk AI Agent

Jika proyek ini dikerjakan oleh AI agent lain, mulai dari:

1. `TODO.md`
2. `AGENTS.md`
3. `docs/PROJECT_RULES.md`
4. `docs/PROJECT_STRUCTURE.md`
5. `docs/REMEDIATION_TRACKER.md`

`TODO.md` adalah tracker aktif dan harus diperbarui setiap ada perubahan pekerjaan.

## Dokumentasi Proyek

Dokumentasi utama ada di folder `docs`:

- `docs/PROJECT_AUDIT.md`: temuan risiko, dampak, lokasi file, dan rekomendasi perbaikan.
- `docs/PROJECT_STRUCTURE.md`: peta struktur aplikasi, modul, route, model, dan alur data.
- `docs/PROJECT_RULES.md`: aturan teknis yang sebaiknya dipatuhi saat mengubah proyek.
- `docs/DEVELOPMENT_GUIDELINES.md`: panduan setup, verifikasi, testing, deployment, dan backlog prioritas.
- `docs/REMEDIATION_TRACKER.md`: task tracker bertahap untuk perbaikan audit dan kesiapan hosting.
- `docs/APP_IMPROVEMENT_ROADMAP.md`: roadmap pengembangan aplikasi sebelum hosting.
- `docs/HOSTING_READINESS.md`: target runtime hosting, checklist paket hosting, dan strategi deploy asset.
- `docs/ROLE_MATRIX.md`: matriks hak akses admin dan operator.
- `docs/CONTENT_MEDIA_CHECKLIST.md`: checklist konten dan media publik sebelum publish.
- `docs/BACKUP_GUIDE.md`: panduan backup dan recovery lokal sebelum migration/perubahan besar.
- `docs/PERFORMANCE_MEDIA_PRESERVATION_PLAN.md`: rencana optimasi website serta aturan menjaga data/gambar baseline.

## Setup Lokal Singkat

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Sesuaikan koneksi database di `.env` sebelum menjalankan migration.

## Verifikasi Umum

```bash
php artisan test
npm run build
```

Untuk production readiness:

```bash
php artisan route:cache
php artisan route:clear
php artisan view:cache
php artisan view:clear
```

## Catatan Media

- Asset web publik berada di `public/uploads`.
- Dokumen pendaftaran sensitif berada di `storage/app/private/ppdb` dan tidak boleh dipindah ke public.
- Varian gambar publik seperti `*_card.webp` dan `*_hero.webp` dipakai untuk mempercepat halaman.
- Jangan menghapus media baseline tanpa backup dan instruksi eksplisit.
