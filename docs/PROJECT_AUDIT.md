# Audit Proyek SPMB Annajiyah

Tanggal audit: 2026-05-28  
Stack: Laravel 13, PHP 8.3, Blade, Vite 8, Tailwind 4, Alpine.js, FontAwesome  
Lingkup: `app`, `routes`, `database`, `resources`, `config`, `tests`, dependency PHP/Node, dan konfigurasi runtime lokal. Folder `vendor`, `node_modules`, `public/build`, dan aset upload tidak diaudit per file.

## Ringkasan

Aplikasi sudah memiliki dasar yang cukup rapi: route admin terpisah, CSRF aktif di form, validasi upload ada, role `admin`/`operator` sudah mulai dipakai, dan build frontend berhasil. Risiko terbesar saat ini ada di privasi data pendaftar, penyimpanan dokumen sensitif, route yang mengubah state lewat GET, konfigurasi produksi, dependency security advisory, dan test suite yang belum siap.

## Temuan Prioritas Tinggi

### 1. Kartu pendaftaran publik dapat diakses dengan ID berurutan

Lokasi:

- `routes/web.php:24`
- `app/Http/Controllers/RegistrationController.php:83`
- `resources/views/public/pendaftaran.blade.php:223`

Route `/pendaftaran/cetak/{id}` memanggil `Siswa::findOrFail($id)` tanpa token rahasia, ownership check, atau pembatasan akses. Karena ID database umumnya berurutan, pihak luar dapat menebak ID dan membuka kartu pendaftaran orang lain.

Dampak: kebocoran nama anak, tanggal lahir, sekolah asal, nama orang tua, nomor WhatsApp, dan nomor pendaftaran.

Rekomendasi:

- Tambahkan `registration_token`/UUID acak di tabel `siswa`.
- Ubah route menjadi `/pendaftaran/cetak/{token}`.
- Jangan tampilkan kartu berdasarkan numeric ID publik.
- Pertimbangkan expiry token atau verifikasi tambahan memakai nomor WA/NISN.

### 2. Dokumen PPDB sensitif disimpan langsung di `public/uploads`

Lokasi:

- `app/Http/Controllers/RegistrationController.php:42`
- `app/Http/Controllers/RegistrationController.php:43`
- `app/Http/Controllers/RegistrationController.php:44`
- `app/Http/Controllers/RegistrationController.php:46`
- `app/Helpers/ImageHelper.php:21`

File akte, KK, KTP orang tua, dan ijazah diupload ke path publik. Jika path file diketahui dari admin, log, backup, atau pola nama, file dapat diakses langsung lewat URL.

Dampak: kebocoran dokumen identitas keluarga.

Rekomendasi:

- Simpan dokumen pendaftar di disk private, misalnya `storage/app/private/ppdb`.
- Buat controller download/view khusus admin yang mengecek auth dan role.
- Pisahkan upload publik seperti banner/guru/kegiatan dari dokumen sensitif.
- Tambahkan migrasi untuk memindahkan path lama jika aplikasi sudah berjalan.

### 3. Endpoint toggle state memakai GET

Lokasi:

- `routes/web.php:85`
- `routes/web.php:102`

Route toggle banner dan fasilitas memakai GET. GET seharusnya idempotent/read-only. Karena route ini mengubah data, aksi bisa terpicu dari link, prefetch browser, crawler internal, atau CSRF-like link click.

Rekomendasi:

- Ubah menjadi `PATCH` atau `POST`.
- Gunakan form Blade dengan `@csrf` dan `@method('PATCH')`.
- Pertahankan konfirmasi UI jika aksi berdampak ke tampilan publik.

### 4. Dependency PHP memiliki security advisories

Hasil `composer audit`: 8 advisory pada 6 package, termasuk `symfony/mime` high severity dan beberapa medium severity.

Package terdampak:

- `symfony/http-foundation`
- `symfony/http-kernel`
- `symfony/mailer`
- `symfony/mime`
- `symfony/polyfill-intl-idn`
- `symfony/routing`

Rekomendasi:

- Jalankan `composer update` terkontrol.
- Setelah update, jalankan `composer audit`, `php artisan test`, dan regression test manual alur PPDB/admin.
- Prioritaskan advisory high severity lebih dulu.

### 5. Konfigurasi produksi masih berisiko bila dipakai apa adanya

Lokasi:

- `.env.example:2`
- `.env.example:4`
- `.env.example:23`
- `.env.example:37`

`.env.example` masih `APP_ENV=local` dan `APP_DEBUG=true`. Di env lokal aktual juga terdeteksi `APP_DEBUG=true`. Ini aman untuk dev, tetapi berbahaya jika terbawa ke hosting.

Rekomendasi produksi:

- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_URL` sesuai domain HTTPS
- `SESSION_SECURE_COOKIE=true` jika sudah HTTPS
- Jangan commit `.env`
- Rotasi `APP_KEY` jika pernah bocor

### 6. Seeder membuat admin default dengan password mudah ditebak

Lokasi:

- `database/seeders/DatabaseSeeder.php:16`

Seeder membuat user `admin` dengan password `admin123`.

Status perbaikan 2026-05-28: selesai. Seeder production tidak lagi membuat `admin/admin123` otomatis; admin awal hanya dibuat jika `INITIAL_ADMIN_USERNAME` dan `INITIAL_ADMIN_PASSWORD` disediakan.

Rekomendasi:

- Untuk produksi, jangan seeding password default.
- Gunakan env variable sekali pakai atau command artisan untuk membuat admin awal.
- Paksa ganti password saat login pertama.

## Temuan Prioritas Sedang

### 7. Test feature gagal karena database testing belum dimigrasi

Lokasi:

- `tests/Feature/ExampleTest.php:5`
- `phpunit.xml:26`

`php artisan test` gagal. Test memakai SQLite memory, tetapi `RefreshDatabase` masih dikomentari sehingga tabel `banner` tidak ada ketika route `/` mengakses database.

Rekomendasi:

- Aktifkan `RefreshDatabase` di feature test.
- Tambahkan seeder minimal atau factory untuk konten homepage.
- Buat test khusus untuk route publik, login admin, role admin/operator, dan submit PPDB.

### 8. `php artisan migrate:status` gagal karena MySQL lokal tidak aktif

Konfigurasi lokal menggunakan MySQL, tetapi koneksi `127.0.0.1:3306` menolak koneksi. Ini bukan bug aplikasi langsung, tetapi menghambat verifikasi migration dan operasional lokal.

Rekomendasi:

- Dokumentasikan kebutuhan MySQL lokal.
- Sediakan opsi SQLite dev jika ingin setup cepat.
- Pastikan `.env.example` selaras dengan setup yang direkomendasikan.

### 9. Migration index dobel dan sebagian error disembunyikan

Lokasi:

- `database/migrations/2026_04_30_235000_add_performance_indexes.php`
- `database/migrations/2026_05_01_000000_add_performance_indexes.php`

Dua migration menambah index yang overlap pada `siswa.no_wa`, `siswa.status_ppdb`, `siswa.tanggal_daftar`, dan `kegiatan_sekolah.tanggal`. Migration kedua membungkus setiap perubahan dalam `try/catch` kosong sehingga kegagalan bisa tidak terlihat.

Status perbaikan 2026-05-28: selesai. Index overlap dirapikan dan `catch` kosong di migration index dihapus.

Rekomendasi:

- Konsolidasikan index dalam satu migration baru untuk database yang belum deploy.
- Untuk database yang sudah deploy, buat migration korektif yang eksplisit mengecek nama index.
- Hindari `catch (\Exception $e) {}` kosong di migration.

### 10. Pencarian status pendaftaran belum diberi throttle

Lokasi:

- `routes/web.php:31`
- `app/Http/Controllers/HomeController.php:108`

Halaman cek pendaftaran mencari berdasarkan NISN, NIS, atau nomor WA. Tanpa throttle, endpoint ini dapat dipakai untuk brute force data pendaftar.

Rekomendasi:

- Tambahkan rate limiter khusus untuk `cek-pendaftaran`.
- Pertimbangkan verifikasi dua faktor sederhana, misalnya NISN + 4 digit akhir WA.
- Batasi data yang ditampilkan di hasil pencarian publik.

### 11. Error internal dikirim ke frontend saat submit PPDB gagal

Lokasi:

- `app/Http/Controllers/RegistrationController.php:76`
- `resources/views/public/pendaftaran.blade.php:233`

Catch block mengirim `Terjadi kesalahan: ` plus pesan exception ke user. Di frontend, `data.message` dimasukkan ke `innerHTML`. Pada kondisi tertentu ini dapat membocorkan detail internal dan membuka risiko HTML injection jika pesan tidak dikontrol.

Rekomendasi:

- Log detail exception di server.
- Kirim pesan generik ke user.
- Di JavaScript, gunakan `textContent` atau escaping saat menampilkan pesan dari server.

## Temuan Prioritas Rendah

### 12. Build frontend berhasil tetapi font lokal belum resolve saat build

Hasil `npm run build` berhasil, namun Vite memberi warning bahwa beberapa font `/fonts/inter-*.woff2` tidak resolve saat build dan akan dicari runtime.

Rekomendasi:

- Pastikan file font ada di `public/fonts`.
- Jika font memang eksternal/tidak dipakai, bersihkan referensi CSS.

### 13. Repo lokal bukan git repository

`git status` gagal karena folder ini tidak memiliki `.git`.

Rekomendasi:

- Inisialisasi git atau kerjakan dari clone repository utama.
- Jangan simpan `vendor`, `node_modules`, upload privat, dan `.env` di version control.

## Hasil Pemeriksaan Otomatis

- `php -l` untuk `app`, `routes`, `database`, dan `config`: lulus, tidak ada syntax error.
- `npm run build`: lulus, dengan warning font runtime.
- `npm audit --audit-level=moderate`: lulus, 0 vulnerability.
- `composer validate --strict`: lulus.
- `composer audit`: gagal karena 8 security advisories.
- `php artisan route:list`: lulus, 47 route terdaftar.
- `php artisan migrate:status`: gagal karena MySQL lokal tidak aktif/menolak koneksi.
- `php artisan test`: gagal, 1 feature test gagal karena tabel `banner` tidak ada di SQLite memory.

## Prioritas Perbaikan

1. Amankan kartu pendaftaran publik dengan token acak.
2. Pindahkan dokumen PPDB dari `public/uploads` ke private storage.
3. Ubah route toggle GET menjadi POST/PATCH.
4. Update dependency PHP sampai `composer audit` bersih.
5. Perbaiki test suite dengan migration/factory/seeder testing.
6. Siapkan konfigurasi produksi dan hilangkan default credential.
7. Rapikan migration index overlap.
