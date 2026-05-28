# Remediation Tracker

Tanggal dibuat: 2026-05-28  
Tujuan: merencanakan perbaikan proyek SPMB Annajiyah secara bertahap, aman untuk hosting umum, dan mudah dilacak.

## Status Legend

- `[ ]` Belum dikerjakan
- `[~]` Sedang dikerjakan
- `[x]` Selesai
- `[!]` Tertahan / perlu keputusan

## Target Stack Aman

Rekomendasi konservatif untuk target hosting umum seperti shared hosting/cPanel:

- PHP: `^8.2` atau `^8.3`, dengan prioritas kompatibilitas hosting.
- Laravel: pertimbangkan Laravel 12 jika ingin target paling aman untuk shared hosting yang belum merata PHP 8.3.
- Node/Vite: build asset sebaiknya dilakukan di lokal/CI, lalu upload hasil `public/build`; hosting tidak wajib menjalankan Node.
- Database: MySQL/MariaDB untuk production.
- Document root: wajib mengarah ke folder `public`.

Keputusan yang perlu diambil sebelum implementasi dependency:

- `[ ]` Putuskan tetap Laravel 13 jika hosting final mendukung PHP 8.3/8.4, Composer, extension lengkap, dan SSH.
- `[x]` Putuskan downgrade ke Laravel 12 jika target hosting lebih terbatas atau ingin kompatibilitas PHP 8.2.

Rekomendasi saya: gunakan Laravel 12 + PHP 8.2/8.3 bila prioritasnya minim risiko deploy di hosting umum. Tetap Laravel 13 hanya jika paket hosting final sudah pasti mendukung PHP 8.3+ dengan baik.

## Fase 0 - Baseline dan Backup

Tujuan: memastikan perubahan berikutnya bisa dilacak dan dipulihkan.

- `[x]` Inisialisasi Git atau pindahkan pekerjaan ke repository Git utama.
  - Prioritas: P0
  - Kriteria selesai: `git status` berjalan dan perubahan bisa di-review lewat diff.

- `[ ]` Buat backup database dan folder upload sebelum perubahan storage/migration.
  - Prioritas: P0
  - Kriteria selesai: ada backup database dan backup `public/uploads`.

- `[x]` Catat versi runtime lokal dan target hosting.
  - Prioritas: P1
  - Kriteria selesai: versi PHP, Composer, Node, MySQL/MariaDB, dan extension PHP terdokumentasi.
  - Catatan: runtime lokal dicatat di `docs/HOSTING_READINESS.md`; target hosting masih menunggu paket final.

## Fase 1 - Keputusan Dependency dan Hosting

Tujuan: memilih dependency yang aman sebelum menyentuh fitur.

- `[!]` Cek paket hosting final.
  - Prioritas: P0
  - Cek minimal: PHP 8.2/8.3, Composer 2, SSH, Fileinfo, Mbstring, OpenSSL, PDO MySQL, DOM/XML, kemampuan set document root ke `public`.
  - Kriteria selesai: ada keputusan hosting layak atau perlu upgrade paket.
  - Catatan: tertahan sampai paket hosting final/akses hosting diberikan.

- `[x]` Tentukan jalur dependency.
  - Prioritas: P0
  - Opsi A: tetap Laravel 13 jika PHP 8.3+ aman.
  - Opsi B: downgrade ke Laravel 12 jika ingin target PHP 8.2 lebih aman.
  - Kriteria selesai: `composer.json` target disepakati.
  - Catatan: dipilih Laravel 12 + PHP `^8.2`.

- `[x]` Rencanakan update dependency security.
  - Prioritas: P0
  - Target: `composer audit` bersih dari advisory high/medium.
  - Kriteria selesai: daftar package yang perlu update jelas sebelum eksekusi.
  - Catatan: dependency sudah diturunkan ke Laravel 12 dan `composer audit` sudah bersih.

- `[x]` Tentukan strategi asset frontend.
  - Prioritas: P1
  - Rekomendasi: build di lokal/CI, deploy `public/build`.
  - Kriteria selesai: hosting tidak perlu menjalankan `npm run build`.
  - Catatan: frontend diarahkan ke Vite 6 + `laravel-vite-plugin` 1.x agar build lebih ramah Node 18/20.

## Fase 2 - Security dan Privasi Data PPDB

Tujuan: menutup risiko terbesar sebelum aplikasi dipakai publik.

- `[ ]` Ganti cetak kartu dari numeric ID ke token acak.
  - Prioritas: P0
  - Target file: `routes/web.php`, `app/Http/Controllers/RegistrationController.php`, migration `siswa`, view pendaftaran.
  - Kriteria selesai: `/pendaftaran/cetak/{id}` tidak lagi membuka data berdasarkan ID berurutan; token invalid menghasilkan 404.

- `[ ]` Pindahkan dokumen PPDB ke private storage.
  - Prioritas: P0
  - Target file: `app/Helpers/ImageHelper.php` atau helper/service baru, `RegistrationController`, controller admin dokumen.
  - Kriteria selesai: akte/KK/KTP/ijazah tidak bisa diakses langsung lewat URL publik.

- `[ ]` Buat route khusus admin untuk melihat/mengunduh dokumen PPDB.
  - Prioritas: P0
  - Target file: `routes/web.php`, controller admin.
  - Kriteria selesai: hanya user login yang bisa membuka dokumen, operator/admin sesuai aturan.

- `[ ]` Tambahkan throttle untuk cek status pendaftaran.
  - Prioritas: P1
  - Target file: `app/Providers/AppServiceProvider.php`, `routes/web.php`.
  - Kriteria selesai: request berlebihan ke `/cek-pendaftaran` dibatasi.

- `[ ]` Kurangi data yang tampil di cek status publik.
  - Prioritas: P1
  - Target file: `resources/views/public/cek-pendaftaran.blade.php`.
  - Kriteria selesai: hasil publik hanya menampilkan informasi seperlunya.

- `[ ]` Hapus detail exception dari response publik.
  - Prioritas: P0
  - Target file: `app/Http/Controllers/RegistrationController.php`.
  - Kriteria selesai: user mendapat pesan generik, detail error masuk log server.

## Fase 3 - Route Safety dan CSRF

Tujuan: memastikan aksi perubahan data memakai method yang benar.

- `[ ]` Ubah toggle banner dari GET ke PATCH/POST.
  - Prioritas: P1
  - Target file: `routes/web.php`, `app/Http/Controllers/Admin/KontenController.php`, `resources/views/admin/konten.blade.php`.
  - Kriteria selesai: GET tidak lagi mengubah status banner.

- `[ ]` Ubah toggle fasilitas dari GET ke PATCH/POST.
  - Prioritas: P1
  - Target file: `routes/web.php`, `app/Http/Controllers/Admin/FasilitasController.php`, `resources/views/admin/fasilitas.blade.php`.
  - Kriteria selesai: GET tidak lagi mengubah status fasilitas.

- `[ ]` Tambahkan `rel="noopener noreferrer"` untuk link `target="_blank"`.
  - Prioritas: P2
  - Target file: view publik dan admin.
  - Kriteria selesai: semua link tab baru memiliki rel yang aman.

- `[ ]` Ganti penggunaan `innerHTML` untuk pesan dinamis dari server.
  - Prioritas: P1
  - Target file: `resources/views/public/pendaftaran.blade.php`.
  - Kriteria selesai: pesan server ditampilkan dengan `textContent` atau escaping aman.

## Fase 4 - Test dan Reliability

Tujuan: membuat perubahan bisa diverifikasi otomatis.

- `[x]` Perbaiki feature test homepage.
  - Prioritas: P1
  - Target file: `tests/Feature/ExampleTest.php`, `tests/TestCase.php`.
  - Kriteria selesai: `php artisan test` tidak gagal karena tabel belum ada.
  - Catatan: `RefreshDatabase` diaktifkan pada feature test baseline.

- `[ ]` Tambahkan test submit PPDB sukses.
  - Prioritas: P1
  - Kriteria selesai: upload file palsu valid, record `siswa` dibuat, response JSON sukses.

- `[ ]` Tambahkan test validasi unique NISN/NIS.
  - Prioritas: P1
  - Kriteria selesai: data duplikat ditolak.

- `[ ]` Tambahkan test role admin/operator.
  - Prioritas: P1
  - Kriteria selesai: operator tidak bisa akses kelola admin.

- `[ ]` Tambahkan test cetak kartu token.
  - Prioritas: P0 setelah token dibuat.
  - Kriteria selesai: token valid 200, token invalid 404, numeric ID lama tidak valid.

- `[ ]` Tambahkan test route toggle hanya menerima method aman.
  - Prioritas: P2
  - Kriteria selesai: GET toggle ditolak, PATCH/POST berhasil dengan CSRF/auth.

## Fase 5 - Database dan Migration Cleanup

Tujuan: mengurangi risiko migration gagal di hosting.

- `[ ]` Rapikan migration index dobel.
  - Prioritas: P1
  - Target file: `database/migrations/2026_04_30_235000_add_performance_indexes.php`, `database/migrations/2026_05_01_000000_add_performance_indexes.php`.
  - Kriteria selesai: tidak ada index overlap dan tidak ada `catch` kosong.

- `[ ]` Evaluasi seeder admin default.
  - Prioritas: P0 untuk production.
  - Target file: `database/seeders/DatabaseSeeder.php`.
  - Kriteria selesai: production tidak membuat user `admin/admin123`.

- `[x]` Selaraskan `.env.example` dengan setup yang dipilih.
  - Prioritas: P1
  - Target file: `.env.example`.
  - Kriteria selesai: nilai default tidak misleading, `APP_DEBUG` aman untuk template production atau diberi catatan jelas.
  - Catatan: `.env.example` sudah diarahkan ke Laravel 12/PHP 8.2+, MySQL, locale Indonesia, dan default production-safe.

## Fase 6 - Deployment Readiness

Tujuan: memastikan siap naik hosting.

- `[ ]` Pastikan document root hosting mengarah ke `public`.
  - Prioritas: P0
  - Kriteria selesai: file root project seperti `.env`, `composer.json`, dan `storage` tidak bisa diakses publik.

- `[ ]` Jalankan full verification sebelum deploy.
  - Prioritas: P0
  - Perintah:
    - `composer validate --strict`
    - `composer audit`
    - `npm audit --audit-level=moderate`
    - `php artisan test`
    - `npm run build`
    - `php artisan route:list`
  - Kriteria selesai: semua lulus atau ada catatan risiko yang diterima.

- `[ ]` Siapkan konfigurasi production.
  - Prioritas: P0
  - Wajib: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domain-final`, database production, mailer, session/cookie HTTPS.
  - Kriteria selesai: tidak ada konfigurasi local/debug di production.

- `[ ]` Jalankan migration production secara terkontrol.
  - Prioritas: P0
  - Kriteria selesai: `php artisan migrate --force` berhasil dan data lama aman.

- `[ ]` Cache konfigurasi/view untuk production.
  - Prioritas: P1
  - Perintah: `php artisan config:cache`, `php artisan view:cache`.
  - Catatan: `route:cache` perlu diuji karena saat ini ada route closure untuk sitemap.

## Urutan Eksekusi yang Disarankan

1. Fase 0: Git dan backup.
2. Fase 1: keputusan Laravel 12 vs 13 berdasarkan hosting final.
3. Fase 2: token kartu dan private storage dokumen.
4. Fase 3: route method safety dan pesan frontend aman.
5. Fase 4: test otomatis.
6. Fase 5: cleanup migration/seeder/env.
7. Fase 6: checklist deploy.

## Catatan Keputusan

Isi bagian ini saat keputusan sudah dibuat.

- Dependency target: Laravel 12 + PHP `^8.2`; frontend Vite 6 + `laravel-vite-plugin` 1.x.
- Hosting target: belum diputuskan; checklist tersedia di `docs/HOSTING_READINESS.md`.
- Database production: MySQL/MariaDB direkomendasikan; detail database belum diputuskan.
- Strategi deploy asset: build lokal/CI, lalu deploy `public/build`.
- Repository remote: `origin` terhubung ke `https://github.com/rabbanydaniswara/mi-annajiyah.git`, branch lokal `main`.
