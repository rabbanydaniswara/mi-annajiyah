# Hosting Readiness

Tanggal update: 2026-05-28

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

## Checklist Hosting Final

Pastikan paket hosting final menyediakan:

- PHP 8.2 atau 8.3.
- Composer 2.x.
- SSH/terminal, minimal untuk `composer install`, `php artisan migrate`, dan cache command.
- Extension PHP: `ctype`, `filter`, `hash`, `mbstring`, `openssl`, `pdo`, `pdo_mysql`, `session`, `tokenizer`, `xml`, `dom`, `fileinfo`, `gd`.
- MySQL/MariaDB database dan user database.
- Kemampuan set document root/subdomain ke folder `public`.
- Cron job jika nanti memakai scheduler.
- Queue worker jika nanti queue benar-benar dipakai terus menerus.

## Strategi Deploy Asset

Asset frontend sebaiknya dibuild di lokal/CI:

```bash
npm install
npm run build
```

Lalu deploy hasil `public/build` ke hosting. Folder `node_modules` tidak perlu diupload.

## Konfigurasi Production

Template `.env.example` sudah diarahkan ke default production-safe:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `DB_CONNECTION=mysql`
- Locale Indonesia

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
