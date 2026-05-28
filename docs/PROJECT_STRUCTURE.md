# Struktur Proyek

## Gambaran Umum

Proyek ini adalah aplikasi web SPMB/PPDB MI Annajiyah berbasis Laravel. Aplikasi memiliki dua area utama:

- Area publik: homepage, formulir pendaftaran, cek status pendaftaran, profil guru, fasilitas, kegiatan, dan sitemap.
- Area admin: dashboard, kelola PPDB, konten website, jadwal, guru, fasilitas, siswa, admin user, export, dan log aktivitas.

## Stack

- Backend: Laravel 13, PHP 8.3
- Frontend: Blade, Tailwind CSS 4, Alpine.js, FontAwesome
- Build tool: Vite 8
- Database dev/testing: MySQL lokal untuk `.env`, SQLite memory untuk `phpunit.xml`
- Queue/cache/session: memakai driver database pada contoh konfigurasi

## Folder Penting

- `app/Http/Controllers`: controller publik dan admin.
- `app/Models`: model Eloquent untuk `Siswa`, `Guru`, `Jadwal`, `KontenWeb`, `Banner`, `Fasilitas`, `KegiatanSekolah`, `KegiatanKategori`, `ActivityLog`, dan `User`.
- `app/Helpers`: helper gambar dan activity log.
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
- `/api/pendaftaran`: submit form PPDB, POST, throttle `pendaftaran`.
- `/pendaftaran/cetak/{id}`: cetak kartu pendaftaran.
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
- `/admin/jadwal`: CRUD jadwal dan print.
- `/admin/guru`: CRUD guru.
- `/admin/fasilitas`: CRUD fasilitas.
- `/admin/siswa`: CRUD siswa.
- `/admin/admin-users`: CRUD admin/operator, dibatasi middleware `admin`.
- `/admin/export/{type}`: export siswa/PPDB/guru.

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
2. Form tiga langkah di Blade/Alpine mengumpulkan data siswa, orang tua, dan dokumen.
3. JavaScript submit ke `/api/pendaftaran` memakai `fetch` dan CSRF token.
4. `RegistrationController@store` memvalidasi input, mengupload dokumen, membuat record `siswa`, dan mencatat activity log.
5. Response sukses mengembalikan `id`.
6. User diarahkan untuk mencetak kartu lewat `/pendaftaran/cetak/{id}`.

Catatan audit: step 6 perlu diganti token acak agar tidak membuka data pendaftar lain.

## Alur Admin

1. Admin/operator login lewat `/admin/login`.
2. Laravel auth session dipakai untuk semua route `/admin`.
3. Operator boleh mengakses modul operasional.
4. Modul kelola admin dibatasi middleware `admin`.
5. Perubahan penting dicatat dengan `ActivityLogger`.

## Upload dan Asset

Saat ini `ImageHelper::uploadAndOptimize` menyimpan file ke folder di bawah `public_path()`.

- Banner: `public/uploads/banner`
- Guru: `public/uploads/guru`
- Fasilitas: `public/uploads/fasilitas`
- Kegiatan: `public/uploads/kegiatan`
- Dokumen PPDB: `public/uploads`

Rekomendasi struktur jangka panjang:

- Tetap publik: banner, guru, fasilitas, kegiatan.
- Private storage: akte, KK, KTP, ijazah.

## Testing Saat Ini

`phpunit.xml` mengatur testing memakai SQLite memory. Feature test bawaan gagal karena tabel aplikasi belum dimigrasi pada test. Untuk test feature, gunakan `RefreshDatabase` dan seed/factory minimal.
