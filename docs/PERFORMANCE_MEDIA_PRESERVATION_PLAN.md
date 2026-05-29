# Rencana Optimasi Performa dan Preservasi Media

Tanggal dibuat: 2026-05-29

Tujuan: membuat website lebih ringan untuk koneksi lambat tanpa menghapus data, gambar, dan foto yang sudah menjadi baseline aplikasi.

## Status Legend

- `[ ]` Belum dikerjakan
- `[~]` Sedang dikerjakan
- `[x]` Selesai
- `[!]` Perlu keputusan / tertahan

## Keputusan Baseline

- `[x]` Data publik dan media saat ini ditetapkan sebagai baseline proyek.
- `[x]` Path gambar/foto baseline disimpan melalui seeder database, bukan hanya bergantung pada tampilan statis.
- `[x]` Seeder dibuat preservatif: menjalankan `php artisan db:seed` tidak boleh menghapus atau menimpa data publik yang sudah ada.
- `[x]` Jadwal pelajaran tidak dibuat ulang jika tabel `jadwal` sudah berisi data.
- `[!]` Backup database dan `public/uploads` tetap wajib dibuat sebelum optimasi gambar massal yang menghapus/mengganti file lama.

## Data dan Media yang Dipertahankan

Baseline data berada di:

- `database/seeders/DatabaseSeeder.php`
  - Konten web: visi, misi, sejarah, alamat, telepon, email, jam operasional, tahun ajaran PPDB.
  - Banner publik: `uploads/banner/banner1.webp` dan `uploads/banner/banner2.webp`.
- `database/seeders/NewFeaturesSeeder.php`
  - Kategori kegiatan.
  - Fasilitas sekolah beserta gambar WebP.
  - Data guru beserta foto WebP.
  - Kegiatan sekolah beserta gambar WebP.
  - Data simulasi siswa/PPDB.
- `database/seeders/JadwalSeeder.php`
  - Jadwal awal hanya dibuat jika tabel jadwal masih kosong.

Aturan penting:

- Seeder boleh menambahkan baseline yang belum ada.
- Seeder tidak boleh mengosongkan tabel publik seperti `guru`, `fasilitas`, `kegiatan_sekolah`, `kegiatan_kategori`, `banner`, `konten_web`, atau `jadwal`.
- Seeder tidak boleh menimpa edit manual admin/operator yang sudah ada di database.
- Optimasi gambar berikutnya harus menambah varian ringan atau mengganti referensi dengan sadar, bukan menghapus file lama tanpa backup.

## Temuan Performa Saat Ini

- JavaScript publik kecil dan bukan sumber beban utama.
- CSS publik sudah dipisahkan dari kebutuhan admin. Halaman publik memakai subset FontAwesome ringan, sedangkan admin tetap memakai FontAwesome penuh karena ada input ikon dan kebutuhan panel internal.
- Beban terbesar ada pada media publik:
  - Beberapa gambar kegiatan masih sekitar 1-2 MB.
  - Banyak card publik memakai gambar full-size, padahal thumbnail sudah tersedia.
  - Total media publik WebP cukup besar untuk pengguna internet lambat.
- Query publik sudah dibuat lebih ringan dengan cache, `withCount`, dan pengurangan data yang tidak dipakai.
- Production caching sudah disiapkan secara teknis, tetapi konfigurasi `.env` production tetap dilakukan saat nanti benar-benar deploy.

## Target Optimasi

- Homepage tetap informatif, tetapi initial load dibuat ringan.
- Card kegiatan/guru/fasilitas memakai thumbnail atau varian card.
- Gambar hero memakai varian khusus yang lebih kecil.
- Full image hanya dipakai untuk detail, modal, atau kebutuhan inspeksi.
- Konten publik yang jarang berubah memakai cache aplikasi.
- Asset statis memakai cache browser saat production.

## Fase 1 - Optimasi Gambar Publik

- `[!]` Buat backup database dan folder `public/uploads`.
- `[x]` Inventaris semua gambar yang dipakai oleh banner, guru, fasilitas, dan kegiatan.
- `[x]` Buat varian gambar:
  - Hero: lebar sekitar 1400-1600 px.
  - Card: lebar sekitar 420-640 px sesuai jenis konten.
  - Thumbnail: 200x200 px untuk admin/list kecil.
- `[x]` Gunakan varian card/thumbnail pada halaman publik:
  - Homepage kegiatan.
  - Homepage guru.
  - Homepage fasilitas tidak memuat gambar sehingga tetap ringan.
  - Halaman kegiatan.
  - Halaman guru.
  - Halaman fasilitas.
- `[x]` Pertahankan file baseline sampai hasil QA dan backup aman.

Target selesai:

- Card image mayoritas di bawah 100 KB per gambar.
- Hero image di bawah 300 KB.
- Tidak ada broken image di halaman publik.

Catatan implementasi:

- Command generator: `php artisan media:generate-variants`.
- Helper fallback: `ImageHelper::getCard()` dan `ImageHelper::getHero()`.
- File asli WebP/JPG lama tidak dihapus oleh proses optimasi ini.

## Fase 2 - Loading Strategy

- `[x]` Ubah hero dari CSS background penuh menjadi markup gambar.
- `[x]` Banner pertama diberi prioritas load, banner lain lazy lewat `data-hero-src`.
- `[x]` Tambahkan `width`, `height`, `loading`, dan `decoding` pada gambar publik utama.
- `[x]` Pastikan layout memakai ukuran tetap/aspect ratio pada card gambar.

Target selesai:

- Pengguna koneksi lambat tetap melihat konten utama cepat.
- Scroll halaman tidak terasa tersendat karena gambar besar.

## Fase 3 - Optimasi Query dan Cache

- `[x]` Cache konten web yang dipakai layout publik.
- `[x]` Cache data homepage yang jarang berubah.
- `[x]` Ganti hitung kategori kegiatan dengan `withCount`.
- `[x]` Hapus query/data yang tidak dipakai.
- `[x]` Tambahkan mekanisme clear cache setelah admin mengubah konten publik.

Target selesai:

- Render halaman publik lebih ringan.
- Query database tidak bertambah besar saat jumlah kegiatan meningkat.

## Fase 4 - Optimasi Asset CSS dan Font

- `[x]` Audit icon yang benar-benar dipakai.
- `[x]` Kurangi penggunaan FontAwesome penuh pada halaman publik.
- `[x]` Pisahkan kebutuhan icon admin dan publik:
  - Publik memakai `resources/css/fontawesome-public.css`.
  - Admin tetap memakai `@fortawesome/fontawesome-free/css/all.min.css`.
- `[x]` Ganti ikon brand footer publik menjadi SVG inline agar halaman publik tidak perlu memuat font brand.
- `[x]` Batasi scan Tailwind publik ke view publik dan layout publik saja.
- `[x]` Jalankan ulang `npm run build` dan cek ukuran asset.

Target selesai:

- Font/icon yang tidak dipakai tidak ikut membebani halaman publik.

Hasil build terakhir:

- CSS publik: 76.99 KB raw / 12.65 KB gzip.
- JS publik: 0.55 KB raw / 0.34 KB gzip.
- CSS admin: 145.74 KB raw / 33.46 KB gzip.
- Halaman publik tidak mereferensikan `fa-brands-400`, `fa-regular-400`, atau `fa-v4compatibility`.
- Jika nanti menambah ikon fasilitas publik di luar baseline, tambahkan mapping ke `resources/css/fontawesome-public.css` atau gunakan ikon yang sudah ada di subset.

## Fase 5 - Production Readiness Saat Hosting

- `[!]` Set `APP_ENV=production` saat deploy hosting, bukan di lokal sekarang.
- `[!]` Set `APP_DEBUG=false` saat deploy hosting, bukan di lokal sekarang.
- `[!]` Jalankan `php artisan config:cache` saat deploy hosting.
- `[x]` Uji `php artisan view:cache`.
- `[x]` Cache view dikembalikan clear setelah pengujian agar development lokal tetap fleksibel.
- `[x]` Route closure sitemap dirapikan menjadi `SitemapController`.
- `[x]` Uji `php artisan route:cache`.
- `[x]` Cache route dikembalikan clear setelah pengujian agar development lokal tetap fleksibel.
- `[x]` Gzip/cache header untuk asset statis sudah tersedia di `public/.htaccess`.
- `[x]` Command deployment production didokumentasikan di `docs/HOSTING_READINESS.md`.

Target selesai:

- Aplikasi siap dibuka lebih cepat di hosting umum.
- Asset statis tidak diunduh ulang terus-menerus oleh browser.

## QA Wajib Setelah Optimasi

- `[x]` Homepage tampil dengan gambar.
- `[x]` Halaman fasilitas tampil dengan gambar.
- `[x]` Halaman kegiatan tampil dengan gambar.
- `[x]` Halaman guru tampil dengan gambar.
- `[x]` Alur pendaftaran tetap bisa submit.
- `[x]` Tidak ada broken image aktual.
- `[x]` Seeder bisa dijalankan ulang tanpa mengurangi jumlah data publik.
- `[x]` `php artisan test` lulus.
- `[x]` `npm run build` lulus.

Catatan QA:

- QA browser sederhana dijalankan pada 2026-05-29 melalui server lokal `http://127.0.0.1:8000`.
- Screenshot QA tersimpan lokal di `storage/app/qa-screenshots`.
- Halaman yang dicek: homepage, fasilitas, kegiatan, tenaga pendidik, dan kartu cetak pendaftaran.
- Submit pendaftaran simulasi berhasil dan menghasilkan nomor `PPDB-2026-0013`.
- Cek seeder preservatif: `konten_web`, `banner`, `guru`, `fasilitas`, `kegiatan_kategori`, `kegiatan_sekolah`, `jadwal`, dan `siswa` tidak berkurang setelah `php artisan db:seed`.

## Catatan Eksekusi

- Jangan menjalankan optimasi massal media tanpa backup.
- Jangan menghapus file JPG/WebP lama sebelum semua referensi database dan view dicek.
- Jika ada perubahan nama file gambar, ubah melalui database/seeder secara eksplisit.
- Jika data sudah pernah diedit lewat admin, seeder tidak boleh dipakai untuk memaksa data kembali ke versi lama.
- Untuk deployment, upload hanya file yang dibutuhkan web: kode aplikasi, `public/build`, media WebP/thumbnail yang dipakai, dan file konfigurasi production yang sesuai.
