# Audit Menyeluruh Proyek MI Annajiyah

Tanggal audit: 2026-06-01

Dokumen ini merangkum audit teknis bertahap untuk aplikasi SPMB/PPDB MI Annajiyah. Audit dilakukan pada kondisi lokal dengan server `http://127.0.0.1:8000`, database MySQL lokal aktif, dan beberapa perubahan proyek masih belum di-commit.

## Cakupan Audit

- Baseline Git dan kebersihan file proyek.
- Dependency PHP dan Node.
- Route, middleware, autentikasi, role, dan session admin.
- Database, migration, data publik, dokumen PPDB, dan media.
- Upload file, varian gambar, dan file private.
- Frontend publik/admin melalui browser.
- Performa dasar asset, gambar, dan halaman berat.
- Testing, build, cache, dan kesiapan dokumentasi.

## Verifikasi Yang Dijalankan

- `php artisan test` -> 30 passed, 131 assertions.
- `npm run build` -> berhasil.
- `composer validate --strict` -> valid.
- `composer audit` -> tidak ada advisory.
- `npm audit --audit-level=moderate` -> 0 vulnerabilities.
- `php artisan route:list --except-vendor` -> 48 route terdaftar.
- `php artisan route:cache` lalu `php artisan route:clear` -> berhasil.
- `php artisan config:cache` lalu `php artisan config:clear` -> berhasil.
- `php artisan view:cache` lalu `php artisan view:clear` -> berhasil.
- `php artisan migrate:status` -> semua migration berstatus `Ran`.
- Browser check halaman publik: `/`, `/pendaftaran`, `/cek-pendaftaran`, `/tenaga-pendidik`, `/fasilitas`, `/kegiatan`.
- Browser check halaman admin: `/admin`, `/admin/ppdb`, `/admin/konten`, `/admin/jadwal`, `/admin/guru`, `/admin/fasilitas`, `/admin/siswa`, `/admin/admin-users`.
- Pemeriksaan konsistensi media/database: tidak ada referensi gambar, varian gambar, atau dokumen PPDB yang hilang.
- `sitemap.xml` lokal memberi HTTP 200.

## Ringkasan Kondisi Saat Ini

Secara umum aplikasi berjalan baik. Test dan build hijau, route admin terlindungi middleware `auth` dan `single.admin.session`, dokumen PPDB tersimpan di disk private, dan media publik yang direferensikan database tersedia.

Namun, ada beberapa temuan yang perlu ditindaklanjuti sebelum aplikasi dianggap benar-benar rapi untuk pengembangan lanjutan atau deployment production.

## Temuan Prioritas

### High

1. Worktree masih berisi banyak perubahan aktif yang belum di-commit.
   - Dampak: rawan salah push, perubahan penting bisa tertinggal, dan file sementara dapat ikut masuk staging.
   - Kondisi saat audit: terdapat perubahan fitur session admin, perbaikan guard HTTPS asset, laporan Word, TODO, dokumen internal, serta perubahan media guru.
   - Catatan khusus: file lock Word `~$l 4_LaporanAkhirRPL.docx` sempat terdeteksi oleh Git sebagai untracked saat dokumen Word aktif. File seperti ini tidak boleh ikut commit bila muncul lagi.
   - Rekomendasi: pisahkan commit menjadi kelompok kecil: fitur session, perbaikan HTTPS asset, dokumen/TODO, laporan Word bila memang ingin disimpan di repo, dan asset guru yang benar-benar dipakai.

2. Konfigurasi environment masih local/debug tetapi memakai domain online.
   - Bukti: `.env` berisi `APP_ENV=local`, `APP_DEBUG=true`, dan `APP_URL=https://spmb.moodybycaz.my.id`.
   - Dampak: jika konfigurasi ini dipakai untuk akses publik, error debug dapat membuka detail internal aplikasi.
   - Rekomendasi: untuk production nanti gunakan `APP_ENV=production`, `APP_DEBUG=false`, `SESSION_SECURE_COOKIE=true`, dan pastikan domain final sudah sesuai.

3. Manajemen akun admin/operator masih perlu hardening.
   - Bukti: `app/Http/Controllers/Admin/AdminController.php:65` masih memakai minimal password 6 karakter untuk akun baru.
   - Bukti: `app/Http/Controllers/Admin/AdminController.php:77-85` hanya mencegah penghapusan akun dengan username `admin`, belum mencegah penghapusan akun sendiri atau admin terakhir.
   - Dampak: risiko password lemah dan risiko lockout jika akun admin penting dihapus/didemote.
   - Rekomendasi: tingkatkan minimal password ke 8 atau 12 karakter, tambah konfirmasi password, cegah delete current user, dan cegah sistem kehilangan role `admin` terakhir.

### Medium

4. Beberapa endpoint konten admin belum memiliki validasi input yang lengkap.
   - Bukti: `app/Http/Controllers/Admin/KontenController.php:47-59` menerima `tipe`, `konten_items`, dan `konten` tanpa whitelist/validasi kuat untuk jalur umum.
   - Bukti: `app/Http/Controllers/Admin/KontenController.php:165-170` update banner memakai request langsung sebelum validasi eksplisit.
   - Dampak: data konten bisa menjadi tidak konsisten jika request salah/termanipulasi.
   - Rekomendasi: tambahkan validasi per mode update, whitelist `tipe`, validasi `banner_id`, judul, subtitle, urutan, dan file edit banner.

5. Form admin siswa belum memvalidasi uniqueness NISN/NIS di level form.
   - Bukti: `app/Http/Controllers/Admin/SiswaController.php:72-80` belum memakai rule `unique` untuk `nisn` dan `nis`, sedangkan database punya unique index.
   - Dampak: input duplikat dari panel admin berpotensi menghasilkan database exception, bukan pesan validasi yang ramah.
   - Rekomendasi: tambahkan rule unique dengan pengecualian id saat edit.

6. Penghapusan/penggantian media belum membersihkan semua varian gambar.
   - Bukti: controller menghapus original dan thumbnail, tetapi varian `*_card.webp` dan `*_hero.webp` belum ikut dihapus, misalnya `GuruController.php:56-58`, `FasilitasController.php:41-43`, dan `KontenController.php:173-175`.
   - Dampak: file varian lama dapat menjadi orphan dan menumpuk di `public/uploads`.
   - Rekomendasi: tambah helper `deleteVariants()` atau `deleteImageSet()` untuk menghapus original, thumb, card, dan hero secara konsisten.

7. Dashboard admin masih bergantung pada CDN Chart.js.
   - Bukti awal: dashboard admin memuat Chart.js dari CDN eksternal.
   - Status perbaikan: dashboard memakai chart Blade/CSS sederhana tanpa CDN eksternal.
   - Dampak: chart dapat gagal tampil jika internet lambat, CDN diblokir, atau koneksi hosting terbatas.
   - Rekomendasi: pasang Chart.js melalui npm dan bundle lewat Vite, atau buat fallback ringkas tanpa chart eksternal.

8. Placeholder gambar hero terdeteksi sebagai broken image oleh browser audit.
   - Bukti awal: helper gambar memakai data GIF placeholder yang tidak valid.
   - Status perbaikan: placeholder transparent pixel sudah diganti ke data URI GIF 1x1 yang valid.
   - Bukti pemakaian: `resources/views/public/index.blade.php:21`.
   - Dampak: bukan broken image konten, tetapi audit browser menandainya sebagai gambar rusak dan bisa menimbulkan ikon broken pada browser tertentu.
   - Rekomendasi: ganti dengan transparent GIF valid atau gunakan placeholder SVG/data URI yang valid.

9. Mobile layout masih memiliki horizontal overflow di beberapa halaman.
   - Bukti browser audit mobile:
     - Beranda: `scrollWidth 399` pada `clientWidth 375`.
     - Kelola PPDB admin: `scrollWidth 951` pada `clientWidth 375`.
   - Penyebab awal:
     - Beranda: elemen dekoratif absolute dan animasi `reveal-left/right` dapat melebar keluar viewport.
     - Admin PPDB: tabel/data padat dan beberapa container mengikuti lebar konten desktop.
   - Rekomendasi: tambah containment seperti `overflow-x-hidden` pada section yang memakai dekorasi absolute, pastikan admin mobile memakai wrapper scroll internal, dan uji ulang viewport 390px.

10. Halaman Kelola Jadwal admin memuat banyak image tag sekaligus.
    - Bukti browser audit: `/admin/jadwal` memuat 192 image tag.
    - Penyebab: table/grid mode dirender sekaligus, termasuk foto guru pada daftar jadwal.
    - Dampak: admin jadwal bisa terasa berat jika data jadwal/foto bertambah.
    - Rekomendasi: gunakan thumbnail kecil/lazy loading pada foto jadwal, atau hindari render semua foto pada mode yang sedang tersembunyi.

11. Beberapa asset upload asli masih besar dan sebagian masih tracked.
    - Contoh tracked besar: beberapa file WebP kegiatan berada di kisaran 1-2 MB, dan beberapa original JPEG lokal berada di atas 3-5 MB meskipun di-ignore.
    - Dampak: repo dan halaman dapat membesar jika original dipakai di lightbox/modal atau ikut commit sebelum dioptimalkan.
    - Rekomendasi: pertahankan varian `card/hero` untuk tampilan utama, evaluasi apakah original besar perlu tetap tracked, dan buat aturan ukuran maksimum asset baseline.

12. Dokumentasi lama masih tidak sinkron dengan dependency aktual.
    - Bukti awal: `docs/PROJECT_AUDIT.md` dan `docs/PROJECT_STRUCTURE.md` masih menyebut stack major lama.
    - Status perbaikan: dokumen struktur/audit sudah disinkronkan ke Laravel 12, PHP `^8.2`, Vite 6, dan Tailwind 4.
    - Kondisi aktual: Laravel 12.61.0, PHP 8.3.30 lokal, Vite 6.4.2.
    - Dampak: agent/developer lain bisa mengambil keputusan salah.
    - Rekomendasi: sinkronkan dokumen stack utama ke Laravel 12, PHP `^8.2`, Vite 6, Tailwind 4.

### Low

13. Build asset cukup wajar, tetapi masih bisa lebih ramping.
    - Public CSS sekitar 75 KB, admin CSS sekitar 142 KB, Alpine/module sekitar 45 KB, FontAwesome solid/brands sekitar 220 KB total.
    - Dampak: masih aman, tetapi FontAwesome penuh dapat terasa mahal untuk koneksi lambat.
    - Rekomendasi: untuk optimasi lanjutan, pertimbangkan subset icon atau icon lokal yang benar-benar dipakai.

14. Dokumen Word laporan proyek masuk sebagai untracked file.
    - Dampak: tidak bermasalah jika memang ingin versioning laporan, tetapi file `.docx` binary akan membuat diff Git tidak mudah dibaca.
    - Rekomendasi: putuskan apakah laporan Word perlu ikut repo. Jika iya, commit secara sadar. Jika tidak, masukkan pola laporan/temporary Word ke `.gitignore`.

## Hal Yang Sudah Baik

- Test aplikasi saat ini hijau.
- Build Vite berhasil.
- `composer audit` dan `npm audit` bersih.
- Route admin utama memakai `auth` dan `single.admin.session`.
- Route manajemen admin/operator memakai middleware `admin`.
- Dokumen PPDB tersimpan pada disk local/private, bukan public upload.
- Public check status tidak menampilkan catatan internal/dokumen sensitif berdasarkan test.
- Database lokal sudah memiliki migration `active_session_id`.
- Media database saat audit tidak broken dan varian gambar utama tersedia.
- `route:cache`, `config:cache`, dan `view:cache` dapat dijalankan.

## Rekomendasi Tahapan Perbaikan

### Tahap 1 - Rapikan Baseline dan Dokumen

- Pisahkan perubahan Git yang belum commit.
- Hapus file lock Word dari working tree.
- Sinkronkan dokumen yang masih menyebut stack major lama.
- Tentukan apakah laporan `.docx` masuk repo atau disimpan di luar repo.

### Tahap 2 - Hardening Admin

- Perkuat aturan password admin/operator.
- Cegah delete current user dan cegah hilangnya admin terakhir.
- Tambahkan test untuk skenario lockout admin.

### Tahap 3 - Validasi Input Admin

- Lengkapi validasi `KontenController`.
- Lengkapi validasi unique NISN/NIS pada `SiswaController`.
- Tambahkan test validasi untuk input admin yang rawan.

### Tahap 4 - Media Cleanup dan Performa

- Buat helper penghapusan image set: original, thumb, card, hero.
- Perbaiki transparent pixel placeholder.
- Evaluasi asset upload besar dan aturan ukuran baseline.
- Pertimbangkan bundling Chart.js via Vite.

### Tahap 5 - UI Responsif

- Perbaiki overflow horizontal mobile pada beranda.
- Rapikan mobile admin PPDB agar scroll tabel terjadi di area tabel, bukan seluruh halaman.
- Uji ulang viewport 390px dan desktop 1280px.

### Tahap 6 - Production Readiness Nanti

- Finalisasi `.env` production.
- Pastikan `APP_DEBUG=false`.
- Set HTTPS cookie.
- Jalankan checklist hosting dan backup.
