# Operasional Production MI Annajiyah

Tanggal finalisasi: 2026-06-09
Domain aplikasi: `https://miannajiyah.site`

## Lokasi Production

- Aplikasi Laravel: `/home/miak7156/miannajiyah-app`
- Web root domain utama: `/home/miak7156/public_html`
- Dokumen PPDB private: `/home/miak7156/miannajiyah-app/storage/app/private/ppdb`
- Thumbnail dokumen private: `/home/miak7156/miannajiyah-app/storage/app/private/ppdb-thumbs`
- Media publik: `/home/miak7156/public_html/uploads`
- Database: `miak7156_spmb`
- PHP CLI: `/opt/cpanel/ea-php83/root/usr/bin/php`
- Composer: `/home/miak7156/bin/composer`

## Batas Kritis

`siamiannajiyah.my.id` adalah aplikasi terpisah. Jangan mengubah atau menghapus:

- `/home/miak7156/public_html/siamiannajiyah.my.id`
- database/user SIA lama
- handler PHP 7.4 milik domain tersebut

Domain utama memakai `ea-php83`, sedangkan domain SIA lama memakai `ea-php74`.

## Backup Final

Backup final tersimpan di:

```text
/home/miak7156/backups/miannajiyah-final-20260609-001904
```

Isi backup:

- `application.tar.gz`
- `public-main.tar.gz`
- `database-miak7156_spmb.sql.gz`
- `SHA256SUMS`

Folder backup berpermission `700` dan file di dalamnya `600`. Arsip publik tidak memuat folder `siamiannajiyah.my.id`.

Setelah deployment, data operasional contoh dibersihkan agar production hanya berisi baseline publik. Kebijakan dan klasifikasi tabel tersedia di `docs/DATA_BASELINE_POLICY.md`.

Backup sebelum cleanup:

```text
/home/miak7156/backups/baseline-clean-before-20260609-003705
```

Backup database clean-state:

```text
/home/miak7156/backups/baseline-clean-after-20260609-004423
```

Verifikasi integritas:

```bash
cd ~/backups/miannajiyah-final-20260609-001904
sha256sum -c SHA256SUMS
gzip -t database-miak7156_spmb.sql.gz
tar -tzf application.tar.gz >/dev/null
tar -tzf public-main.tar.gz >/dev/null
```

## Perintah Maintenance

Masuk ke aplikasi:

```bash
cd ~/miannajiyah-app
PHP=/opt/cpanel/ea-php83/root/usr/bin/php
```

Pemeriksaan umum:

```bash
$PHP artisan migrate:status
$PHP artisan route:list
$PHP artisan schedule:list
tail -n 100 storage/logs/laravel.log
```

Refresh cache setelah perubahan production:

```bash
$PHP artisan optimize:clear
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
```

Project saat finalisasi tidak memiliki scheduled task, sehingga cron Laravel belum diperlukan.

Jangan menjalankan seeder dengan `SEED_DEMO_DATA=true` pada baseline atau production karena opsi tersebut membuat data siswa dan jadwal demo.

## Prosedur Update

1. Buat backup database, aplikasi, private storage, media publik, dan `.env`.
2. Aktifkan maintenance mode:

```bash
$PHP artisan down
```

3. Upload perubahan ke `~/miannajiyah-app`, bukan ke folder SIA lama.
4. Install dependency production. Hosting menonaktifkan `proc_open`, jadi gunakan:

```bash
~/bin/composer install --no-dev --optimize-autoloader --no-scripts
$PHP artisan package:discover
```

5. Jalankan migration dan command media:

```bash
$PHP artisan migrate --force
$PHP artisan media:generate-variants
$PHP artisan ppdb:migrate-public-documents
$PHP artisan ppdb:generate-document-thumbnails
```

6. Salin hanya isi `public` Laravel ke web root utama. Jangan menimpa atau menghapus `public_html/siamiannajiyah.my.id`.
7. Buat ulang cache lalu nonaktifkan maintenance mode:

```bash
$PHP artisan config:cache
$PHP artisan route:cache
$PHP artisan view:cache
$PHP artisan up
```

8. Uji halaman publik, login admin, dokumen private, export, dan kedua domain.

## Prosedur Rollback

1. Aktifkan maintenance mode dan buat salinan kondisi gagal.
2. Verifikasi checksum backup yang akan dipakai.
3. Restore database hanya ke `miak7156_spmb`:

```bash
gzip -dc ~/backups/miannajiyah-final-20260609-001904/database-miak7156_spmb.sql.gz \
  | mysql -u USER_DATABASE -p miak7156_spmb
```

4. Restore `application.tar.gz` ke home akun. Pastikan `.env` tetap berpermission `600`, lalu install ulang vendor menggunakan prosedur `--no-scripts`.
5. Ekstrak `public-main.tar.gz` ke direktori sementara. Salin isinya ke `public_html` dengan pengecualian mutlak `siamiannajiyah.my.id`.
6. Jalankan cache command, periksa migration, lalu jalankan `$PHP artisan up`.
7. Pastikan `miannajiyah.site` dan `siamiannajiyah.my.id` sama-sama merespons HTTPS 200.

Jangan melakukan rollback dengan menghapus seluruh `public_html`, karena folder tersebut juga menampung aplikasi SIA lama.

## Smoke Test Minimum

```text
https://miannajiyah.site/
https://miannajiyah.site/pendaftaran
https://miannajiyah.site/cek-pendaftaran
https://miannajiyah.site/admin/login
https://miannajiyah.site/sitemap.xml
https://siamiannajiyah.my.id/
```

Route `/admin` dan dokumen PPDB harus mengarah ke login jika tidak terautentikasi. URL langsung ke `storage/app/private` harus menghasilkan 404.

## Riwayat Update

### 2026-06-14 - Pengaturan Buka/Tutup PPDB

- Fitur status pendaftaran, pesan publik saat ditutup, guard submit server-side, serta indikator publik/admin dipasang ke production.
- Status akhir dipertahankan `open` untuk tahun ajaran 2026/2027.
- Backup sebelum patch: `/home/miak7156/backups/ppdb-open-close-before-20260614-073257`.
- Uji siklus production membuktikan kondisi tertutup menyembunyikan form dan menolak POST `/api/pendaftaran` dengan HTTP 403 serta kode `ppdb_closed`.
- Cache config, route, dan view berhasil dibuat ulang; tidak ada migration baru.
- Browser QA desktop/mobile lulus tanpa broken image, overflow, warning, atau console error.
- Domain utama dan `siamiannajiyah.my.id` tetap HTTPS 200; folder dan aplikasi SIA lama tidak diubah.
