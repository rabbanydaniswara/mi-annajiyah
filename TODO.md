# TODO Proyek MI Annajiyah

Tanggal update: 2026-06-14

File ini adalah tracker utama yang harus dibaca dan diperbarui setiap ada perubahan pengerjaan, penambahan fitur, perbaikan, QA, commit, atau push.

## Aturan Update

- Update file ini setelah sebuah task selesai, ditunda, atau berubah prioritas.
- Catat keputusan penting yang memengaruhi alur aplikasi, data, media, keamanan, atau deployment.
- Jika ada perubahan file media yang dipakai web, catat apakah sudah dicek, sudah commit, dan sudah push.
- Jangan tandai task selesai jika belum diverifikasi sesuai kebutuhan task.

## Status Legend

- `[ ]` Belum dikerjakan
- `[~]` Sedang dikerjakan
- `[x]` ~~Selesai~~
- `[!]` Tertahan / perlu keputusan

Catatan tampilan: task selesai ditulis dengan coret (`~~teks~~`) agar terlihat samar/berbeda di Markdown. Task aktif dan tertahan tidak dicoret.

## Aktif / Berikutnya

- `[x]` ~~Tambahkan pengaturan buka/tutup pendaftaran PPDB dari admin.~~
  - ~~Status pendaftaran, tahun ajaran aktif, dan pesan publik saat ditutup dapat diatur dari tab `Konten > PPDB` tanpa mengubah kode.~~
  - ~~Default tetap terbuka jika setting status belum tersedia agar instalasi dan data lama tetap kompatibel.~~
  - ~~Halaman publik, CTA homepage/navbar/fasilitas, dan dashboard admin menampilkan status PPDB secara konsisten.~~
  - ~~Saat ditutup, `/pendaftaran` menampilkan panel informasi tanpa form dan POST `/api/pendaftaran` ditolak server-side dengan HTTP 403 serta pesan publik yang sama.~~
  - ~~Tes mencakup penyimpanan setting admin, validasi pesan penutupan, tampilan publik, default terbuka, dan penolakan submit langsung tanpa menyimpan data atau dokumen.~~
  - ~~Patch production dipasang ke `miannajiyah.site` pada 2026-06-14 dan status akhir dipertahankan `open` untuk tahun ajaran 2026/2027.~~
  - ~~Backup rollback production terverifikasi di `/home/miak7156/backups/ppdb-open-close-before-20260614-073257`.~~
  - ~~QA production membuktikan halaman tertutup tidak merender form, guard submit mengembalikan HTTP 403 `ppdb_closed`, status berhasil dipulihkan terbuka, serta homepage/form/login admin bebas broken image, overflow, warning, dan console error pada desktop/mobile.~~
  - ~~Seluruh route utama kedua domain tetap HTTPS 200 dan probe dokumen private tetap 404; aplikasi SIA lama tidak diubah.~~

- `[x]` ~~Deployment production bertahap ke Rumahweb `miannajiyah.site`.~~
  - Tracker deployment per fase: `docs/PRODUCTION_DEPLOYMENT_TRACKER_2026-06-08.md`.
  - Checkpoint Phase 2 selesai pada 2026-06-08:
    - Database `miak7156_spmb` dan user `miak7156_spmbusr` berhasil dibuat.
    - User baru memiliki seluruh privilege hanya pada database baru; koneksi dan operasi temporary table berhasil.
    - `SHOW DATABASES` untuk user baru tidak menampilkan database SIA lama.
    - Database/user `miak7156_sia_mi_annajiyah` tetap ada dan tidak diubah.
    - Kedua domain tetap merespons HTTPS 200 dan halaman SIA lama tetap berjalan.
    - Dump aplikasi belum diimpor dan belum ada file yang diunggah.
  - Checkpoint Phase 3 selesai pada 2026-06-08:
    - Artifact aplikasi, dump database, dan private storage diunggah ke `~/deploy/incoming/phase3-20260608`.
    - Checksum remote cocok dengan artifact lokal.
    - Aplikasi diekstrak ke `~/miannajiyah-app`, di luar `public_html`.
    - Private storage diekstrak ke `~/miannajiyah-app/storage/app/private` dengan 64 file.
    - `public/build` tersedia di staging; `.env` dan `vendor` belum ada.
    - Tidak ada file sensitif `akte`, `kk`, `ktp`, atau `ijazah` di `~/miannajiyah-app/public`.
    - Database `miak7156_spmb` masih kosong dari tabel aplikasi.
    - Folder dan `index.php` `public_html/siamiannajiyah.my.id` tetap ada; kedua domain tetap HTTPS 200.
  - Checkpoint Phase 4 selesai pada 2026-06-08:
    - Runtime domain utama disesuaikan ke `ea-php83` karena extension Laravel lengkap; `siamiannajiyah.my.id` tetap `ea-php74`.
    - `.env` production dibuat di staging dengan permission `600`, `APP_DEBUG=false`, `APP_URL=https://miannajiyah.site`, dan database `miak7156_spmb`.
    - Composer production selesai; karena `proc_open` hosting nonaktif, install dijalankan dengan `--no-scripts` dan `package:discover` manual berhasil.
    - Dump database berhasil diimpor setelah sanitasi stream untuk collation MariaDB dan penghapusan `DEFINER`.
    - `php artisan migrate --force` berhasil; 12 migration berstatus `Ran`.
    - Data production terverifikasi: 1 user, 15 siswa, 10 guru, 192 jadwal, dan 10 konten web.
    - Session/cache/jobs dan `active_session_id` lokal dibersihkan.
    - Command media/dokumen berhasil tanpa failure, dan `config:cache`, `route:cache`, `view:cache` berhasil.
    - Kedua domain tetap HTTPS 200 dan halaman SIA lama tetap berjalan.
  - Checkpoint Phase 5 selesai pada 2026-06-08:
    - Backup tepat sebelum aktivasi dibuat: `~/backups/public_html-before-phase5-20260608-205107.tar.gz`.
    - File publik Laravel disalin ke `~/public_html`, sementara aplikasi utama tetap di `~/miannajiyah-app`.
    - `index.php` root sudah menunjuk ke app luar web root.
    - `.htaccess` root mempertahankan handler `ea-php83`, rewrite Laravel, dan redirect HTTP ke HTTPS.
    - Permission induk `public_html` sempat membuat 404 karena mode `700`, lalu dikoreksi ke `750` sehingga Laravel dan SIA lama kembali terbaca web server.
    - Smoke test HTTP 200 lulus untuk `/`, `/pendaftaran`, `/cek-pendaftaran`, `/tenaga-pendidik`, `/fasilitas`, `/kegiatan`, dan `/sitemap.xml`.
    - Semua asset build di `public_html/build` merespons HTTP 200.
    - Probe dokumen private publik merespons 404 dan tidak ada file sensitif `akte`, `kk`, `ktp`, atau `ijazah` pada root publik utama.
    - Browser smoke test mengenali website Laravel baru dan halaman login SIA lama.
  - Checkpoint Phase 6 selesai pada 2026-06-08:
    - Browser QA desktop/mobile lulus pada seluruh halaman publik utama tanpa broken image, overflow, warning, atau error console.
    - Validasi formulir PPDB, cek status, kartu pendaftaran existing, login admin/operator, halaman admin, export XLSX, serta dokumen private telah diuji.
    - Operator dapat memakai modul operasional tetapi ditolak HTTP 403 dari manajemen user khusus admin.
    - Route admin dan dokumen private tanpa login mengarah ke login; tidak ada file sensitif pada web root.
    - HTTPS redirect, SSL Let's Encrypt, security headers, PHP `ea-php83` domain utama, dan PHP `ea-php74` domain lama terverifikasi.
    - Laravel tidak memiliki scheduled task, sehingga cron tidak perlu dibuat.
    - Akun dan kredensial QA sementara sudah dihapus; data kembali ke 1 user, 15 siswa, dan 7 activity log tanpa mengubah session admin asli.
    - Kedua domain tetap HTTPS 200 dan SIA lama tetap berjalan.
  - Checkpoint Phase 7 selesai pada 2026-06-09:
    - Backup final tersimpan di `~/backups/miannajiyah-final-20260609-001904`.
    - Arsip database, aplikasi, dan file publik lulus uji integritas serta checksum SHA-256.
    - Arsip publik mengecualikan folder `siamiannajiyah.my.id`.
    - Permission production aman, tidak ada file world-writable, dan direktori runtime Laravel tetap writable.
    - Seluruh migration dan cache production terverifikasi.
    - Runbook maintenance dan rollback dibuat di `docs/PRODUCTION_OPERATIONS_2026-06-09.md`.
    - Artifact deployment dan secret sementara lokal sudah dihapus setelah backup final terverifikasi.
    - Smoke test final kedua domain lulus HTTPS 200.
  - Checkpoint tahap 1 pada 2026-06-08:
    - Hosting Unlimited M aktif, domain dan nameserver terkonfigurasi, SSL gratis aktif.
    - PHP global akun dipertahankan pada native 7.4. MultiPHP awalnya mengunci `miannajiyah.site` ke PHP 8.3 (`alt-php83`) dan `siamiannajiyah.my.id` secara eksplisit ke PHP 7.4 (`ea-php74`).
    - Pemisahan domain diverifikasi dari handler `.htaccess` dan respons HTTPS: kedua domain HTTP 200, serta halaman login SIA lama tetap tampil. Folder, database, dan data `siamiannajiyah.my.id` tidak digunakan untuk deployment baru.
    - SSH key khusus deployment berhasil diotorisasi dan koneksi port `2223` terverifikasi.
    - Backup awal hosting dibuat di server: `~/backups/public_html-before-laravel-20260608-194123.tar.gz`.
    - Paket aplikasi production dibuat tanpa `.env`, database lokal, cache/log, dan dokumen private.
    - Dump `spmb_annajiyah` dibuat dan berhasil diuji restore: 12 migration, 15 siswa, 10 guru, dan 192 jadwal.
    - Dokumen PPDB private dan thumbnail dikemas terpisah dari web root.
  - ~~Seluruh Phase 1 sampai Phase 7 selesai. Deployment production dinyatakan selesai setelah QA, cleanup, backup final, dan dokumentasi serah-terima lulus.~~

- `[x]` ~~Audit final komprehensif dan remediation lokal sebelum hosting.~~
  - Tracker rinci: `docs/COMPREHENSIVE_DEPLOYMENT_AUDIT_PLAN_2026-06-08.md`.
  - Laporan audit: `docs/FINAL_PRE_HOSTING_AUDIT_2026-06-08.md`.
  - ~~Status saat ini: seluruh temuan P0/P1 kode sudah diremediasi dan regression check selesai.~~
  - Scope aktif hanya pengecekan dan perbaikan lokal. Belum melakukan staging, deployment, DNS, atau pemindahan file ke hosting.
  - Baseline 2026-06-08:
    - `composer validate --strict`, `composer audit`, `npm audit --audit-level=moderate`, dan `npm run build` berhasil.
    - `php artisan test`: 42 passed, 1 failed karena PHP CLI belum memuat extension `zip`/class `ZipArchive`.
    - Pemeriksaan awal `php artisan migrate:status` sempat tertahan karena MySQL lokal belum berjalan; setelah MySQL dijalankan, seluruh migration terverifikasi `Ran`.
    - Route terdeteksi: 49 route non-vendor.
  - Pembaruan setelah audit:
    - MySQL lokal berhasil dijalankan; seluruh migration aktual berstatus `Ran`.
    - Fresh migration dan rollback berhasil pada database MySQL sementara.
    - Backup dan restore database berhasil diverifikasi.
    - Integritas database, media publik, dan dokumen private aktual bersih.
    - Browser QA publik/admin lulus untuk render, console error, broken image, dan overflow desktop/mobile.
  - ~~Seeder demo dibuat opt-in, pendaftaran dibuat transaksional dengan cleanup file dan lock nomor, bentrok jadwal diperbaiki, replacement media diamankan, timezone/header/logging diperkeras, serta konfigurasi proxy dibuat eksplisit.~~
  - ~~Full regression: 49 test lulus dengan 239 assertion; build, Pint, audit dependency, cache command, fresh migration, rollback, dan browser QA lulus.~~
  - ~~Gate A dan Gate B lulus. Gate C lokal lulus secara teknis; eksekusi hosting tetap menunggu provider/domain dan konfigurasi production final.~~

- `[x]` ~~Hapus kolom Dokumen dari tabel Kelola PPDB.~~
  - ~~Dokumen tetap dapat dilihat dari modal detail calon pendaftar.~~
  - ~~Browser QA memastikan tabel hanya memiliki delapan kolom dan modal detail tetap memiliki empat link dokumen.~~

- `[x]` ~~Terapkan validasi menyeluruh pada input pendaftaran PPDB dan data siswa admin.~~
  - ~~Aturan reusable diterapkan melalui Form Request untuk nama, tempat/tanggal lahir, sekolah asal, NISN, NIS, nomor akte, nomor KK, alamat, WhatsApp, kelas, dan dokumen.~~
  - ~~Nama siswa/orang tua hanya menerima huruf Unicode dan tanda baca nama yang wajar; NISN wajib tepat 10 digit dan nomor KK tepat 16 digit.~~
  - ~~Spasi berlebih dinormalisasi tanpa mengubah format nomor WhatsApp lokal `08...`; validasi backend tetap menjadi sumber kebenaran.~~
  - ~~Form publik memvalidasi setiap langkah, menampilkan pesan per field, memindahkan fokus ke input bermasalah, dan menangani error 422 dari server.~~
  - ~~Feature test mencakup input valid, format tidak valid, duplikasi identitas, file salah/terlalu besar, normalisasi, dan validasi form admin.~~
  - ~~QA browser publik desktop/mobile memastikan alur tiga langkah, pesan error, dan layout tanpa overflow; error JavaScript notifikasi publik yang ditemukan saat QA sudah diperbaiki.~~

## Arsip Selesai

- `[x]` ~~Audit dan perbaikan menyeluruh halaman publik.~~
  - ~~Tracker: `docs/PUBLIC_PAGE_QA_TRACKER_2026-06-09.md`.~~
  - ~~Pagination Kegiatan diganti dengan komponen publik berbahasa Indonesia yang menampilkan nomor halaman, halaman aktif, rentang data, tombol sebelumnya/berikutnya, filter kategori, dan anchor galeri.~~
  - ~~Kartu kegiatan/guru mendukung keyboard dan indikator fokus; modal memiliki semantik dialog serta tombol Escape.~~
  - ~~Modal guru homepage yang sebelumnya tidak terhubung sekarang berfungsi.~~
  - ~~Verifikasi lokal lulus: 68 test/407 assertion, Pint, build, route/view cache, diff check, dan browser QA desktop/tablet/mobile.~~
  - ~~Backup production dibuat di `/home/miak7156/backups/public-page-patch-before-20260609-084410`; patch berhasil dipasang.~~
  - ~~Browser QA production seluruh halaman publik lulus tanpa broken image, overflow, raw pagination key, control tanpa label, warning, atau console error.~~
  - ~~Seluruh route publik utama, sitemap, `miannajiyah.site`, dan `siamiannajiyah.my.id` tetap HTTPS 200; domain lama tidak diubah.~~

- `[x]` ~~Audit dan perbaikan menyeluruh seluruh fungsi admin.~~
  - ~~Tracker: `docs/ADMIN_FUNCTION_AUDIT_TRACKER_2026-06-09.md`.~~
  - ~~Bug simpan Kontak diperbaiki dengan validasi yang terlihat, old input, transaksi database, invalidasi cache, dan redirect kembali ke tab Kontak.~~
  - ~~Data TikTok dipisahkan dari field WhatsApp melalui migration preservatif.~~
  - ~~Fungsi edit kegiatan dan kategori ditambahkan; regression test edit seluruh modul admin diperluas.~~
  - ~~Verifikasi lokal lulus: 65 test/377 assertion, Pint, build, cache command, dan browser QA desktop/mobile.~~
  - ~~Backup production dibuat di `/home/miak7156/backups/admin-contact-patch-before-20260609-065418`; patch dan migration berhasil dipasang.~~
  - ~~Browser QA production seluruh halaman admin lulus tanpa broken image, overflow, warning, atau console error.~~
  - ~~Akun/data QA lokal dan production sudah dibersihkan; baseline akhir production tetap 1 admin, 0 siswa, 0 jadwal, 10 guru, 6 fasilitas, 4 kategori, 32 kegiatan, 2 banner, dan 0 activity log.~~
  - ~~`miannajiyah.site` dan `siamiannajiyah.my.id` tetap HTTPS 200; domain lama tidak diubah.~~

- `[x]` ~~Bersihkan data lokal dan production agar hanya menyisakan baseline publik.~~
  - ~~Baseline mempertahankan 1 admin, 2 banner, 10 guru, 6 fasilitas, 4 kategori, 32 kegiatan/galeri, 10 konten web, dan seluruh media publik terkait.~~
  - ~~Sebanyak 15 siswa/pendaftar, 192 jadwal, 7 activity log, session/cache/queue, 29 dokumen PPDB, dan 34 thumbnail dokumen dihapus dari lokal dan production.~~
  - ~~Backup sebelum dan setelah cleanup dibuat serta diverifikasi dengan checksum.~~
  - ~~Production memiliki 0 siswa, 0 jadwal, 0 dokumen private, dan sequence operasional mulai dari 1.~~
  - ~~Lokal memiliki 0 record operasional; metadata counter MySQL lokal masih menyimpan nilai historis walaupun tabel kosong.~~
  - ~~Seluruh 50 referensi media publik tersedia, test suite lulus 54 test/299 assertion, dan browser QA production lulus tanpa broken image, overflow, warning, atau error console.~~
  - ~~Akun operator `operator/operator123` tidak dibuat karena belum diperlukan dan password tersebut terlalu mudah ditebak untuk production.~~
  - ~~Definisi baseline dan strategi satu sumber proyek didokumentasikan di `docs/DATA_BASELINE_POLICY.md`.~~

- `[x]` ~~Buat lampiran teknis untuk manual book dalam format Word.~~
  - ~~Dokumen `Lampiran_Manual_Book_SPMB_MI_Annajiyah.docx` memuat potongan kode sumber kunci, panduan instalasi dependency, dan glosarium.~~
  - ~~Format dokumen menggunakan kertas A4, margin akademik, Times New Roman, spasi 1,5, paragraf justify, tabel, dan nomor halaman.~~

- `[x]` ~~Perbaiki format export Excel data PPDB.~~
  - ~~Export admin sekarang memakai Laravel Excel `maatwebsite/excel` dan PhpSpreadsheet, bukan HTML table dengan ekstensi `.xls` atau generator XLSX manual.~~
  - ~~PHP extension `zip` diaktifkan pada PHP Laragon lokal agar pembuatan `.xlsx` kompatibel dengan Excel.~~
  - ~~Nilai identitas seperti nomor pendaftaran, NISN, NIS, dan WhatsApp dipaksa sebagai string agar tidak berubah menjadi angka atau kehilangan angka awal.~~
  - ~~Export PPDB membawa filter pencarian `q`, filter tanggal, status, tahun ajaran, dan kelas sesuai tampilan admin.~~
  - ~~Test ditambahkan di `tests/Feature/AdminExportTest.php`.~~

- `[x]` ~~Optimasi performa lanjutan media dan dokumen PPDB admin.~~
  - ~~Preview dokumen pada admin PPDB sekarang memakai thumbnail private melalui route `admin.ppdb.document.thumbnail`, bukan file asli.~~
  - ~~Thumbnail dokumen PPDB image disimpan di `storage/app/private/ppdb-thumbs`.~~
  - ~~Command `php artisan ppdb:migrate-public-documents` ditambahkan dan sudah dijalankan pada database lokal laptop; tidak ada lagi referensi dokumen PPDB ke `public/uploads`.~~
  - ~~Command `php artisan ppdb:generate-document-thumbnails` ditambahkan dan sudah menghasilkan thumbnail dokumen PPDB lokal.~~
  - ~~Logo kecil web `public/logo-web.webp` ditambahkan untuk navigasi, footer, login, dan fallback modal tanpa mengubah tampilan.~~
  - ~~Varian media publik dicek ulang dengan `php artisan media:generate-variants`; satu varian card guru yang kurang sudah dibuat.~~
  - ~~Dokumentasi diperbarui di `docs/PERFORMANCE_MEDIA_PRESERVATION_PLAN.md`, `docs/PROJECT_STRUCTURE.md`, `docs/DEVELOPMENT_GUIDELINES.md`, dan `docs/HOSTING_READINESS.md`.~~

- `[x]` ~~Keputusan laporan Word tetap lokal.~~
  - ~~`Kel 4_LaporanAkhirRPL.docx` tidak perlu dipush ke repository.~~
  - ~~File sudah dimasukkan ke `.gitignore` agar tidak ikut stage/commit tanpa sengaja.~~

- `[x]` ~~Tindak lanjuti temuan audit menyeluruh 2026-06-01.~~
  - ~~Dokumen rencana: `docs/AUDIT_FIX_PLAN_2026-06-01.md`.~~
  - ~~Tahap 0: baseline kerja dirapikan dan `.gitignore` melindungi file lock Word.~~
  - ~~Tahap 1: hardening admin/operator selesai, termasuk password minimal 8 karakter, konfirmasi password, proteksi akun sendiri, proteksi admin terakhir, dan reset session saat password diganti.~~
  - ~~Tahap 2: validasi input admin untuk konten, kontak, banner, dan data siswa dilengkapi.~~
  - ~~Tahap 3: cleanup media `thumb/card/hero` dipusatkan di `ImageHelper::deleteImageSet`.~~
  - ~~Tahap 4: dashboard admin tidak lagi memakai Chart.js CDN, placeholder gambar valid, dan foto jadwal memakai thumbnail lazy-load.~~
  - ~~Tahap 5: overflow horizontal halaman publik/admin diproteksi dan dicek lewat browser.~~
  - ~~Tahap 6: dokumen stack disinkronkan ke Laravel 12, PHP `^8.2`, Vite 6, Tailwind 4.~~
  - ~~Tahap 7: test, build, audit dependency, cache command, browser check, dan media/database check selesai.~~

- `[x]` ~~Audit menyeluruh proyek 2026-06-01.~~
  - ~~Laporan dibuat di `docs/FULL_PROJECT_AUDIT_2026-06-01.md`.~~
  - ~~Audit mencakup baseline Git, dependency, route/auth, database/media, upload, frontend, performa dasar, testing, cache, dan dokumentasi.~~
  - ~~Temuan sudah diprioritaskan menjadi High, Medium, dan Low.~~

- `[x]` ~~Lengkapi laporan akhir RPL BAB IV.~~
  - ~~File `Kel 4_LaporanAkhirRPL.docx` diperbarui pada bagian 4.1.2 Tampilan Antarmuka Sistem, 4.2.1 Black Box Testing, 4.3 Hasil dan Pembahasan, serta 4.4 Kendala dan Solusi.~~
  - ~~Screenshot aktual sistem dimasukkan ke bagian tampilan antarmuka.~~
  - ~~Tabel Black Box Testing diperbaiki dan dilengkapi berdasarkan modul aplikasi yang berjalan.~~

- `[x]` ~~Perbaiki tampilan lokal saat `APP_URL` memakai domain online HTTPS.~~
  - ~~Guard `URL::forceScheme('https')` dibuat hanya aktif pada host publik yang sesuai dengan `APP_URL`.~~
  - ~~Preview lokal `http://127.0.0.1:8000` kembali memuat asset CSS/JS dengan benar, sementara domain online tetap memakai HTTPS.~~

- `[x]` ~~Fitur session admin satu akun satu perangkat.~~
  - ~~Login baru menyimpan session id aktif ke akun user.~~
  - ~~Session lama otomatis diarahkan kembali ke login jika akun yang sama login di perangkat/browser lain.~~
  - ~~Logout hanya menghapus session aktif jika cocok dengan session yang sedang logout.~~
  - ~~Test auth/session ditambahkan: `tests/Feature/AdminSingleSessionTest.php`.~~

- `[x]` ~~Perbaiki ikon dan tampilkan Instagram & TikTok di footer kontak.~~
  - ~~Menggunakan SVG path FontAwesome Brand yang bersih dan benar.~~
  - ~~Menambahkan link Instagram dan TikTok pada list kontak footer halaman publik.~~

- `[x]` ~~Optimasi media publik dan preservasi data baseline.~~
  - ~~Commit/push: `0df395e Optimize public media and preserve baseline content`.~~
  - ~~Asset `*_card.webp` dan `*_hero.webp` yang dipakai web sudah ikut dipush.~~

- `[x]` ~~Nomor WhatsApp pendaftaran disimpan sesuai input lokal.~~
  - ~~Contoh: `081250800137` tetap tersimpan `081250800137`, tidak diubah menjadi `6281250800137`.~~
  - ~~Link WhatsApp admin tetap dikonversi sementara untuk `wa.me`.~~
  - ~~Commit/push: `a552499 Preserve local WhatsApp number format`.~~

## Tertunda Sampai Hosting

- `[!]` Pilih domain dan paket hosting final.
- `[!]` Set konfigurasi production final: `APP_ENV=production`, `APP_DEBUG=false`, database, mail, HTTPS cookie.
- `[!]` Jalankan deployment checklist dari `docs/HOSTING_READINESS.md`.
- `[!]` Full verification sebelum deployment production.

## Verifikasi Terakhir

- Test terfokus fitur buka/tutup PPDB pada 2026-06-14: 22 passed, 163 assertions (`PpdbRegistrationTest`, `AdminRoleAccessTest`, dan `PublicPageRegressionTest`).
- `vendor/bin/pint --test` untuk file PHP terkait fitur buka/tutup PPDB: lulus pada 2026-06-14.
- Full regression fitur buka/tutup PPDB pada 2026-06-14: `php artisan test` lulus 72 test/429 assertion; `npm run build`, route/view cache, dan `git diff --check` lulus.
- Browser QA lokal memakai database SQLite sementara: status dapat ditutup dari admin, form publik hilang, pesan penutupan dan CTA homepage sinkron, serta halaman publik/admin bebas overflow pada desktop dan viewport 375 px. Console error Alpine yang ditemukan saat QA sudah diperbaiki dan ikon status publik sudah masuk subset Font Awesome.
- Deployment production fitur buka/tutup PPDB pada 2026-06-14: backup app/public/database/env lulus checksum, 19 file patch lulus checksum/lint, migration tidak memiliki pending item, route cache memuat middleware `ppdb.open`, dan status akhir production `open`.
- Smoke test production 2026-06-14: `/`, `/pendaftaran`, `/cek-pendaftaran`, `/admin/login`, `/sitemap.xml`, dan `siamiannajiyah.my.id` HTTPS 200; direct probe dokumen private 404.
- `php artisan test`: 68 passed, 407 assertions pada 2026-06-09 setelah audit dan perbaikan halaman publik.
- Test publik terfokus: 3 passed, 30 assertions.
- Pint, `npm run build`, route/view cache, dan `git diff --check`: lulus.
- Browser QA lokal dan production pada desktop/tablet/mobile: seluruh halaman publik tanpa broken image, overflow, raw pagination key, control tanpa label, warning, atau console error.
- Pagination Kegiatan, filter kategori, anchor galeri, keyboard modal, slider homepage, menu mobile, validasi pendaftaran, cek status tidak ditemukan, dan sitemap terverifikasi.
- `php artisan test`: 65 passed, 377 assertions pada 2026-06-09 setelah perbaikan dan audit fungsi admin.
- `vendor/bin/pint --test`, `npm run build`, `git diff --check`, route cache, dan view cache: lulus pada 2026-06-09.
- Browser QA lokal dan production seluruh halaman admin pada desktop/mobile: tidak ada broken image, overflow, warning, atau console error.
- Simpan kontak valid/invalid, old input, cache publik, footer WhatsApp/TikTok/jam operasional, serta pemulihan baseline diuji langsung melalui browser production.
- Migration pemisahan WhatsApp/TikTok production berstatus `Ran`; kedua domain tetap HTTPS 200.
- `php artisan test`: 54 passed, 299 assertions pada 2026-06-08 setelah implementasi validasi input PPDB dan siswa admin.
- `vendor/bin/pint --test`, `npm run build`, `php artisan view:cache`, `php artisan view:clear`, dan `git diff --check`: lulus pada 2026-06-08 setelah implementasi validasi input.
- Browser QA `/pendaftaran`: validasi nama, data wali, KK, dan dokumen wajib lulus pada desktop dan viewport 375 px tanpa overflow atau error console baru.
- `php artisan test`: 49 passed, 239 assertions pada 2026-06-08.
- `vendor/bin/pint --test`: lulus pada 2026-06-08.
- `npm run build`: lulus pada 2026-06-08 setelah Alpine diperbarui ke patch 3.15.12.
- `composer audit` dan `npm audit --omit=dev`: tidak ada vulnerability pada 2026-06-08.
- `php artisan route:cache`, `route:clear`, `view:cache`, dan `view:clear`: lulus.
- Fresh migration dan rollback seluruh migration pada SQLite temporary: lulus.
- PHP CLI Laragon memuat extension `zip`; export XLSX test lulus.
- Browser QA publik/admin desktop dan viewport 375 px: tidak ada overflow, broken image, control tanpa accessible name, atau console warning/error baru.
- Header CSP, nosniff, frame policy, referrer policy, dan permissions policy aktif; HSTS hanya aktif pada production HTTPS.
- `php artisan test --filter=AdminExportTest`: 1 passed, 16 assertions pada 2026-06-01 setelah migrasi export Excel PPDB ke Laravel Excel.
- Export nyata dari `http://localhost:8000/admin/export/ppdb`: berhasil menghasilkan `ppdb_export_*.xlsx` dengan content type `application/vnd.openxmlformats-officedocument.spreadsheetml.sheet`, byte awal `PK`, sheet `Data PPDB`, freeze pane `A7`, filter `A6:R13`, dan sampel WhatsApp terbaca sebagai string.
- `php artisan test`: 43 passed, 206 assertions pada 2026-06-01 setelah migrasi export Excel PPDB ke Laravel Excel.
- `npm.cmd run build`: berhasil pada 2026-06-01 setelah optimasi dokumen PPDB dan media ringan.
- `git diff --check`: bersih pada 2026-06-01.
- `composer validate --strict`: valid pada 2026-06-01 setelah penambahan Laravel Excel.
- `composer audit`: tidak ada vulnerability advisory pada 2026-06-01.
- `npm audit --audit-level=moderate`: 0 vulnerability pada 2026-06-01.
- `php artisan route:cache` dan `php artisan view:cache`: berhasil pada 2026-06-01 setelah perbaikan export Excel PPDB; cache kemudian dibersihkan kembali.
- Browser check lokal `http://localhost:8000`: `/`, `/fasilitas`, `/kegiatan`, `/tenaga-pendidik`, `/pendaftaran`, `/cek-pendaftaran`, dan `/admin/ppdb` memiliki isi, tidak ada visible broken image, tidak ada console error, halaman publik tidak merujuk JPG/PNG original besar, dan admin PPDB memakai URL thumbnail untuk preview dokumen.
- Cek media/database: tidak ada path gambar database yang hilang dari `public/uploads`.
- Cek dokumen PPDB/database: tidak ada referensi dokumen PPDB yang masih menunjuk `public/uploads`, dan tidak ada referensi dokumen PPDB yang missing.
- `php artisan migrate`: berhasil pada 2026-06-01, migration `2026_06_01_000000_add_active_session_id_to_users` sudah masuk ke database lokal.
- Baseline audit, hardening, perbaikan UI, dan fitur buka/tutup PPDB siap di-commit dan dipush ke `origin/main` pada 2026-06-14.
- Paket source untuk pengumpulan akademik/HKI disiapkan tanpa `.env`, database, dokumen PPDB privat, dependency, cache, log, dan konfigurasi tunnel lokal.
