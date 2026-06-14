# Laporan Audit Final Lokal Sebelum Hosting

Tanggal audit: 2026-06-08
Scope: repository lokal, source code, dependency, runtime, database, migration, seeder, media, private document, route, security, test, build, cache, frontend publik, panel admin, responsivitas, aksesibilitas, serta prosedur backup/restore.
Deployment/staging/pemindahan file ke hosting: belum dilakukan.

## Kesimpulan Akhir Setelah Remediation

Status teknis lokal: **SIAP MASUK TAHAP PERSIAPAN HOSTING**.

Seluruh temuan P0 dan P1 pada audit ini telah diperbaiki dan diuji ulang. Gate A dan Gate B lulus. Gate C lulus untuk sisi kode dan environment lokal.

Eksekusi deployment belum dilakukan. Gate D tetap menunggu provider/domain, credential production, SSL, document root `public`, dan izin user untuk membuat baseline commit/push.

## Status Temuan

- `AUD-F001` ditutup: demo siswa/jadwal sekarang opt-in dan tidak berjalan di production.
- `AUD-F002` ditutup: PHP CLI memuat ZIP dan test export XLSX lulus.
- `AUD-F003` ditutup: file upload dibersihkan bila proses database gagal.
- `AUD-F004` ditutup: pembuatan siswa memakai distributed cache lock dan transaksi.
- `AUD-F005` ditutup: rumus overlap memakai interval eksklusif dan ruangan kosong tidak dibandingkan.
- `AUD-F006` aspek media ditutup: seeder Haikal memakai asset pengganti yang tersedia. Baseline Git masih menunggu izin commit.
- `AUD-F007` ditutup: media lama baru dihapus setelah database menerima media baru.
- `AUD-F008` ditutup: default timezone `Asia/Jakarta` dan dapat diatur dari environment.
- `AUD-F009` ditutup: security header middleware aktif dan kompatibel dengan Alpine/Google Maps.
- `AUD-F010` ditutup: log penghapusan hanya menyimpan identifier operasional minimum.
- `AUD-F011` ditutup: heading dan accessible name halaman publik/admin diverifikasi browser.
- `AUD-F012` ditutup: Pint lulus.
- `AUD-F013` ditingkatkan: test ditambah untuk cleanup upload, seeder production, jadwal, header, tabel PPDB, dan chart nol.
- `AUD-F014` ditutup: `.env.example` memakai log warning, secure cookie, timezone, proxy, dan flag demo yang aman.
- `AUD-F015` ditutup: trusted proxy hanya aktif bila `TRUSTED_PROXIES` diisi.
- `AUD-F016` ditutup dengan keputusan: Alpine patch diperbarui; major upgrade ditunda secara sengaja.
- `AUD-F017` ditutup: nilai skala chart dipisahkan dari nilai puncak aktual.

## Retest Akhir

- `php artisan test`: 49 passed, 239 assertions.
- `vendor/bin/pint --test`: lulus.
- `npm run build`: lulus.
- `composer audit` dan `npm audit --omit=dev`: bersih.
- Route/view cache: lulus dan dibersihkan kembali.
- Fresh migration dan rollback database temporary: lulus.
- Browser QA publik/admin desktop dan 375 px: tidak ada console error baru, broken image, horizontal overflow, atau control tanpa accessible name.
- Tabel admin PPDB tidak lagi memuat kolom Dokumen; empat dokumen tetap tersedia dari modal detail.

## Temuan P0

### AUD-F001 - Seeder production memasukkan data demo

Lokasi:

- `database/seeders/DatabaseSeeder.php:33`
- `database/seeders/DatabaseSeeder.php:34`
- `database/seeders/NewFeaturesSeeder.php:122`
- `database/seeders/NewFeaturesSeeder.php:125`

Bukti reproduksi pada SQLite memory dengan environment production:

- user: 0;
- siswa demo: 12;
- guru: 10;
- kegiatan: 32;
- jadwal: 192.

Dampak: menjalankan `php artisan db:seed --force` di production dapat mencemari database sekolah dengan data simulasi.

Rekomendasi: pisahkan baseline content seeder, demo seeder, dan initial-admin seeder. Demo siswa/jadwal tidak boleh dipanggil oleh `DatabaseSeeder` pada production.

### AUD-F002 - Full test belum lulus karena extension ZIP tidak aktif

Bukti:

- `php artisan test`: 42 passed, 1 failed.
- Test gagal: `AdminExportTest`.
- Error: class `ZipArchive` tidak tersedia.
- `composer check-platform-reqs`: `ext-zip` missing.
- PHP CLI memuat `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.ini`.
- `php.ini:832` masih berisi `;extension=zip`.

Dampak: export XLSX tidak dapat dijalankan pada runtime tersebut.

Rekomendasi: aktifkan `ext-zip` pada runtime lokal dan pastikan hosting menyediakan extension yang sama, kemudian jalankan ulang seluruh test dan export nyata.

### AUD-F003 - Pendaftaran dapat meninggalkan dokumen yatim

Lokasi:

- `app/Http/Controllers/RegistrationController.php:54`
- `app/Http/Controllers/RegistrationController.php:61`
- `app/Http/Controllers/RegistrationController.php:88`

Dokumen diupload satu per satu sebelum record siswa dibuat. Catch hanya menulis log dan tidak menghapus file yang sudah tersimpan.

Bukti simulasi: tiga dokumen tetap ada setelah kegagalan database disimulasikan.

Dampak: kegagalan database, nomor pendaftaran collision, atau exception lain dapat meninggalkan dokumen pribadi tanpa pemilik di storage.

Rekomendasi: gunakan transaksi database serta cleanup seluruh path upload pada catch. Tambahkan test kegagalan setelah upload.

### AUD-F004 - Nomor pendaftaran tidak aman terhadap request bersamaan

Lokasi:

- `app/Helpers/PpdbHelper.php:74`
- `app/Helpers/PpdbHelper.php:77`

Nomor berikutnya dihitung memakai `count() + 1`. Dua request bersamaan dapat memilih nomor yang sama.

Dampak: salah satu insert gagal oleh unique index dan, karena AUD-F003, dokumennya dapat tertinggal.

Rekomendasi: gunakan counter yang dikunci dalam transaksi, retry saat unique collision, atau strategi sequence yang atomic.

## Temuan P1

### AUD-F005 - Deteksi bentrok jadwal menghasilkan false positive

Lokasi:

- `app/Http/Controllers/Admin/JadwalController.php:55`
- `app/Http/Controllers/Admin/JadwalController.php:65`

Masalah:

- `whereBetween` bersifat inklusif sehingga jadwal 08.00-09.00 dianggap bertabrakan dengan jadwal yang berakhir tepat 08.00;
- `orWhere('ruangan', null)` menganggap semua jadwal tanpa ruangan sebagai memakai ruangan yang sama.

Bukti reproduksi: jadwal berurutan dengan guru dan kelas berbeda tetap terdeteksi bentrok.

Rekomendasi: gunakan overlap `existing.start < new.end AND existing.end > new.start`; bandingkan ruangan hanya jika input ruangan terisi; tambahkan test jadwal berurutan, overlap nyata, dan edit.

### AUD-F006 - Baseline Git belum stabil dan media tracked hilang

Bukti:

- worktree berisi banyak perubahan modified/untracked;
- `public/uploads/guru/Haikal_Fikri.webp` dan thumbnail-nya tercatat deleted;
- seeder masih merujuk `uploads/guru/Haikal_Fikri.webp` pada `database/seeders/NewFeaturesSeeder.php:52`;
- 152 file upload tracked, dua di antaranya hilang dari disk.

Database aktual tidak memiliki referensi media hilang karena record Haikal memakai path upload pengganti. Fresh seed tetap berpotensi menghasilkan gambar rusak.

Rekomendasi: putuskan asset final Haikal, selaraskan seeder, lalu tetapkan commit/branch baseline sebelum membuat paket hosting.

### AUD-F007 - Penggantian banner menghapus file lama terlalu cepat

Lokasi:

- `app/Http/Controllers/Admin/KontenController.php:196`
- `app/Http/Controllers/Admin/KontenController.php:197`

File lama dihapus sebelum upload/optimasi file baru dipastikan berhasil.

Dampak: jika proses GD, penyimpanan, atau pembuatan varian gagal, record masih dapat kehilangan media lama.

Rekomendasi: simpan file baru dan seluruh varian terlebih dahulu, update database, baru hapus image set lama.

### AUD-F008 - Konfigurasi waktu aplikasi masih UTC

Lokasi: `config/app.php:68`.

Runtime audit:

- locale: Indonesia;
- timezone: UTC;
- server/user berada pada konteks Asia/Jakarta.

Dampak: waktu pendaftaran, verifikasi, activity log, chart harian, dan nama hari jadwal dapat bergeser tujuh jam.

Rekomendasi: buat timezone berbasis environment dengan default `Asia/Jakarta`, lalu uji tanggal dan jadwal.

### AUD-F009 - Header keamanan HTTP belum diterapkan

Header yang kosong pada response beranda:

- `Content-Security-Policy`;
- `Strict-Transport-Security`;
- `X-Content-Type-Options`;
- `X-Frame-Options`;
- `Referrer-Policy`;
- `Permissions-Policy`.

Dampak: pertahanan browser terhadap framing, MIME sniffing, kebocoran referrer, dan injeksi resource belum optimal.

Rekomendasi: tambahkan header yang kompatibel dengan Vite, Alpine, Google Maps iframe, dan asset aplikasi. HSTS hanya diaktifkan setelah HTTPS production stabil.

### AUD-F010 - Seluruh data siswa disalin ke activity log saat penghapusan

Lokasi:

- `app/Http/Controllers/Admin/PpdbController.php:182`
- `app/Http/Controllers/Admin/SiswaController.php:126`

`$siswa->toArray()` dapat memasukkan NISN, NIS, nomor KK, alamat, WhatsApp, token, catatan, dan path dokumen ke metadata log.

Dampak: data pribadi yang sudah dihapus tetap tersalin di tabel log dan memperluas permukaan data sensitif.

Rekomendasi: log hanya identifier minimum, nomor pendaftaran, nama tersamarkan, status, dan actor. Tentukan retention activity log.

## Temuan P2

### AUD-F011 - Aksesibilitas form dan heading belum memadai

Hasil browser audit:

- 17 dari 17 control form pendaftaran tidak memiliki label programatik;
- field cek pendaftaran tidak memiliki label programatik;
- banyak form admin memiliki kondisi serupa;
- layout publik menghasilkan dua `h1` per halaman;
- halaman admin utama tidak memiliki `h1`;
- beberapa tombol ikon tidak memiliki accessible name yang konsisten.

Rekomendasi: tambahkan `id` dan `for`, `aria-label` untuk tombol ikon, satu heading utama per halaman, serta test keyboard/focus.

### AUD-F012 - Pint belum lulus

`vendor/bin/pint --test` gagal pada banyak file aplikasi, command, migration, seeder, route, dan test.

Dampak: style tidak konsisten dan review perubahan menjadi lebih sulit. Ini bukan kegagalan runtime.

Rekomendasi: jalankan Pint sebagai perubahan mekanis terpisah setelah baseline Git disepakati, kemudian regression test.

### AUD-F013 - Coverage test belum mencakup beberapa jalur penting

Belum ada coverage khusus untuk:

- bentrok dan CRUD jadwal;
- kegagalan parsial upload pendaftaran;
- concurrency/retry nomor pendaftaran;
- production seeder tidak membuat data demo;
- export PDF/print;
- update media yang gagal;
- security headers dan custom error pages;
- backup/restore dan konfigurasi production.

### AUD-F014 - Konfigurasi lokal dan template production perlu dipisahkan lebih tegas

Kondisi `.env` lokal saat audit:

- `APP_ENV=local`;
- `APP_DEBUG=true`;
- `APP_URL` menunjuk domain HTTPS;
- `LOG_LEVEL=debug`;
- secure cookie belum aktif.

Kondisi tersebut sesuai untuk audit lokal tertentu, tetapi tidak boleh dipindahkan apa adanya ke hosting.

`.env.example` juga masih memakai `LOG_LEVEL=debug` dan secure cookie hanya berupa komentar.

### AUD-F015 - Proxy trust terlalu luas untuk konfigurasi yang belum final

Lokasi: `bootstrap/app.php:14`.

`trustProxies(at: '*')` mempercayai seluruh proxy. Ini dapat sesuai pada platform tertentu, tetapi harus diputuskan berdasarkan topologi hosting final agar header forwarded tidak dipercaya secara berlebihan.

### AUD-F016 - Dependency memiliki versi major lebih baru

Tidak ada vulnerability advisory. Beberapa package memiliki major baru, tetapi proyek sengaja menargetkan Laravel 12/Vite 6 untuk kompatibilitas hosting. Alpine memiliki patch `3.15.12` sementara yang terpasang `3.15.11`.

Rekomendasi: jangan melakukan major upgrade menjelang hosting tanpa kebutuhan. Evaluasi patch/minor secara terpisah.

### AUD-F017 - Nilai puncak chart dashboard salah saat seluruh data nol

Lokasi:

- `resources/views/admin/dashboard.blade.php:91`
- `resources/views/admin/dashboard.blade.php:152`

`$maxChartValue` dipaksa minimal satu untuk kebutuhan skala SVG, tetapi nilai yang sama ditampilkan sebagai statistik "Puncak". Browser QA menunjukkan seluruh titik bernilai nol, sedangkan teks menampilkan `Puncak: 1`.

Rekomendasi: pisahkan nilai maksimum data sebenarnya dari nilai maksimum skala chart.

## Verifikasi Yang Lulus

- PHP syntax: 85 file valid.
- `composer validate --strict`: lulus.
- `composer audit`: tidak ada advisory.
- `npm audit --audit-level=moderate`: 0 vulnerability.
- `npm run build`: lulus.
- Manifest Vite: seluruh 9 entry menunjuk file yang tersedia.
- Route: 49 route non-vendor; seluruh route admin selain login berada di middleware auth.
- MySQL aktual: seluruh 12 migration `Ran`.
- MySQL temporary: fresh migration dan rollback seluruh migration berhasil.
- Database aktual:
  - 14 siswa;
  - tidak ada duplicate NISN, NIS, token, atau nomor pendaftaran;
  - tidak ada token/nomor pendaftaran null;
  - tidak ada status PPDB invalid;
  - tersedia satu akun admin.
- Integritas file database:
  - tidak ada media publik referensi database yang hilang;
  - tidak ada dokumen private referensi database yang hilang.
- Private storage:
  - 26 dokumen, tidak ada zero-byte file;
  - thumbnail private tersedia.
- Backup/restore MySQL:
  - dump berhasil;
  - restore ke database sementara berhasil;
  - 19 tabel dan 14 siswa cocok;
  - database sementara dan dump audit sudah dibersihkan.
- Backup/restore media dan private document:
  - 259 file berhasil diarsipkan dan diekstrak ke folder temporary;
  - jumlah file dan total byte hasil restore sama dengan sumber;
  - archive dan folder temporary sudah dibersihkan.
- Cache command:
  - config cache berhasil;
  - route cache berhasil;
  - view cache berhasil;
  - cache audit dibersihkan kembali.
- Proteksi document root lokal:
  - `.env`, `composer.json`, dan `vendor/autoload.php` tidak dapat diakses;
  - private storage tidak dapat dibuka langsung.
- Browser QA:
  - halaman publik utama dan seluruh halaman admin utama dapat dirender;
  - tidak ada console warning/error pada halaman yang diperiksa;
  - lazy-loaded image selesai tanpa broken image;
  - tidak ada horizontal overflow pada desktop dan viewport sekitar 375 px.

## Urutan Perbaikan Yang Direkomendasikan

1. P0: pisahkan production/demo seeder.
2. P0: aktifkan ZIP dan pastikan full test hijau.
3. P0: buat transaksi/cleanup upload dan nomor pendaftaran atomic/retry.
4. P1: perbaiki deteksi bentrok jadwal dan tambah test.
5. P1: stabilkan baseline Git dan media Haikal.
6. P1: perbaiki urutan replacement media banner.
7. P1: timezone, minimisasi log data pribadi, dan security headers.
8. P2: aksesibilitas, Pint, serta coverage test tambahan.
9. Jalankan ulang seluruh verifikasi dan browser QA.
10. Baru buat keputusan Gate C: siap atau belum siap dipindahkan ke hosting.
