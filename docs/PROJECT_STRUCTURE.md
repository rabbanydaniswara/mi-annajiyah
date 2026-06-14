# Struktur Proyek

## Gambaran Umum

Proyek ini adalah aplikasi web SPMB/PPDB MI Annajiyah berbasis Laravel. Aplikasi memiliki dua area utama:

- Area publik: homepage, formulir pendaftaran, cek status pendaftaran, profil guru, fasilitas, kegiatan, dan sitemap.
- Area admin: dashboard, kelola PPDB, konten website, jadwal, guru, fasilitas, siswa, admin user, export, dan log aktivitas.

## Stack

- Backend: Laravel 12, PHP `^8.2` (lokal teruji di PHP 8.3)
- Frontend: Blade, Tailwind CSS 4, Alpine.js, FontAwesome
- Build tool: Vite 6
- Database dev/testing: MySQL lokal untuk `.env`, SQLite memory untuk `phpunit.xml`
- Queue/cache/session: memakai driver database pada contoh konfigurasi

## Folder Penting

- `app/Http/Controllers`: controller publik dan admin.
- `app/Exports`: class export spreadsheet admin berbasis Laravel Excel/PhpSpreadsheet.
- `app/Models`: model Eloquent untuk `Siswa`, `Guru`, `Jadwal`, `KontenWeb`, `Banner`, `Fasilitas`, `KegiatanSekolah`, `KegiatanKategori`, `ActivityLog`, dan `User`.
- `app/Helpers`: helper gambar, dokumen, dan activity log.
- `app/Http/Middleware`: middleware role admin.
- `routes/web.php`: seluruh route publik dan admin.
- `database/migrations`: schema tabel aplikasi.
- `database/seeders`: data awal admin, konten, guru, fasilitas, kegiatan, jadwal.
- `resources/views/public`: halaman publik.
- `resources/views/admin`: halaman admin.
- `resources/views/layouts`: layout publik dan admin.
- `resources/css` dan `resources/js`: asset source untuk Vite.
- `public/uploads`: file upload publik saat ini.
- `public/build`: hasil build Vite.
- `tests`: test PHPUnit bawaan/proyek.

## Route Publik

- `/`: homepage.
- `/pendaftaran`: form PPDB.
- `/api/pendaftaran`: submit form PPDB, POST, middleware `ppdb.open`, throttle `pendaftaran`.
- `/pendaftaran/cetak/{token}`: cetak kartu pendaftaran memakai token acak.
- `/cek-pendaftaran`: cek status pendaftaran.
- `/tenaga-pendidik`: daftar guru.
- `/fasilitas`: daftar fasilitas.
- `/kegiatan`: daftar kegiatan.
- `/sitemap.xml`: sitemap statis dari route utama.

## Route Admin

Semua route admin utama berada di prefix `/admin` dan middleware `auth`, kecuali login.

- `/admin/login`: form dan proses login.
- `/admin`: dashboard.
- `/admin/ppdb`: daftar pendaftar, update status, hapus data PPDB.
- `/admin/konten`: update konten, kegiatan, kategori, banner.
  - Tab `PPDB` mengatur tahun ajaran aktif, status pendaftaran buka/tutup, dan pesan publik saat ditutup.
- `/admin/jadwal`: CRUD jadwal dan print.
- `/admin/guru`: CRUD guru.
- `/admin/fasilitas`: CRUD fasilitas.
- `/admin/siswa`: CRUD siswa.
- `/admin/admin-users`: CRUD admin/operator, dibatasi middleware `admin`.
- `/admin/export/{type}`: export siswa/PPDB/guru.
  - Default export menghasilkan workbook `.xlsx` asli.
  - Format PDF tetap tersedia dengan query `format=pdf`.

## Model dan Tabel

- `User` -> `users`: admin dan operator.
- `Siswa` -> `siswa`: data siswa/pendaftar PPDB.
- `Guru` -> `guru`: data guru dan relasi ke jadwal.
- `Jadwal` -> `jadwal`: jadwal pelajaran, relasi ke guru.
- `KontenWeb` -> `konten_web`: visi, misi, sejarah, kontak.
- `Banner` -> `banner`: banner homepage.
- `Fasilitas` -> `fasilitas`: fasilitas sekolah.
- `KegiatanKategori` -> `kegiatan_kategori`: kategori kegiatan.
- `KegiatanSekolah` -> `kegiatan_sekolah`: item kegiatan.
- `ActivityLog` -> `activity_logs`: log aksi admin dan public registration.

## Alur Pendaftaran PPDB

1. User membuka `/pendaftaran`.
2. `PpdbHelper` membaca setting `ppdb_status`, `ppdb_pesan_tutup`, dan `ppdb_tahun_ajaran` dari `konten_web`.
3. Jika PPDB ditutup, halaman menampilkan pesan publik tanpa form. Middleware `ppdb.open` juga menolak POST langsung dengan HTTP 403 sebelum validasi dan upload dokumen.
4. Jika PPDB dibuka, form tiga langkah di Blade/Alpine mengumpulkan data siswa, orang tua, dan dokumen.
5. JavaScript submit ke `/api/pendaftaran` memakai `fetch` dan CSRF token.
6. `RegistrationController@store` memvalidasi input, mengupload dokumen, membuat record `siswa`, dan mencatat activity log.
7. Response sukses mengembalikan nomor pendaftaran dan URL cetak berbasis token.
8. User diarahkan untuk mencetak kartu lewat `/pendaftaran/cetak/{token}`.

## Alur Admin

1. Admin/operator login lewat `/admin/login`.
2. Laravel auth session dipakai untuk semua route `/admin`, ditambah pembatasan satu akun satu session aktif.
3. Operator boleh mengakses modul operasional.
4. Modul kelola admin dibatasi middleware `admin`.
5. Perubahan penting dicatat dengan `ActivityLogger`.

## Upload dan Asset

Saat ini `ImageHelper::uploadAndOptimize` menyimpan file ke folder di bawah `public_path()`.

- Banner: `public/uploads/banner`
- Guru: `public/uploads/guru`
- Fasilitas: `public/uploads/fasilitas`
- Kegiatan: `public/uploads/kegiatan`
- Dokumen PPDB: `storage/app/private/ppdb`
- Thumbnail dokumen PPDB admin: `storage/app/private/ppdb-thumbs`
- Logo kecil web: `public/logo-web.webp`

Struktur saat ini:

- Tetap publik: banner, guru, fasilitas, kegiatan.
- Private storage: akte, KK, KTP, ijazah, dan thumbnail preview admin untuk dokumen image.
- Preview dokumen pada admin PPDB memakai route `admin.ppdb.document.thumbnail`; file asli tetap dibuka lewat route `admin.ppdb.document`.

Command terkait media:

- `php artisan media:generate-variants`: membuat varian `card`/`hero` media publik.
- `php artisan ppdb:generate-document-thumbnails`: membuat thumbnail private dokumen PPDB image.
- `php artisan ppdb:migrate-public-documents`: memindahkan dokumen PPDB lama dari `public/uploads` ke private storage.

## Export Admin

Route `/admin/export/{type}` dipakai untuk export PPDB, siswa, dan guru. Export default memakai `App\Exports\AdminDataExport` berbasis Laravel Excel/PhpSpreadsheet untuk membuat file `.xlsx` asli, dengan title, metadata filter, header, freeze pane, auto filter, border, warna header, dan lebar kolom. Nilai seperti nomor pendaftaran, NISN, NIS, dan WhatsApp dipaksa sebagai string agar tetap terbaca sebagai teks saat dibuka di spreadsheet.

Export PPDB mengikuti filter admin: pencarian `q`, tahun ajaran, kelas, status, dan rentang tanggal.

## Testing Saat Ini

`phpunit.xml` mengatur testing memakai SQLite memory. Feature test proyek memakai `RefreshDatabase` untuk menyiapkan schema dan menjaga test tetap isolatif.
