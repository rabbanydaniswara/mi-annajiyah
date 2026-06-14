# Rencana Audit Final Komprehensif Sebelum Hosting

Tanggal dibuat: 2026-06-08
Status: audit dan remediation lokal selesai; menunggu provider/domain untuk Gate D
Target aktif: memeriksa aplikasi lokal secara menyeluruh dan menghasilkan keputusan kesiapan sebelum file proyek dipindahkan ke layanan hosting.

Deployment, staging, perubahan DNS, dan pemindahan file ke hosting belum termasuk pekerjaan aktif. Fase tersebut tetap dicatat sebagai checklist lanjutan dan baru dijalankan setelah audit lokal selesai serta user memberi instruksi.

## Tujuan

Audit ini memeriksa proyek secara menyeluruh:

- integritas repository dan dependency;
- route, controller, model, helper, middleware, migration, dan seeder;
- autentikasi, otorisasi, session, privasi, upload, dan dokumen PPDB;
- fungsi setiap modul publik, admin, operator, export, dan cetak;
- Blade, CSS, JavaScript, responsivitas, aksesibilitas, dan kompatibilitas browser;
- database, media, backup, restore, performa, logging, dan operasional;
- konfigurasi hosting, staging, deployment production, rollback, dan pemeriksaan pascadeploy.

Sebuah task hanya boleh ditandai selesai jika bukti pada kolom verifikasi sudah dijalankan dan hasilnya dicatat.

## Status Legend

- `[ ]` belum dikerjakan
- `[~]` sedang dikerjakan
- `[x]` selesai dan sudah diverifikasi
- `[!]` tertahan atau gagal, wajib memiliki catatan blocker

Prioritas:

- `P0`: blocker deployment atau risiko data/keamanan tinggi
- `P1`: wajib selesai sebelum production
- `P2`: perbaikan penting yang dapat dijadwalkan setelah blocker
- `P3`: peningkatan lanjutan

## Baseline 2026-06-08

Hasil pemeriksaan awal:

- `[x]` `composer validate --strict` berhasil.
- `[x]` `composer audit` tidak menemukan advisory.
- `[x]` `npm audit --audit-level=moderate` menemukan 0 vulnerability.
- `[x]` `npm run build` berhasil.
- `[!]` `php artisan test` menghasilkan 42 passed dan 1 failed.
  - Blocker: `AdminExportTest` gagal karena class PHP `ZipArchive` tidak tersedia.
  - PHP CLI memakai `C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.ini`.
- `[!]` `php artisan migrate:status` gagal.
  - Blocker: MySQL lokal `127.0.0.1:3306` tidak menerima koneksi.
- `[x]` `php artisan route:list --except-vendor` berhasil dan mencatat 49 route.
- `[!]` Worktree berisi banyak perubahan aktif dan file untracked.
  - Audit tidak boleh menghapus, mengembalikan, commit, atau push perubahan tersebut tanpa izin user.
- `[!]` Dokumentasi lama menyebut extension `zip` sudah aktif, tetapi runtime PHP CLI saat audit belum memuat extension tersebut.

## Hasil Remediation 2026-06-08

- `[x]` Gate A lulus: runtime, ZIP, migration, full test, build, formatter, dan dependency audit bersih.
- `[x]` Gate B lulus: fungsi kritis backend/frontend diuji otomatis dan melalui browser.
- `[x]` Gate C lokal lulus: seluruh temuan P0/P1 kode ditutup dan backup/restore telah terbukti.
- `[!]` Gate D belum aktif karena paket hosting, domain, credential production, SSL, dan document root final belum tersedia.
- `[x]` `php artisan test`: 49 passed, 239 assertions.
- `[x]` `vendor/bin/pint --test`, `npm run build`, `composer audit`, dan `npm audit --omit=dev` lulus.
- `[x]` Browser QA desktop/mobile mencakup halaman publik utama dan seluruh modul admin utama tanpa console error, broken image, overflow, atau control tanpa accessible name.
- `[!]` Worktree tetap belum di-commit karena aturan proyek melarang commit/push tanpa izin user. Ini adalah kontrol rilis, bukan defect runtime.

## Gerbang Kesiapan

### Gate A - Baseline Stabil

Lulus jika repository dapat diidentifikasi dengan jelas, dependency terpasang konsisten, seluruh test hijau, build berhasil, dan database lokal dapat diperiksa.

### Gate B - Application Verified

Lulus jika seluruh modul backend dan frontend telah melewati test otomatis serta QA manual berbasis matriks fitur dan role.

### Gate C - Pre-Hosting Ready

Lulus jika backup/restore lokal terbukti, requirement production terdokumentasi, seluruh temuan P0/P1 ditutup, dan laporan final menyatakan proyek layak dipindahkan ke hosting.

### Gate D - Hosting Execution

Belum aktif. Baru dimulai setelah hosting dipilih dan user meminta proses deployment.

## Fase 0 - Freeze Baseline dan Tracking

Tujuan: memastikan audit berjalan pada kondisi yang dapat dilacak.

- `[x]` `AUD-000` Catat status Git, branch, commit terakhir, dan seluruh perubahan aktif. `P0`
  - Verifikasi: `git status --short`, `git diff --stat`, `git diff --check`.
- `[ ]` `AUD-001` Kelompokkan perubahan aktif berdasarkan fitur, dokumen, media, test, dan file sementara tanpa mengubah isinya. `P0`
  - Verifikasi: daftar kelompok file disetujui user.
- `[ ]` `AUD-002` Putuskan baseline audit: worktree aktif saat ini atau commit/branch khusus setelah mendapat izin. `P0`
  - Verifikasi: commit hash atau catatan eksplisit baseline.
- `[x]` `AUD-003` Pastikan file sensitif dan hasil runtime tidak tracked. `P0`
  - Cakupan: `.env`, database lokal, `storage`, dokumen PPDB private, `vendor`, `node_modules`, `public/build`, screenshot QA.
  - Verifikasi: `git ls-files` dan pemeriksaan `.gitignore`.
- `[x]` `AUD-004` Buat format log temuan yang memuat ID, severity, bukti, dampak, rekomendasi, status, dan hasil retest. `P1`
  - Laporan: `docs/FINAL_PRE_HOSTING_AUDIT_2026-06-08.md`.

Exit criteria: baseline audit disepakati dan perubahan user terlindungi.

## Fase 1 - Runtime, Dependency, dan Reproducibility

- `[x]` `AUD-100` Aktifkan/verifikasi PHP extension `zip` pada PHP CLI yang benar. `P0`
  - Verifikasi: `php -m`, `php --ini`, dan `php artisan test --filter=AdminExportTest`.
- `[x]` `AUD-101` Hidupkan/verifikasi MySQL lokal dan koneksi `.env` tanpa membocorkan credential. `P0`
  - Verifikasi: `php artisan migrate:status` dan query read-only sederhana.
- `[x]` `AUD-102` Catat versi PHP, Composer, Laravel, MySQL/MariaDB, Node, npm, serta extension wajib. `P1`
- `[x]` `AUD-103` Validasi konsistensi `composer.json`/`composer.lock` dan `package.json`/`package-lock.json`. `P1`
- `[ ]` `AUD-104` Uji instalasi bersih atau simulasi CI dengan lock file. `P1`
  - Verifikasi: `composer install` dan `npm ci` pada workspace/temp environment yang aman.
- `[x]` `AUD-105` Jalankan audit dependency dan evaluasi package deprecated/outdated secara terkontrol. `P1`
- `[ ]` `AUD-106` Pastikan requirement server terdokumentasi dan cocok dengan paket hosting final. `P0`

Exit criteria: environment dapat direproduksi dan semua dependency/runtime wajib tersedia.

## Fase 2 - Review Arsitektur dan Kualitas Kode

- `[x]` `AUD-200` Review seluruh route, HTTP method, route name, parameter binding, middleware, dan throttle. `P0`
- `[x]` `AUD-201` Review seluruh controller publik dan admin untuk validasi, transaksi, error handling, query, dan response. `P0`
- `[x]` `AUD-202` Review model untuk fillable/cast, relasi, event, scope, token, dan constraint bisnis. `P1`
- `[x]` `AUD-203` Review helper media, dokumen, telepon, PPDB, cache publik, dan activity log. `P0`
- `[x]` `AUD-204` Review middleware role dan single-session termasuk kondisi race, user terhapus, dan session kedaluwarsa. `P0`
- `[x]` `AUD-205` Cari debug statement, dead code, query di view, raw HTML, unsafe DOM API, TODO/FIXME, dan exception yang bocor. `P1`
- `[x]` `AUD-206` Jalankan formatter/static checks yang tersedia dan PHP syntax check. `P1`
  - Verifikasi minimum: `vendor/bin/pint --test` dan lint seluruh file PHP aplikasi.
  - PHP syntax dan `vendor/bin/pint --test` lulus.
- `[ ]` `AUD-207` Catat area dengan kompleksitas/duplikasi yang berisiko, tanpa refactor di luar kebutuhan deployment. `P2`

Exit criteria: semua temuan kode memiliki severity, lokasi file, dampak, dan keputusan perbaikan.

## Fase 3 - Database, Migration, Seeder, dan Integritas Data

- `[x]` `AUD-300` Review seluruh migration secara urut untuk fresh install dan upgrade database existing. `P0`
- `[x]` `AUD-301` Jalankan `migrate:fresh --seed` hanya pada database testing/temporary. `P0`
- `[x]` `AUD-302` Verifikasi `migrate` terhadap salinan database existing dan cek rollback yang relevan. `P0`
- `[x]` `AUD-303` Review foreign key, unique constraint, enum/status, index, nullable/default, dan tipe data identitas. `P1`
- `[x]` `AUD-304` Audit seeder agar preservatif dan tidak membuat credential lemah di production. `P0`
  - Data siswa/jadwal demo hanya dibuat jika non-production dan `SEED_DEMO_DATA=true`.
- `[x]` `AUD-305` Cek orphan record, duplicate identifier, nomor pendaftaran, token kosong/duplikat, dan path file hilang. `P0`
- `[x]` `AUD-306` Uji backup database, backup media, backup dokumen private, dan proses restore. `P0`
  - Backup/restore database, media publik, dan dokumen private terbukti pada target temporary.
- `[ ]` `AUD-307` Tentukan retention dan lokasi backup di luar document root. `P1`

Exit criteria: migration aman untuk data existing dan restore telah dibuktikan, bukan hanya didokumentasikan.

## Fase 4 - Security dan Privasi

- `[ ]` `AUD-400` Audit autentikasi login/logout, session fixation, rate limit, password, dan lockout. `P0`
- `[ ]` `AUD-401` Audit otorisasi admin/operator pada setiap route dan aksi, bukan hanya visibilitas menu. `P0`
- `[ ]` `AUD-402` Audit CSRF, method destructive, mass assignment, IDOR, enumeration, dan token cetak. `P0`
- `[ ]` `AUD-403` Audit upload berdasarkan MIME, ekstensi, ukuran, nama file, image bomb, PDF, dan file berbahaya. `P0`
- `[ ]` `AUD-404` Pastikan dokumen PPDB private tidak dapat diakses langsung atau melalui path traversal. `P0`
- `[ ]` `AUD-405` Audit XSS, output Blade, data server ke JavaScript, open redirect, dan link tab baru. `P0`
- `[ ]` `AUD-406` Review proxy trust, HTTPS detection, secure cookie, SameSite, session encryption, dan domain cookie. `P0`
- `[ ]` `AUD-407` Review security headers dan kebijakan server: CSP yang realistis, HSTS setelah HTTPS stabil, nosniff, frame policy, dan referrer policy. `P1`
- `[ ]` `AUD-408` Pastikan `APP_DEBUG=false`, error generik untuk publik, dan log tidak menyimpan dokumen/credential/data sensitif. `P0`
- `[ ]` `AUD-409` Verifikasi document root hanya ke `public` dan file project tidak dapat diunduh. `P0`

Exit criteria: tidak ada temuan security P0/P1 yang terbuka sebelum production.

## Fase 5 - Audit Fungsional Backend

Setiap modul diuji untuk happy path, validasi gagal, data kosong, data duplikat, data besar, hak akses, serta kegagalan penyimpanan.

- `[ ]` `AUD-500` Pendaftaran PPDB tiga langkah, upload, nomor pendaftaran, token, dan kartu cetak. `P0`
- `[ ]` `AUD-501` Cek status pendaftaran, masking data, status workflow, dan throttle. `P0`
- `[ ]` `AUD-502` Login, logout, ganti password, dan satu akun satu session. `P0`
- `[ ]` `AUD-503` Dashboard dan seluruh statistik/filter ringkas. `P1`
- `[ ]` `AUD-504` Kelola PPDB: filter, detail, dokumen, thumbnail, status, bulk action, hapus, dan pagination. `P0`
- `[ ]` `AUD-505` Kelola konten umum, kontak, tahun ajaran, banner, kegiatan, dan kategori. `P1`
- `[ ]` `AUD-506` Kelola jadwal dan hasil print. `P1`
- `[ ]` `AUD-507` Kelola guru dan media terkait. `P1`
- `[ ]` `AUD-508` Kelola fasilitas, toggle, dan media terkait. `P1`
- `[ ]` `AUD-509` Kelola siswa, unique identifier, update, dan hapus. `P1`
- `[ ]` `AUD-510` Kelola admin/operator dan proteksi admin terakhir/current user. `P0`
- `[ ]` `AUD-511` Export PPDB/siswa/guru ke XLSX dan PDF dengan filter serta format identifier sebagai teks. `P0`
- `[ ]` `AUD-512` Activity log dan audit trail untuk aksi penting. `P1`
- `[ ]` `AUD-513` Sitemap, robots, health endpoint, halaman 404/419/500, dan maintenance mode. `P1`

Exit criteria: matriks fungsi/role lulus dan seluruh defect memiliki retest.

## Fase 6 - Frontend, UI, UX, dan Aksesibilitas

- `[x]` `AUD-600` QA desktop dan mobile untuk seluruh halaman publik. `P1`
- `[x]` `AUD-601` QA desktop dan mobile untuk seluruh halaman admin/operator. `P1`
- `[~]` `AUD-602` Uji breakpoint minimal 360, 390, 768, 1024, dan 1440 px. `P1`
  - Desktop dan viewport sekitar 375 px sudah diperiksa; breakpoint lain masuk regression setelah perbaikan.
- `[x]` `AUD-603` Periksa overflow, overlap, modal, dropdown, tabel, sticky header, toast, loading, dan empty state. `P1`
- `[x]` `AUD-604` Periksa form dengan keyboard, label, focus state, urutan tab, pesan error, dan submit ganda. `P1`
  - Label programatik dan accessible name diverifikasi melalui browser pada halaman publik/admin utama.
- `[ ]` `AUD-605` Periksa semantic heading, alt text, contrast, zoom 200%, serta reduced motion. `P2`
- `[x]` `AUD-606` Periksa broken link/image, favicon, metadata, Open Graph, canonical URL, dan sitemap absolute URL. `P1`
- `[ ]` `AUD-607` Uji print layout untuk kartu, jadwal, guru, siswa, dan PDF terkait. `P1`
- `[ ]` `AUD-608` Uji Chrome/Edge dan minimal satu engine/browser mobile yang relevan. `P2`
- `[x]` `AUD-609` Pastikan tidak ada console error, request gagal, mixed content, atau asset development pada build production. `P0`

Exit criteria: tidak ada defect UI P0/P1 dan alur utama dapat digunakan dengan keyboard serta mobile.

## Fase 7 - Test Otomatis dan Regression Coverage

- `[ ]` `AUD-700` Buat matriks route/modul terhadap test yang tersedia. `P1`
- `[ ]` `AUD-701` Tambahkan test untuk jalur kritis yang belum tercakup, terutama jadwal, export PDF, error upload, dan halaman error. `P1`
- `[ ]` `AUD-702` Tambahkan test otorisasi per role untuk setiap aksi destructive/sensitif. `P0`
- `[ ]` `AUD-703` Tambahkan test integritas file private dan cleanup media/dokumen. `P0`
- `[ ]` `AUD-704` Tambahkan test production configuration yang layak diotomasi. `P1`
- `[ ]` `AUD-705` Jalankan full suite berulang untuk mendeteksi flaky test. `P1`
- `[ ]` `AUD-706` Siapkan CI minimal untuk install, lint, test, build, dan dependency audit. `P1`

Exit criteria: full suite hijau, test kritis tidak flaky, dan CI dapat mengulang verifikasi utama.

## Fase 8 - Performa, Media, dan Kapasitas

- `[ ]` `AUD-800` Ukur ukuran asset build, request count, gambar terbesar, dan waktu respons halaman utama. `P1`
- `[ ]` `AUD-801` Audit query N+1, pagination, eager loading, aggregate dashboard, dan query filter/export. `P1`
- `[ ]` `AUD-802` Uji dataset lebih besar untuk PPDB, siswa, jadwal, log, dan export. `P1`
- `[ ]` `AUD-803` Verifikasi varian `thumb/card/hero`, lazy loading, dimensi gambar, dan cleanup file orphan. `P1`
- `[ ]` `AUD-804` Verifikasi cache publik, config/route/view cache, dan invalidasi setelah update konten. `P1`
- `[ ]` `AUD-805` Uji limit upload server dan Laravel agar konsisten. `P0`
- `[ ]` `AUD-806` Tentukan batas kapasitas storage database, upload publik, dokumen private, log, dan backup. `P1`

Exit criteria: halaman dan operasi utama tetap layak pada volume data target sekolah.

## Fase 9 - Review Requirement Hosting dan Operasional

- `[ ]` `AUD-900` Pilih hosting/domain final dan catat runtime, extension, SSH, cron, backup, SSL, serta document root. `P0`
- `[ ]` `AUD-901` Buat `.env` production dari template tanpa commit credential. `P0`
- `[ ]` `AUD-902` Tetapkan driver session, cache, queue, filesystem, mail, dan logging production. `P0`
- `[ ]` `AUD-903` Verifikasi permission `storage` dan `bootstrap/cache`. `P0`
- `[ ]` `AUD-904` Tentukan kebutuhan scheduler/queue dan cara menjalankannya di hosting. `P1`
- `[ ]` `AUD-905` Konfigurasi email nyata dan uji deliverability bila fitur email dipakai. `P1`
- `[ ]` `AUD-906` Siapkan monitoring health, log error, disk usage, backup result, dan notifikasi insiden. `P1`
- `[ ]` `AUD-907` Finalisasi command deployment idempotent dan urutan cache/migration/media. `P0`
- `[ ]` `AUD-908` Finalisasi runbook maintenance, rollback kode, rollback database, dan pemulihan file. `P0`

Exit criteria: semua nilai production dan prosedur operasional sudah konkret, bukan placeholder.

## Fase 10 - Staging dan User Acceptance Test (Belum Aktif)

- `[ ]` `AUD-1000` Deploy salinan aplikasi ke staging yang menyerupai production. `P0`
- `[ ]` `AUD-1001` Jalankan migration pada salinan data dan verifikasi media/private storage. `P0`
- `[ ]` `AUD-1002` Jalankan smoke test seluruh route publik/admin dan matriks role. `P0`
- `[ ]` `AUD-1003` Jalankan QA browser, HTTPS, cookie, upload, download, export, print, dan error page di staging. `P0`
- `[ ]` `AUD-1004` Jalankan UAT bersama pemilik sekolah/panitia untuk konten dan workflow operasional. `P1`
- `[ ]` `AUD-1005` Catat persetujuan go-live atau daftar defect yang harus diperbaiki. `P0`
- `[ ]` `AUD-1006` Latih rollback staging dan ukur waktu pemulihan. `P0`

Exit criteria: Gate C lulus dan user menyetujui workflow serta konten.

## Fase 11 - Production Cutover (Belum Aktif)

- `[ ]` `AUD-1100` Tetapkan jadwal deploy dan maintenance window. `P0`
- `[ ]` `AUD-1101` Ambil backup final database, upload publik, dan dokumen private. `P0`
- `[ ]` `AUD-1102` Verifikasi DNS, SSL, HTTPS redirect, document root, dan credential production. `P0`
- `[ ]` `AUD-1103` Deploy release yang sama dengan staging dan jalankan migration terkontrol. `P0`
- `[ ]` `AUD-1104` Jalankan config/route/view cache dan command media yang diperlukan. `P0`
- `[ ]` `AUD-1105` Jalankan smoke test production tanpa membuat data palsu yang tertinggal. `P0`
- `[ ]` `AUD-1106` Verifikasi log, health endpoint, session, upload, dokumen private, export, dan email. `P0`
- `[ ]` `AUD-1107` Putuskan lanjut atau rollback berdasarkan kriteria yang telah disepakati. `P0`

Exit criteria: Gate D lulus dan aplikasi production stabil.

## Fase 12 - Pascadeploy (Belum Aktif)

- `[ ]` `AUD-1200` Monitor error, response time, disk, database, dan login selama 24-72 jam awal. `P1`
- `[ ]` `AUD-1201` Verifikasi backup production pertama dan uji restore terjadwal. `P0`
- `[ ]` `AUD-1202` Review log untuk error tersembunyi, brute force, dan upload gagal. `P1`
- `[ ]` `AUD-1203` Dokumentasikan perubahan final, versi release, dan known issues. `P1`
- `[ ]` `AUD-1204` Susun jadwal patch dependency, audit keamanan, backup, dan housekeeping media/log. `P1`

Exit criteria: sistem masuk mode operasional rutin dengan monitoring dan maintenance yang jelas.

## Perintah Verifikasi Inti

```bash
composer validate --strict
composer audit
npm audit --audit-level=moderate
vendor/bin/pint --test
php artisan test
npm run build
php artisan route:list --except-vendor
php artisan migrate:status
php artisan config:cache
php artisan route:cache
php artisan view:cache
git diff --check
git diff --cached --check
```

Cache yang dibuat saat audit lokal harus dibersihkan kembali jika diperlukan agar tidak mengganggu pengembangan.

## Artefak Yang Harus Dihasilkan

- tracker ini dengan status dan bukti terbaru;
- laporan temuan terurut berdasarkan severity;
- matriks fitur, route, role, dan test;
- matriks browser/UI dan hasil screenshot QA yang tidak di-commit;
- laporan integritas database/media;
- bukti backup dan restore;
- checklist staging dan UAT;
- runbook deployment, rollback, dan pascadeploy;
- ringkasan keputusan go-live.
