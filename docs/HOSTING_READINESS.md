# Hosting Readiness

Tanggal update: 2026-06-08

## Status Production

Deployment production ke `https://miannajiyah.site` selesai diverifikasi pada 2026-06-09.

- Aplikasi berada di `/home/miak7156/miannajiyah-app`.
- Web root domain utama berada di `/home/miak7156/public_html`.
- Runtime domain utama memakai `ea-php83`.
- Database production memakai `miak7156_spmb`.
- `APP_ENV=production`, `APP_DEBUG=false`, HTTPS redirect, secure cookie, dan security headers aktif.
- Seluruh 12 migration berstatus `Ran`.
- Aplikasi tidak memiliki scheduled task, sehingga cron belum diperlukan.
- Runbook maintenance dan rollback: `docs/PRODUCTION_OPERATIONS_2026-06-09.md`.
- Tracker dan bukti QA: `docs/PRODUCTION_DEPLOYMENT_TRACKER_2026-06-08.md`.

Domain `siamiannajiyah.my.id` tetap merupakan proyek terpisah dengan PHP 7.4 dan tidak boleh ikut diubah saat maintenance aplikasi ini.

## Target Runtime

Target aman untuk shared hosting/cPanel umum:

- PHP: 8.2 atau 8.3
- Composer: 2.x
- Database: MySQL/MariaDB
- Laravel: 12.x
- Node: tidak wajib di hosting jika asset dibuild lokal/CI
- Document root: wajib diarahkan ke folder `public`

## Runtime Lokal Saat Ini

- PHP CLI: 8.3.30
- Composer: 2.9.7
- Node.js: 24.14.0
- npm: 11.9.0
- Laravel: 12.61.0

## Dependency Yang Dipilih

- `php`: `^8.2`
- `laravel/framework`: `^12.0`
- `laravel/tinker`: `^2.10`
- `phpunit/phpunit`: `^11.5.50`
- `vite`: `6.4.2`
- `laravel-vite-plugin`: `1.3.0`
- `tailwindcss`: `4.1.17`
- `@tailwindcss/vite`: `4.1.17`
- `maatwebsite/excel`: `^3.1.69`
- `phpoffice/phpspreadsheet`: `1.30.5` via Laravel Excel

## Checklist Hosting Final

Pastikan paket hosting final menyediakan:

- PHP 8.2 atau 8.3.
- Composer 2.x.
- SSH/terminal, minimal untuk `composer install`, `php artisan migrate`, dan cache command.
- Extension PHP: `ctype`, `filter`, `hash`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `session`, `tokenizer`, `xml`, `dom`, `xmlreader`, `xmlwriter`, `fileinfo`, `gd`, `zip`, `zlib`.
- MySQL/MariaDB database dan user database.
- Kemampuan set document root/subdomain ke folder `public`.
- Cron job jika nanti memakai scheduler.
- Queue worker jika nanti queue benar-benar dipakai terus menerus.

Status provider Rumahweb saat deployment:

- PHP 8.3 dan extension wajib: tersedia.
- Composer 2 dan SSH: tersedia.
- MySQL/MariaDB terpisah: tersedia dan teruji.
- Asset production: sudah dibuild dan aktif.
- Cron: tidak dibuat karena `artisan schedule:list` tidak memiliki task.
- Queue worker: tidak diperlukan oleh alur aplikasi saat ini.

## Strategi Deploy Asset

Asset frontend sebaiknya dibuild di lokal/CI:

```bash
npm install
npm run build
```

Lalu deploy hasil `public/build` ke hosting. Folder `node_modules` tidak perlu diupload.

Catatan asset saat ini:

- Halaman publik memakai subset FontAwesome khusus agar tidak memuat CSS icon penuh.
- Panel admin tetap memakai FontAwesome penuh karena fitur admin mengizinkan input class ikon.
- Media publik memakai varian ringan `*_card.webp` dan `*_hero.webp`.
- Logo kecil halaman web memakai `public/logo-web.webp`; `logo.png` tetap dipertahankan untuk favicon, metadata, dan kebutuhan cetak.
- Preview dokumen PPDB admin memakai thumbnail private di `storage/app/private/ppdb-thumbs`.
- File media asli tetap dipertahankan sebagai baseline dan tidak dihapus oleh proses optimasi.

## Konfigurasi Production

Template `.env.example` sudah diarahkan ke default production-safe:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `DB_CONNECTION=mysql`
- Locale Indonesia
- Timezone `Asia/Jakarta`
- `LOG_LEVEL=warning`
- `SESSION_SECURE_COOKIE=true`
- data demo nonaktif melalui `SEED_DEMO_DATA=false`
- trusted proxy kosong sampai topologi hosting diketahui

Saat deploy, isi nilai sebenarnya untuk:

- `APP_KEY`
- `APP_URL`
- `DB_DATABASE`
- `DB_USERNAME`
- `DB_PASSWORD`
- konfigurasi email jika dipakai

Jika sudah HTTPS, aktifkan:

```env
SESSION_SECURE_COOKIE=true
```

Isi `TRUSTED_PROXIES=*` hanya jika provider memang menempatkan aplikasi di belakang reverse proxy tepercaya. Untuk daftar proxy tertentu, gunakan daftar IP dipisahkan koma.

Seeder production tidak membuat siswa atau jadwal demo. Jangan mengaktifkan `SEED_DEMO_DATA` di production.

## Command Production Saat Deploy

Jalankan setelah file aplikasi dan `.env` production siap:

```bash
php -m | grep -i zip
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan media:generate-variants
php artisan ppdb:migrate-public-documents
php artisan ppdb:generate-document-thumbnails
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika ada perubahan route/config/view setelah deploy, jalankan ulang cache command yang relevan. Route cache sudah diuji setelah sitemap dipindahkan ke controller.

## Cache dan Kompresi

`public/.htaccess` sudah menyiapkan:

- Rewrite ke `index.php`.
- Kompresi gzip melalui `mod_deflate` jika tersedia.
- Cache header panjang untuk gambar, font, CSS, dan JS.
- Proteksi directory listing.

Jika hosting menyediakan Brotli/CDN, aktifkan dari panel hosting untuk tambahan kompresi.
