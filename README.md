<div align="center">
  <h1 align="center">SPMB / PPDB MI Annajiyah</h1>
  <p align="center">
    Aplikasi Sistem Penerimaan Peserta Didik Baru (PPDB) Online berbasis Laravel.
    <br />
    <br />
    <a href="#fitur-utama">Fitur Utama</a>
    ·
    <a href="#teknologi-yang-digunakan">Teknologi</a>
    ·
    <a href="#panduan-instalasi">Panduan Instalasi</a>
  </p>
</div>

---

## 📖 Tentang Proyek

**SPMB / PPDB MI Annajiyah** adalah sebuah aplikasi web yang dirancang khusus untuk memfasilitasi proses Penerimaan Peserta Didik Baru secara online di lingkungan MI Annajiyah. Aplikasi ini mempermudah calon siswa dan orang tua dalam melakukan pendaftaran, melihat informasi sekolah, serta mengecek status kelulusan. 

Sistem ini juga dilengkapi dengan panel administrasi untuk mengelola data pendaftar, konten halaman publik, dan memantau seluruh alur PPDB.

## ✨ Fitur Utama

- **Halaman Publik Informatif**: Menampilkan profil sekolah, daftar tenaga pendidik, fasilitas, dan dokumentasi kegiatan.
- **Pendaftaran Online**: Formulir pendaftaran online yang komprehensif dan mudah digunakan oleh calon siswa.
- **Cek Status Pendaftaran**: Calon siswa dapat mengecek status pendaftaran dan pengumuman secara mandiri.
- **Manajemen Konten (CMS)**: Admin dapat memperbarui konten halaman publik (banner, berita, guru, fasilitas) langsung melalui panel dashboard.
- **Multi-Role Access**: Sistem role untuk **Admin** dan **Operator** dengan hak akses yang disesuaikan.
- **Responsive Design**: Tampilan yang optimal diakses melalui desktop, tablet, maupun perangkat mobile.

## 💻 Teknologi yang Digunakan

Proyek ini dibangun menggunakan teknologi modern untuk memastikan performa yang cepat, aman, dan mudah dikembangkan:

- **Framework**: [Laravel 12](https://laravel.com/)
- **Frontend**: [Tailwind CSS](https://tailwindcss.com/), Alpine.js (opsional/bila ada), Blade Templates
- **Asset Bundler**: [Vite](https://vitejs.dev/)
- **Database**: MySQL / SQLite
- **Ikon**: FontAwesome

## 🚀 Panduan Instalasi

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi di komputer lokal (Local Development).

### Prasyarat
Pastikan sistem kamu sudah terinstal:
- PHP >= 8.2
- Composer
- Node.js & NPM
- MySQL / MariaDB (Jika tidak menggunakan SQLite)

### Langkah-langkah

1. **Clone repositori**
   ```bash
   git clone https://github.com/username/spmb-annajiyah-laravel.git
   cd spmb-annajiyah-laravel
   ```

2. **Install dependensi backend**
   ```bash
   composer install
   ```

3. **Install dependensi frontend & build asset**
   ```bash
   npm install
   npm run build
   ```
   *(Catatan: Saat melakukan development aktif, gunakan `npm run dev`)*

4. **Konfigurasi Environment**
   Duplikat file `.env.example` menjadi `.env` lalu sesuaikan konfigurasi database.
   ```bash
   cp .env.example .env
   ```

5. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

6. **Migrasi Database & Seeding**
   Jalankan migrasi untuk membuat struktur tabel dan mengisi data awal (dummy/pengaturan).
   ```bash
   php artisan migrate --seed
   ```

7. **Jalankan Aplikasi**
   ```bash
   php artisan serve
   ```
   Aplikasi dapat diakses melalui `http://localhost:8000`

---

## 🔒 Catatan Penting
- **Keamanan Data**: File pendaftaran yang bersifat sensitif akan disimpan secara privat (`storage/app/private/ppdb`) dan tidak dapat diakses langsung oleh publik.
- Jika kamu adalah pengembang (atau AI Agent) yang berkontribusi, harap merujuk ke dokumen teknis internal seperti `TODO.md` dan file panduan di folder `docs/` (atau lihat file `README_INTERNAL.md`).
