# Production Deployment Tracker

Tanggal mulai: 2026-06-08
Target: `https://miannajiyah.site`
Provider: Rumahweb Unlimited M
Status: berjalan bertahap

## Aturan Keselamatan

- `siamiannajiyah.my.id` adalah proyek terpisah dan tidak boleh diubah, dipindahkan, dihapus, atau dipakai sebagai target deployment.
- Database dan user `miak7156_sia_mi_annajiyah` tidak boleh diubah atau dipakai oleh aplikasi baru.
- Aplikasi baru memakai database `miak7156_spmb` dan user `miak7156_spmbusr`.
- Aplikasi Laravel ditempatkan di luar web root. Hanya isi folder `public` yang diaktifkan pada root `miannajiyah.site`.
- Folder `public_html/siamiannajiyah.my.id` wajib dipertahankan utuh pada setiap fase.
- `.env`, dump database, password, dan dokumen PPDB private tidak boleh masuk Git atau web root.
- Satu fase harus diverifikasi sebelum fase berikutnya dimulai.

## Status Fase

### Phase 1 - Baseline dan Isolasi Hosting

Status: `[x]` Selesai

- `[x]` Domain, SSL, document root, dan akses SSH diperiksa.
- `[x]` Backup awal `public_html` dibuat di server.
- `[x]` Paket aplikasi, dump database lokal, dan private storage dibuat terpisah.
- `[x]` Dump database diuji restore secara lokal.
- `[x]` `miannajiyah.site` dikunci ke PHP 8.3.
- `[x]` `siamiannajiyah.my.id` dikunci ke PHP 7.4.
- `[x]` Kedua domain diverifikasi merespons HTTPS 200.

Bukti penting:

- Backup server: `~/backups/public_html-before-laravel-20260608-194123.tar.gz`
- Handler domain utama awal: `alt-php83`
- Handler domain utama setelah Phase 4: `ea-php83`
- Handler domain lama: `ea-php74`

### Phase 2 - Database Production Terpisah

Status: `[x]` Selesai

- `[x]` Buat database `miak7156_spmb`.
- `[x]` Buat user `miak7156_spmbusr` dengan password acak kuat.
- `[x]` Berikan seluruh privilege user baru hanya ke database baru.
- `[x]` Uji koneksi MySQL menggunakan kredensial baru.
- `[x]` Verifikasi database lama tetap ada dan tidak berubah.
- `[x]` Catat checkpoint Phase 2.

Gate selesai:

- Database dan user baru terlihat di cPanel.
- Koneksi memakai user baru berhasil.
- Database `miak7156_sia_mi_annajiyah` tetap ada dan tidak digunakan.
- Belum ada data aplikasi yang diimpor.

Bukti verifikasi:

- Login MySQL mengembalikan database aktif `miak7156_spmb` dan user `miak7156_spmbusr@localhost`.
- Pembuatan, pengisian, dan pembacaan temporary table berhasil.
- `SHOW DATABASES` untuk user baru hanya menampilkan `information_schema` dan `miak7156_spmb`.
- cPanel tetap menampilkan database/user SIA lama secara terpisah.
- `miannajiyah.site` dan `siamiannajiyah.my.id` tetap merespons HTTPS 200; halaman SIA lama tetap dikenali.
- Database baru masih kosong dari tabel aplikasi. Dump lokal belum diimpor.

### Phase 3 - Upload ke Staging Server

Status: `[x]` Selesai

- `[x]` Buat direktori aplikasi di luar `public_html`.
- `[x]` Upload dan verifikasi checksum paket aplikasi.
- `[x]` Upload dump database dan paket private storage.
- `[x]` Ekstrak aplikasi dan private storage pada lokasi yang benar.
- `[x]` Pastikan dokumen private tidak dapat diakses dari URL publik.

Gate selesai:

- Seluruh file tersedia di staging.
- `public_html` belum diaktifkan sebagai Laravel.
- Folder proyek SIA lama tetap utuh.

Bukti verifikasi:

- Artifact staging tersimpan di `~/deploy/incoming/phase3-20260608`.
- Aplikasi diekstrak ke `~/miannajiyah-app`.
- Checksum remote cocok dengan artifact lokal:
  - `miannajiyah-app-20260608-192427.zip`: `49ca399b953306c9b933722bad4cab00286d9483086c95e163776bc43a68f1c3`
  - `spmb_annajiyah-20260608-192842.sql`: `76c48ab1dae3be65dae0134543a01566d122ec252264d57f3551d7620aac18b24`
  - `miannajiyah-private-storage-20260608-192946.zip`: `d01e3f70fee8a77bc30ffac110d0d343fdafa237c7e17327c56df43cfcadc43f`
- `~/miannajiyah-app` berukuran sekitar 81 MB dan memiliki 413 file.
- `~/miannajiyah-app/storage/app/private` berisi 64 file private.
- `~/miannajiyah-app/public/build` berisi 10 file build asset.
- `.env` dan `vendor` belum ada di staging app; ini memang menunggu Phase 4.
- Pencarian file sensitif `akte`, `kk`, `ktp`, dan `ijazah` di `~/miannajiyah-app/public` menghasilkan 0.
- Database `miak7156_spmb` masih kosong dari tabel aplikasi.
- `public_html/siamiannajiyah.my.id` dan `index.php` proyek SIA lama tetap ada.
- `miannajiyah.site` dan `siamiannajiyah.my.id` tetap merespons HTTPS 200; halaman SIA lama tetap dikenali.

### Phase 4 - Konfigurasi dan Data Production

Status: `[x]` Selesai

- `[x]` Buat `.env` production dengan `APP_DEBUG=false`.
- `[x]` Pasang dependency Composer production.
- `[x]` Verifikasi extension PHP wajib.
- `[x]` Impor dump lokal ke database baru.
- `[x]` Jalankan migration production yang belum ada.
- `[x]` Bersihkan session, cache, queue, dan active session lokal yang terbawa.
- `[x]` Jalankan command migrasi dokumen dan pembuatan varian media.
- `[x]` Buat cache config, route, dan view.

Gate selesai:

- Artisan bootstrap tanpa error.
- Database baru berisi data yang diharapkan.
- File private dan media publik konsisten.
- Tidak ada secret di web root.

Bukti verifikasi:

- Runtime `miannajiyah.site` disesuaikan dari `alt-php83` ke `ea-php83` karena `ea-php83` memiliki extension Laravel lengkap.
- Runtime `siamiannajiyah.my.id` tetap `ea-php74`; kedua domain tetap HTTPS 200.
- Extension PHP 8.3 terverifikasi lengkap untuk Laravel: `ctype`, `dom`, `fileinfo`, `filter`, `gd`, `hash`, `mbstring`, `openssl`, `PDO`, `pdo_mysql`, `session`, `tokenizer`, `xml`, `xmlreader`, `xmlwriter`, `zip`, dan `zlib`.
- `.env` production dibuat di `~/miannajiyah-app/.env` dengan permission `600`, `APP_DEBUG=false`, `APP_URL=https://miannajiyah.site`, dan database `miak7156_spmb`.
- Composer lokal dipasang di `~/bin/composer`. Karena `proc_open` dinonaktifkan di hosting, `composer install` dijalankan ulang dengan `--no-scripts`, lalu `php artisan package:discover` dijalankan manual dan berhasil.
- Vendor production dan autoload tersedia.
- Dump database diimpor ke `miak7156_spmb`. Saat impor, collation `utf8mb4_0900_ai_ci` diganti menjadi `utf8mb4_unicode_ci` dan `DEFINER` dihapus saat stream import agar kompatibel dengan MariaDB/shared hosting.
- `php artisan migrate --force` berhasil; seluruh 12 migration berstatus `Ran`.
- Data production terverifikasi: 19 objek tabel/view, 1 user, 15 siswa, 10 guru, 192 jadwal, dan 10 konten web.
- Runtime lokal yang terbawa dibersihkan: `sessions=0`, `cache=0`, `jobs=0`, `failed_jobs=0`, `active_session_users=0`.
- `php artisan media:generate-variants`: Generated 0, Skipped 52, Failed 0.
- `php artisan ppdb:migrate-public-documents`: Checked 0, Migrated 0, Missing 0, Unchanged 0.
- `php artisan ppdb:generate-document-thumbnails`: Checked 6, Generated/ready 6, Skipped 54, Failed 0.
- `config:cache`, `route:cache`, dan `view:cache` berhasil. View cache berisi 43 file.
- Belum ada aktivasi `public_html`; Laravel masih berada di staging luar web root.

### Phase 5 - Aktivasi Domain Utama

Status: `[x]` Selesai

- `[x]` Backup ulang root domain utama tepat sebelum aktivasi.
- `[x]` Salin hanya isi `public` Laravel ke root `miannajiyah.site`.
- `[x]` Sesuaikan bootstrap path pada `index.php` production.
- `[x]` Pertahankan `.well-known`, konfigurasi hosting, dan folder domain lama.
- `[x]` Aktifkan HTTPS redirect dan secure cookie.
- `[x]` Jalankan smoke test tanpa membuka akses dokumen private.

Gate selesai:

- Homepage Laravel tampil melalui HTTPS.
- Asset Vite dan media publik dapat dimuat.
- `siamiannajiyah.my.id` tetap berfungsi seperti sebelumnya.

Bukti verifikasi:

- Backup tepat sebelum aktivasi dibuat: `~/backups/public_html-before-phase5-20260608-205107.tar.gz`.
- Laravel public files disalin ke `~/public_html`; aplikasi utama tetap di `~/miannajiyah-app`.
- `~/public_html/index.php` menunjuk ke `/home/miak7156/miannajiyah-app/vendor/autoload.php` dan `/home/miak7156/miannajiyah-app/bootstrap/app.php`.
- `.htaccess` root mempertahankan handler `ea-php83` dan menambahkan rewrite Laravel serta redirect HTTP ke HTTPS.
- Folder `public_html/siamiannajiyah.my.id` dan `index.php` SIA lama tetap ada.
- Setelah aktivasi, permission induk `public_html` dikembalikan ke `750` karena mode `700` membuat web server tidak bisa membaca root dan folder SIA. Setelah koreksi, Laravel dan SIA kembali HTTP 200.
- Smoke test halaman publik lulus HTTP 200: `/`, `/pendaftaran`, `/cek-pendaftaran`, `/tenaga-pendidik`, `/fasilitas`, `/kegiatan`, dan `/sitemap.xml`.
- Browser smoke test mengenali title `MI Annajiyah - Madrasah Ibtidaiyah Unggulan`.
- Semua file asset di `public_html/build` merespons HTTP 200.
- Probe direct URL dokumen private `/storage/app/private/ppdb/test.pdf`, `/storage/ppdb/test.pdf`, dan `/private/ppdb/test.pdf` merespons 404.
- Pencarian file sensitif `akte`, `kk`, `ktp`, dan `ijazah` pada root publik utama menghasilkan 0.
- `siamiannajiyah.my.id` tetap HTTP 200 dan browser smoke test mengenali title `Login - SIA MI ANNAJIYAH`.

### Phase 6 - QA Production

Status: `[x]` Selesai

- `[x]` QA halaman publik desktop dan mobile.
- `[x]` QA formulir PPDB, validasi, upload, dan cetak kartu.
- `[x]` QA login serta fungsi admin/operator.
- `[x]` QA export, media, dokumen private, dan pembatasan role.
- `[x]` Periksa console browser, broken asset, overflow, status HTTP, SSL, dan header keamanan.
- `[x]` Verifikasi ulang versi PHP dan respons kedua domain.
- `[x]` Hapus data QA production bila memang dibuat khusus untuk pengujian.

Gate selesai:

- Alur kritis lulus tanpa error.
- Data sensitif tidak dapat diakses publik.
- Tidak ada regresi pada proyek SIA lama.

Bukti verifikasi:

- Browser QA desktop `1440x900` dan mobile `390x844` lulus untuk `/`, `/pendaftaran`, `/cek-pendaftaran`, `/tenaga-pendidik`, `/fasilitas`, dan `/kegiatan`.
- Seluruh halaman publik yang diuji tidak memiliki broken image, overflow horizontal, warning, atau error console.
- Menu mobile terbuka dan memuat seluruh navigasi publik yang diharapkan.
- Validasi formulir PPDB diuji per langkah untuk nama, NISN, data orang tua, WhatsApp, nomor KK, dan dokumen wajib. Tidak ada pendaftaran QA yang dikirim.
- Pencarian status kosong dan nomor yang tidak ditemukan menghasilkan validasi/status yang sesuai.
- Kartu pendaftaran existing melalui token private merespons HTTP 200, menampilkan judul kartu, dan tidak membocorkan debug marker.
- Login admin dan operator berhasil diuji memakai akun QA sementara.
- Halaman admin utama, PPDB, siswa, jadwal, guru, fasilitas, konten, manajemen user, dan ganti password berhasil dirender tanpa broken image, overflow, atau error console.
- Operator dapat membuka halaman operasional PPDB, tetapi `/admin/admin-users` ditolak dengan HTTP 403 dan pesan `Akses khusus admin`.
- Dokumen PPDB private dapat dibuka oleh admin terautentikasi. Tanpa autentikasi, route admin dan dokumen mengarah ke `/admin/login`.
- Export PPDB, siswa, dan guru menghasilkan response attachment XLSX valid berstatus 200 dengan magic byte ZIP/XLSX `PK`.
- HTTP domain utama mengarah 301 ke HTTPS. Header CSP, HSTS, nosniff, frame policy, referrer policy, dan permissions policy aktif.
- Sertifikat Let's Encrypt berlaku 7 Juni 2026 sampai 5 September 2026 dan SAN mencakup `miannajiyah.site` serta `www.miannajiyah.site`.
- PHP domain utama tetap `ea-php83`; domain lama tetap `ea-php74`.
- `miannajiyah.site` dan `siamiannajiyah.my.id` sama-sama merespons HTTPS 200 setelah seluruh QA.
- Tidak ada scheduled task Laravel, sehingga cron tidak perlu dibuat pada deployment ini.
- Seluruh akun QA sementara dihapus. Data kembali ke 1 user, 15 siswa, dan 7 activity log; active session admin asli tetap dipertahankan.
- Kredensial QA lokal dihapus. Log error selama setup QA diarsipkan private dengan permission `600`; log aktif setelah smoke test berisi 0 error.
- Pencarian akhir pada web root utama menemukan 0 file sensitif bernama akte, KK, KTP, atau ijazah.

### Phase 7 - Finalisasi dan Serah Terima

Status: `[x]` Selesai

- `[x]` Dokumentasikan lokasi aplikasi, backup, database, dan perintah maintenance.
- `[x]` Dokumentasikan prosedur rollback.
- `[x]` Pastikan permission file dan direktori production aman.
- `[x]` Catat hasil akhir di `TODO.md` dan `docs/HOSTING_READINESS.md`.
- `[x]` Tandai deployment selesai hanya setelah seluruh bukti QA tersedia.

Bukti final:

- Backup final dibuat di `~/backups/miannajiyah-final-20260609-001904`.
- Backup terdiri dari database, aplikasi, file publik domain utama, dan checksum SHA-256.
- `gzip -t`, pembacaan kedua tar archive, dan `sha256sum -c` lulus.
- Folder backup berpermission `700`; seluruh file backup berpermission `600`.
- Arsip file publik mengecualikan `public_html/siamiannajiyah.my.id`.
- Permission final: `public_html=750`, app/storage/cache private `700`, `.env=600`, public file utama `644`.
- Tidak ada file world-writable pada aplikasi baru atau web root utama.
- `storage` dan `bootstrap/cache` tetap writable untuk runtime Laravel.
- Seluruh 12 migration berstatus `Ran`; config, route, dan 43 compiled view cache tersedia.
- Data final: 1 user, 15 siswa, 10 guru, 192 jadwal, dan 10 konten web.
- Smoke test final HTTP 200 lulus untuk homepage, pendaftaran, cek status, login admin, sitemap, dan SIA lama.
- Artifact deployment sementara lokal, dump, private storage copy, dan file secret sementara sudah dihapus setelah backup final terverifikasi.
- Runbook production tersedia di `docs/PRODUCTION_OPERATIONS_2026-06-09.md`.

## Catatan Checkpoint

- 2026-06-08: Phase 1 selesai. Deployment sempat dijeda setelah isolasi PHP kedua domain.
- 2026-06-08: Deployment dilanjutkan secara bertahap. Phase 2 dimulai dengan scope database production baru saja.
- 2026-06-08: Phase 2 selesai. Database/user production baru dibuat, privilege dan koneksi diuji, serta tidak ada akses ke database SIA lama. Deployment berhenti sebelum Phase 3.
- 2026-06-08: Phase 3 selesai. Artifact aplikasi, dump, dan private storage sudah berada di staging luar web root. Belum ada impor data, Composer install, `.env` production, atau aktivasi `public_html`.
- 2026-06-08: Phase 4 selesai. Staging Laravel sudah memiliki `.env`, vendor, database production terisi, migration/cache/media command lulus, dan runtime domain utama dipindahkan ke `ea-php83` dengan SIA lama tetap `ea-php74`. Deployment berhenti sebelum Phase 5.
- 2026-06-08: Phase 5 selesai. Root `miannajiyah.site` aktif menjalankan Laravel dari app luar web root, asset publik lulus, HTTPS redirect aktif, dan SIA lama tetap berjalan. Deployment lanjut ke QA mendalam Phase 6.
- 2026-06-08: Phase 6 selesai. QA publik, formulir, kartu, admin/operator, export, dokumen private, SSL/header, dan isolasi kedua domain lulus. Seluruh data/kredensial QA sementara sudah dibersihkan. Deployment lanjut ke finalisasi dan serah terima Phase 7.
- 2026-06-09: Phase 7 selesai. Backup final terverifikasi, permission aman, runbook maintenance/rollback tersedia, data final konsisten, dan kedua domain lulus smoke test. Deployment `miannajiyah.site` dinyatakan selesai.
- 2026-06-09: Cleanup baseline pascadeployment selesai pada lokal dan production. Seluruh 15 siswa/pendaftar, 192 jadwal, dokumen PPDB private, thumbnail, activity log, session, cache, dan queue dihapus setelah backup terverifikasi. Satu akun admin serta seluruh konten/media publik dipertahankan. Production menyisakan 0 siswa dan 0 jadwal, seluruh sequence operasional kembali ke 1, 50 referensi media valid, dan browser QA publik lulus tanpa broken image atau error console.
