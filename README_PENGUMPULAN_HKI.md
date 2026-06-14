# Paket Pengumpulan Aplikasi SPMB MI Annajiyah

Paket ZIP pengumpulan berisi source code aplikasi Laravel, dokumentasi teknis,
manual book, aset media publik, dan hasil build frontend.

## Isi Paket

- source code Laravel dan Blade;
- migration, seeder, serta automated test;
- dokumentasi proyek pada folder `docs`;
- media publik pada `public/uploads`;
- hasil build Vite pada `public/build`;
- `Lampiran_Manual_Book_SPMB_MI_Annajiyah.docx`;
- `.env.example` sebagai contoh konfigurasi.

## Data yang Sengaja Tidak Disertakan

- `.env` dan kredensial production;
- database lokal atau salinan database production;
- dokumen pendaftar pada `storage/app/private/ppdb`;
- log, session, cache, dan file sementara;
- dependency `vendor` dan `node_modules`;
- konfigurasi tunnel serta path khusus komputer pengembang.

## Menjalankan Aplikasi

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
php artisan serve
```

Sesuaikan koneksi database pada `.env` sebelum migration. Hasil build frontend
sudah tersedia di paket, tetapi dapat dibuat ulang dengan `npm run build`.

## Verifikasi

Baseline source telah diverifikasi dengan:

```bash
php artisan test
npm run build
php artisan route:cache
php artisan view:cache
```

Checksum SHA-256 ZIP diberikan terpisah bersama berkas pengumpulan agar
integritas arsip dapat diperiksa.
