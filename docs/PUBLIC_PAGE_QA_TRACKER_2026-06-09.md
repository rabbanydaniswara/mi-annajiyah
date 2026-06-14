# Public Page QA and Remediation Tracker

Tanggal mulai: 2026-06-09

## Tujuan

Mengaudit dan memperbaiki seluruh halaman publik, dengan prioritas pertama pagination halaman Kegiatan. Perubahan harus lulus browser QA desktop/mobile, automated test, dan build sebelum dipasang ke production.

## Aturan

- Data serta media baseline publik tidak boleh dihapus.
- Pengujian formulir yang membuat data dilakukan secara non-destructive atau memakai data QA yang dibersihkan kembali.
- Perbaikan dilakukan di lokal terlebih dahulu.
- Backup production wajib dibuat sebelum deployment.
- `siamiannajiyah.my.id` tidak boleh diubah.

## Phase A - Pemetaan dan Baseline

Status: `[x]` Selesai

- `[x]` Petakan route, controller, view, asset, dan fitur halaman publik.
- `[x]` Catat baseline visual dan fungsional pagination Kegiatan.
- `[x]` Audit console, broken image, overflow, heading, link, dan control accessibility.

## Phase B - QA Halaman Publik

Status: `[x]` Selesai

- `[x]` Homepage dan seluruh section.
- `[x]` Tenaga Pendidik dan modal/detail.
- `[x]` Fasilitas dan modal/detail.
- `[x]` Kegiatan: filter, pagination, lightbox, dan query string.
- `[x]` Pendaftaran: navigasi langkah dan validasi frontend.
- `[x]` Cek Pendaftaran: pencarian kosong dan tidak ditemukan.
- `[x]` Navbar, menu mobile, footer, external link, dan sitemap.
- `[x]` Viewport desktop, tablet, dan mobile.

## Phase C - Remediation

Status: `[x]` Selesai

- `[x]` Perbaiki pagination Kegiatan.
- `[x]` Perbaiki temuan fungsional, responsivitas, dan aksesibilitas yang terverifikasi.
- `[x]` Tambahkan regression test sesuai perubahan.

## Phase D - Verifikasi Lokal

Status: `[x]` Selesai

- `[x]` Full automated test dan test publik terfokus.
- `[x]` Pint, build, dan diff check.
- `[x]` Browser regression seluruh halaman publik pada desktop/tablet/mobile.

## Phase E - Production

Status: `[x]` Selesai

- `[x]` Backup production sebelum patch.
- `[x]` Deploy artifact yang sama dengan hasil QA lokal.
- `[x]` Browser QA production.
- `[x]` Verifikasi `miannajiyah.site` dan `siamiannajiyah.my.id`.
- `[x]` Update `TODO.md` dan dokumentasi akhir.

## Temuan Awal

1. Pagination Kegiatan memakai view default Laravel melalui `links()` dan belum memiliki komponen yang mengikuti desain publik.
2. Label pagination tampil sebagai key mentah `pagination.previous` dan `pagination.next`.
3. Nomor halaman versi desktop tidak terlihat karena class template pagination vendor tidak terdeteksi oleh build Tailwind proyek.
4. Perpindahan halaman mengembalikan pengguna ke hero, bukan ke daftar kegiatan.
5. Kartu kegiatan dan guru yang dapat diklik belum dapat dioperasikan melalui keyboard.
6. Modal kegiatan/guru belum memiliki role dialog dan label yang sesuai.
7. Modal guru homepage sudah tersedia di kode, tetapi tidak pernah diaktifkan oleh kartu guru.

## Hasil Perbaikan Lokal

- Pagination khusus publik menampilkan nomor halaman, halaman aktif, tombol sebelumnya/berikutnya, dan status halaman dalam Bahasa Indonesia.
- Link pagination mempertahankan filter kategori dan menuju anchor `#daftar-kegiatan`.
- Filter kategori tidak valid kembali menampilkan seluruh kegiatan.
- Kartu kegiatan/guru mendukung klik, `Enter`, `Space`, dan indikator fokus.
- Modal menggunakan `role="dialog"`, `aria-modal`, judul dialog, tombol tutup yang jelas, dan tombol Escape.
- Kartu guru homepage sekarang membuka modal profil yang sebelumnya tidak terhubung.
- `php artisan test`: 68 test lulus dengan 407 assertion.
- Test publik terfokus: 3 test lulus dengan 30 assertion.
- Pint, build, route/view cache, dan `git diff --check`: lulus.
- Browser regression lokal pada desktop, tablet, dan mobile lulus tanpa broken image, overflow, control tanpa label, raw pagination key, warning, atau console error.

## Hasil Deployment dan QA Production

- Backup sebelum patch: `/home/miak7156/backups/public-page-patch-before-20260609-084410`.
- Checksum backup app, build, dan daftar file baru terverifikasi.
- Patch controller, view publik, komponen pagination, dan build frontend berhasil dipasang.
- Browser QA production mencakup homepage, fasilitas, kegiatan, filter dan pagination, tenaga pendidik, pendaftaran, cek pendaftaran, dan kondisi data tidak ditemukan.
- Seluruh halaman diuji pada desktop, tablet, dan mobile tanpa broken image, overflow, control tanpa label, raw pagination key, warning, atau console error.
- Interaksi keyboard modal kegiatan dan guru, modal guru homepage, filter kategori, serta anchor pagination berhasil.
- Seluruh route publik utama dan sitemap merespons HTTPS 200.
- `siamiannajiyah.my.id` tetap HTTPS 200 dan direktori domain lama tidak diubah.
- Artifact staging production dan artifact sementara lokal telah dibersihkan; backup rollback dipertahankan.
