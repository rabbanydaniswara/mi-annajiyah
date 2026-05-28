# Remediation Tracker

Tanggal dibuat: 2026-05-28  
Tujuan: merencanakan perbaikan proyek SPMB Annajiyah secara bertahap, menjaga keamanan data, meningkatkan kualitas aplikasi, dan menyiapkan hosting saat waktunya sudah tepat.

## Status Legend

- `[ ]` Belum dikerjakan
- `[~]` Sedang dikerjakan
- `[x]` Selesai
- `[!]` Tertahan / perlu keputusan

## Status Arah Kerja Saat Ini

Hosting belum menjadi target langsung. Fase teknis dasar, keamanan PPDB, route safety, test reliability, dan cleanup database sudah selesai. Mulai tahap berikutnya, prioritas kerja dialihkan ke peningkatan aplikasi/web sebelum deployment production.

Prioritas baru:

1. Rapikan workflow PPDB agar lebih lengkap untuk operasional panitia.
2. Tingkatkan ergonomi admin/operator untuk input, filter, export, dan verifikasi.
3. Perbaiki konten publik dan pengalaman pengguna pendaftaran.
4. Tambahkan fitur operasional yang membantu sekolah mengelola data.
5. Simpan checklist hosting sebagai fase tertunda sampai domain/hosting final dipilih.

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

- `[x]` Ganti cetak kartu dari numeric ID ke token acak.
  - Prioritas: P0
  - Target file: `routes/web.php`, `app/Http/Controllers/RegistrationController.php`, migration `siswa`, view pendaftaran.
  - Kriteria selesai: `/pendaftaran/cetak/{id}` tidak lagi membuka data berdasarkan ID berurutan; token invalid menghasilkan 404.
  - Catatan: kolom `registration_token` ditambahkan, token otomatis dibuat di model `Siswa`, dan test token valid/ID lama sudah ditambahkan.

- `[x]` Pindahkan dokumen PPDB ke private storage.
  - Prioritas: P0
  - Target file: `app/Helpers/ImageHelper.php` atau helper/service baru, `RegistrationController`, controller admin dokumen.
  - Kriteria selesai: akte/KK/KTP/ijazah tidak bisa diakses langsung lewat URL publik.
  - Catatan: upload baru masuk ke disk `local` private melalui `DocumentHelper`; path lama di `public/uploads` masih didukung untuk transisi; direct URL `/storage/ppdb/...` sudah ditutup oleh test.

- `[x]` Buat route khusus admin untuk melihat/mengunduh dokumen PPDB.
  - Prioritas: P0
  - Target file: `routes/web.php`, controller admin.
  - Kriteria selesai: hanya user login yang bisa membuka dokumen, operator/admin sesuai aturan.
  - Catatan: route `admin.ppdb.document` berada di grup `auth`; preview/link dokumen admin diarahkan ke route ini.

- `[x]` Tambahkan throttle untuk cek status pendaftaran.
  - Prioritas: P1
  - Target file: `app/Providers/AppServiceProvider.php`, `routes/web.php`.
  - Kriteria selesai: request berlebihan ke `/cek-pendaftaran` dibatasi.
  - Catatan: limiter `cek-pendaftaran` disetel 10 request/menit per IP.

- `[x]` Kurangi data yang tampil di cek status publik.
  - Prioritas: P1
  - Target file: `resources/views/public/cek-pendaftaran.blade.php`.
  - Kriteria selesai: hasil publik hanya menampilkan informasi seperlunya.
  - Catatan: halaman publik hanya menampilkan status, nama tersamarkan, tanggal daftar, dan update status; NISN/NIS/WA/asal sekolah tidak ditampilkan.

- `[x]` Hapus detail exception dari response publik.
  - Prioritas: P0
  - Target file: `app/Http/Controllers/RegistrationController.php`.
  - Kriteria selesai: user mendapat pesan generik, detail error masuk log server.
  - Catatan: detail exception dicatat ke log server, response publik memakai pesan generik.

## Fase 3 - Route Safety dan CSRF

Tujuan: memastikan aksi perubahan data memakai method yang benar.

- `[x]` Ubah toggle banner dari GET ke PATCH/POST.
  - Prioritas: P1
  - Target file: `routes/web.php`, `app/Http/Controllers/Admin/KontenController.php`, `resources/views/admin/konten.blade.php`.
  - Kriteria selesai: GET tidak lagi mengubah status banner.
  - Catatan: route banner toggle memakai `PATCH`, tombol view memakai form CSRF, dan test memastikan `GET` ditolak.

- `[x]` Ubah toggle fasilitas dari GET ke PATCH/POST.
  - Prioritas: P1
  - Target file: `routes/web.php`, `app/Http/Controllers/Admin/FasilitasController.php`, `resources/views/admin/fasilitas.blade.php`.
  - Kriteria selesai: GET tidak lagi mengubah status fasilitas.
  - Catatan: route fasilitas toggle memakai `PATCH`, tombol view memakai form CSRF, dan test memastikan `GET` ditolak.

- `[x]` Tambahkan `rel="noopener noreferrer"` untuk link `target="_blank"`.
  - Prioritas: P2
  - Target file: view publik dan admin.
  - Kriteria selesai: semua link tab baru memiliki rel yang aman.
  - Catatan: sweep view publik/admin selesai; semua `target="_blank"` di `resources` sudah memiliki `rel="noopener noreferrer"`.

- `[x]` Ganti penggunaan `innerHTML` untuk pesan dinamis dari server.
  - Prioritas: P1
  - Target file: `resources/views/public/pendaftaran.blade.php`.
  - Kriteria selesai: pesan server ditampilkan dengan `textContent` atau escaping aman.
  - Catatan: pesan sukses/error dinamis dari response pendaftaran sudah memakai DOM `textContent`; HTML yang tersisa bersifat statis untuk ikon/tombol.

## Fase 4 - Test dan Reliability

Tujuan: membuat perubahan bisa diverifikasi otomatis.

- `[x]` Perbaiki feature test homepage.
  - Prioritas: P1
  - Target file: `tests/Feature/ExampleTest.php`, `tests/TestCase.php`.
  - Kriteria selesai: `php artisan test` tidak gagal karena tabel belum ada.
  - Catatan: `RefreshDatabase` diaktifkan pada feature test baseline.

- `[x]` Tambahkan test submit PPDB sukses.
  - Prioritas: P1
  - Kriteria selesai: upload file palsu valid, record `siswa` dibuat, response JSON sukses.
  - Catatan: tercakup di `tests/Feature/PpdbRegistrationTest.php`; memastikan dokumen masuk private storage dan response mengembalikan `card_url`.

- `[x]` Tambahkan test validasi unique NISN/NIS.
  - Prioritas: P1
  - Kriteria selesai: data duplikat ditolak.
  - Catatan: tercakup di `tests/Feature/PpdbRegistrationTest.php`; duplikat `nisn` dan `nis` menghasilkan error validasi dan tidak mengunggah dokumen.

- `[x]` Tambahkan test role admin/operator.
  - Prioritas: P1
  - Kriteria selesai: operator tidak bisa akses kelola admin.
  - Catatan: tercakup di `tests/Feature/AdminRoleAccessTest.php`; admin bisa membuka kelola admin, operator hanya bisa masuk dashboard dan ditolak dari kelola admin.

- `[x]` Tambahkan test cetak kartu token.
  - Prioritas: P0 setelah token dibuat.
  - Kriteria selesai: token valid 200, token invalid 404, numeric ID lama tidak valid.
  - Catatan: tercakup di `tests/Feature/PpdbSecurityTest.php`.

- `[x]` Tambahkan test route toggle hanya menerima method aman.
  - Prioritas: P2
  - Kriteria selesai: GET toggle ditolak, PATCH/POST berhasil dengan CSRF/auth.
  - Catatan: tercakup di `tests/Feature/AdminRouteSafetyTest.php`.

## Fase 5 - Database dan Migration Cleanup

Tujuan: mengurangi risiko migration gagal di hosting.

- `[x]` Rapikan migration index dobel.
  - Prioritas: P1
  - Target file: `database/migrations/2026_04_30_235000_add_performance_indexes.php`, `database/migrations/2026_05_01_000000_add_performance_indexes.php`.
  - Kriteria selesai: tidak ada index overlap dan tidak ada `catch` kosong.
  - Catatan: index `nisn` dobel dihindari karena sudah unique; migration kedua hanya menambah index `nama` dan tidak lagi memakai `catch` kosong.

- `[x]` Evaluasi seeder admin default.
  - Prioritas: P0 untuk production.
  - Target file: `database/seeders/DatabaseSeeder.php`.
  - Kriteria selesai: production tidak membuat user `admin/admin123`.
  - Catatan: production tidak membuat admin default kecuali `INITIAL_ADMIN_USERNAME` dan `INITIAL_ADMIN_PASSWORD` disediakan; password production minimal 12 karakter; perilaku ini dilindungi test.

- `[x]` Selaraskan `.env.example` dengan setup yang dipilih.
  - Prioritas: P1
  - Target file: `.env.example`.
  - Kriteria selesai: nilai default tidak misleading, `APP_DEBUG` aman untuk template production atau diberi catatan jelas.
  - Catatan: `.env.example` sudah diarahkan ke Laravel 12/PHP 8.2+, MySQL, locale Indonesia, dan default production-safe.

## Fase 6 - Workflow PPDB dan Data Pendaftar

Tujuan: membuat proses PPDB lebih enak dipakai panitia dan lebih jelas untuk wali murid.

- `[x]` Tambahkan pengaturan tahun ajaran PPDB.
  - Prioritas: P1
  - Target area: model/migration pengaturan PPDB, admin konten atau menu khusus PPDB.
  - Kriteria selesai: admin dapat mengatur tahun ajaran aktif tanpa mengubah kode.
  - Catatan: tahun ajaran aktif bisa diatur dari tab `PPDB` pada menu konten; pendaftar baru menyimpan snapshot `tahun_ajaran`.

- `[x]` Tambahkan nomor pendaftaran publik yang tidak sama dengan ID database.
  - Prioritas: P1
  - Target area: model `Siswa`, migration, cetak kartu, admin PPDB.
  - Kriteria selesai: pendaftar memiliki nomor pendaftaran rapi untuk komunikasi panitia, tetap tidak membuka ID internal.
  - Catatan: kolom `nomor_pendaftaran` memakai format `PPDB-YYYY-0001`, tampil di admin, cek status, kartu cetak, dan export.

- `[x]` Rapikan status PPDB menjadi alur yang lebih eksplisit.
  - Prioritas: P2
  - Opsi status: `pending`, `berkas_kurang`, `diverifikasi`, `diterima`, `ditolak`, `daftar_ulang`.
  - Kriteria selesai: status sesuai kebutuhan operasional panitia dan tampil konsisten di admin/publik.
  - Catatan: status dipusatkan di `PpdbHelper`, didukung migration enum MySQL, tampil konsisten di admin, dashboard, cek status publik, dan test workflow.

- `[x]` Tambahkan catatan verifikasi internal.
  - Prioritas: P2
  - Target area: admin PPDB.
  - Kriteria selesai: operator/admin dapat menulis catatan tanpa tampil ke publik.
  - Catatan: kolom `catatan_verifikasi` ditambahkan, bisa disimpan dari modal/bulk update admin, dan tidak ditampilkan di halaman cek status publik.

- `[x]` Tambahkan filter dan export PPDB yang lebih terarah.
  - Prioritas: P2
  - Target area: admin PPDB, export.
  - Kriteria selesai: data bisa difilter berdasarkan status, tahun ajaran, tanggal daftar, dan kelas tujuan.
  - Catatan: admin PPDB dan export mendukung filter status, tahun ajaran, kelas, rentang tanggal, dan pencarian nomor pendaftaran.

## Fase 7 - Admin Panel dan Operasional Sekolah

Tujuan: membuat area admin/operator lebih nyaman untuk pekerjaan berulang.

- `[x]` Evaluasi ulang hak akses admin dan operator per menu.
  - Prioritas: P1
  - Kriteria selesai: ada matriks role yang jelas dan middleware/policy mengikuti matriks tersebut.
  - Catatan: matriks ada di `docs/ROLE_MATRIX.md`; route kelola akun tetap dibatasi middleware `admin`, dan link sidebar disembunyikan dari operator.

- `[x]` Tambahkan fitur ganti password untuk user login.
  - Prioritas: P1
  - Kriteria selesai: admin/operator bisa mengganti password sendiri dengan validasi password lama.
  - Catatan: route `admin.password.edit/update` ditambahkan, validasi password lama aktif, dan aktivitas dicatat.

- `[x]` Tambahkan dashboard ringkas PPDB.
  - Prioritas: P2
  - Kriteria selesai: dashboard menampilkan tren pendaftar, status, dan tugas verifikasi yang perlu ditindaklanjuti.
  - Catatan: dashboard menampilkan jumlah per status PPDB dan daftar pendaftar pending/berkas kurang yang perlu ditindaklanjuti.

- `[x]` Rapikan log aktivitas agar mudah diaudit.
  - Prioritas: P2
  - Kriteria selesai: log bisa difilter berdasarkan user, action, tanggal, dan model terkait.
  - Catatan: tab log mendukung filter user, action, model, dan rentang tanggal.

- `[x]` Tambahkan bulk action untuk data PPDB jika dibutuhkan.
  - Prioritas: P3
  - Kriteria selesai: admin dapat mengubah status beberapa pendaftar secara aman dengan konfirmasi.
  - Catatan: bulk update status PPDB tersedia dengan catatan verifikasi opsional dan activity log per pendaftar.

## Fase 8 - Konten Publik, Media, dan UX Pendaftaran

Tujuan: meningkatkan pengalaman wali murid dan membuat konten sekolah lebih mudah dirawat.

- `[x]` Rapikan validasi dan format input nomor WhatsApp.
  - Prioritas: P1
  - Kriteria selesai: nomor WA disimpan dalam format konsisten dan pesan validasi mudah dipahami.
  - Catatan: nomor WhatsApp dinormalisasi ke format `62...` pada pendaftaran publik dan input siswa admin.

- `[x]` Tambahkan preview file sebelum submit pendaftaran.
  - Prioritas: P2
  - Kriteria selesai: wali murid bisa melihat nama/ukuran file yang dipilih sebelum mengirim.
  - Catatan: step dokumen menampilkan nama dan ukuran file terpilih.

- `[x]` Perbaiki warning font Inter pada build.
  - Prioritas: P2
  - Kriteria selesai: `npm run build` tidak lagi memberi warning font unresolved.
  - Catatan: referensi font lokal yang tidak tersedia dihapus; aplikasi memakai stack Inter/system font.

- `[x]` Evaluasi konten homepage dan halaman informasi.
  - Prioritas: P2
  - Kriteria selesai: teks, kontak, alamat, media sosial, dan CTA sesuai data sekolah terbaru.
  - Catatan: checklist evaluasi konten tersedia di `docs/CONTENT_MEDIA_CHECKLIST.md`; data final sekolah tetap perlu diverifikasi pemilik konten.

- `[x]` Rapikan manajemen media publik.
  - Prioritas: P3
  - Kriteria selesai: upload banner/guru/kegiatan/fasilitas memiliki validasi, thumbnail, dan penghapusan file yang konsisten.
  - Catatan: validasi dan helper thumbnail/penghapusan sudah dipakai di modul media utama; checklist perawatan media ditambahkan.

## Fase 9 - Hardening Lanjutan dan Kualitas Kode

Tujuan: menjaga aplikasi tetap stabil saat fitur bertambah.

- `[x]` Tambahkan test untuk admin PPDB update status.
  - Prioritas: P1
  - Kriteria selesai: status berubah, tanggal verifikasi terisi, dan activity log tercatat.
  - Catatan: tercakup di `tests/Feature/PpdbWorkflowTest.php`, termasuk catatan internal dan bulk update.

- `[x]` Tambahkan test untuk fitur konten utama.
  - Prioritas: P2
  - Kriteria selesai: create/update/delete banner, fasilitas, guru, dan kegiatan minimal tercakup.
  - Catatan: coverage konten utama ditambahkan untuk update konten, kegiatan, guru, dan fasilitas; banner/toggle sudah tercakup di route safety.

- `[x]` Evaluasi refactor upload publik vs dokumen private.
  - Prioritas: P2
  - Kriteria selesai: ada pemisahan helper/service yang jelas untuk media publik dan dokumen sensitif.
  - Catatan: dokumen sensitif memakai `DocumentHelper` private storage, sedangkan media publik tetap melalui `ImageHelper`.

- `[x]` Tambahkan backup/export database lokal sebelum perubahan besar.
  - Prioritas: P1
  - Kriteria selesai: ada prosedur backup yang bisa dijalankan sebelum migration destruktif.
  - Catatan: panduan backup/recovery lokal tersedia di `docs/BACKUP_GUIDE.md`.

## Fase 10 - Deployment Readiness (Ditunda)

Tujuan: memastikan siap naik hosting saat fitur aplikasi sudah cukup matang.

- `[!]` Pastikan document root hosting mengarah ke `public`.
  - Prioritas: P0 saat hosting dimulai
  - Kriteria selesai: file root project seperti `.env`, `composer.json`, dan `storage` tidak bisa diakses publik.
  - Catatan: ditunda sampai hosting/domain final dipilih.

- `[!]` Jalankan full verification sebelum deploy.
  - Prioritas: P0 saat hosting dimulai
  - Perintah:
    - `composer validate --strict`
    - `composer audit`
    - `npm audit --audit-level=moderate`
    - `php artisan test`
    - `npm run build`
    - `php artisan route:list`
  - Kriteria selesai: semua lulus atau ada catatan risiko yang diterima.
  - Catatan: tetap dijalankan tiap fase besar, tetapi checklist deploy final ditunda.

- `[!]` Siapkan konfigurasi production.
  - Prioritas: P0 saat hosting dimulai
  - Wajib: `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://domain-final`, database production, mailer, session/cookie HTTPS.
  - Kriteria selesai: tidak ada konfigurasi local/debug di production.
  - Catatan: menunggu domain, database, dan paket hosting final.

- `[!]` Jalankan migration production secara terkontrol.
  - Prioritas: P0 saat hosting dimulai
  - Kriteria selesai: `php artisan migrate --force` berhasil dan data lama aman.
  - Catatan: wajib didahului backup database dan folder upload.

- `[!]` Cache konfigurasi/view untuk production.
  - Prioritas: P1 saat hosting dimulai
  - Perintah: `php artisan config:cache`, `php artisan view:cache`.
  - Catatan: `route:cache` perlu diuji karena saat ini ada route closure untuk sitemap.

## Urutan Eksekusi yang Disarankan

1. Fase 0-5: fondasi keamanan, dependency, test, route safety, dan database cleanup. Status: selesai mayoritas.
2. Fase 6: workflow PPDB dan data pendaftar.
3. Fase 7: admin panel dan operasional sekolah.
4. Fase 8: konten publik, media, dan UX pendaftaran.
5. Fase 9: hardening lanjutan dan kualitas kode.
6. Fase 10: checklist deploy, ditunda sampai hosting/domain final dipilih.

## Catatan Keputusan

Isi bagian ini saat keputusan sudah dibuat.

- Dependency target: Laravel 12 + PHP `^8.2`; frontend Vite 6 + `laravel-vite-plugin` 1.x.
- Hosting target: belum diputuskan dan tidak menjadi fokus langsung; checklist tetap tersedia di `docs/HOSTING_READINESS.md`.
- Database production: MySQL/MariaDB direkomendasikan; detail database belum diputuskan.
- Strategi deploy asset: build lokal/CI, lalu deploy `public/build`.
- Repository remote: `origin` terhubung ke `https://github.com/rabbanydaniswara/mi-annajiyah.git`, branch lokal `main`.
- Fokus kerja berikutnya: pengembangan fitur aplikasi dan perbaikan UX sebelum masuk tahap hosting.
