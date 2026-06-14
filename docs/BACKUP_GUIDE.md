# Panduan Backup dan Recovery Lokal

Gunakan panduan ini sebelum menjalankan migration besar, perubahan storage, atau import data.

## Area yang Wajib Dibackup

- Database aplikasi.
- Folder dokumen private: `storage/app/private` dan `storage/app/ppdb` bila ada.
- Folder upload publik: `public/uploads`.
- File `.env` lokal atau production.

## Backup Database MySQL/MariaDB

```bash
mysqldump -u USERNAME -p NAMA_DATABASE > backup-mi-annajiyah-YYYYMMDD.sql
```

Simpan file hasil backup di luar folder public web.

## Backup SQLite Lokal Jika Dipakai Testing/Dev

```bash
copy database\database.sqlite backups\database-YYYYMMDD.sqlite
```

Pastikan folder `backups` tidak ikut dipublish ke web public.

## Backup Upload

```bash
xcopy public\uploads backups\uploads-YYYYMMDD\ /E /I
xcopy storage\app backups\storage-app-YYYYMMDD\ /E /I
```

## Restore Database MySQL/MariaDB

```bash
mysql -u USERNAME -p NAMA_DATABASE < backup-mi-annajiyah-YYYYMMDD.sql
```

## Checklist Sebelum Migration Besar

- Backup database sudah dibuat.
- Backup upload public dan private sudah dibuat.
- `php artisan test` lulus di lokal.
- Migration sudah diuji dengan `php artisan migrate:fresh --env=testing --force`.
- Jika production, jalankan migration dengan `php artisan migrate --force` hanya setelah backup selesai.

## Backup Production Saat Ini

Backup final deployment Rumahweb dibuat di luar web root:

```text
/home/miak7156/backups/miannajiyah-final-20260609-001904
```

Backup tersebut mencakup aplikasi, file publik domain utama, database `miak7156_spmb`, dan checksum SHA-256. Folder `public_html/siamiannajiyah.my.id` dikecualikan karena merupakan proyek terpisah.

Panduan verifikasi, maintenance, dan rollback production tersedia di `docs/PRODUCTION_OPERATIONS_2026-06-09.md`.
