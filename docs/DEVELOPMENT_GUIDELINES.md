# Development Guidelines

## Setup Lokal

1. Install PHP 8.3+, Composer, Node.js, npm, dan database yang dipakai proyek.
2. Jalankan `composer install`.
3. Jalankan `npm install`.
4. Salin `.env.example` ke `.env`.
5. Atur koneksi database di `.env`.
6. Jalankan `php artisan key:generate`.
7. Jalankan `php artisan migrate --seed`.
8. Jalankan `npm run build` untuk asset production atau `npm run dev` saat development.
9. Jalankan `php artisan serve` untuk server lokal.

Catatan: saat audit, `.env.example` memakai SQLite, sedangkan `.env` lokal memakai MySQL. Pilih salah satu sebagai standar tim agar setup tidak membingungkan.

Catatan export Excel: PHP extension `zip` wajib aktif karena Laravel Excel/PhpSpreadsheet membutuhkannya untuk membuat file `.xlsx`.

## Perintah Harian

- Route list: `php artisan route:list`
- Test: `php artisan test`
- Build asset: `npm run build`
- Validasi Composer: `composer validate --strict`
- Audit PHP dependency: `composer audit`
- Audit Node dependency: `npm audit --audit-level=moderate`
- Clear config cache: `php artisan config:clear`
- Generate varian media publik: `php artisan media:generate-variants`
- Generate thumbnail dokumen PPDB private: `php artisan ppdb:generate-document-thumbnails`
- Migrasi dokumen PPDB lama dari public ke private: `php artisan ppdb:migrate-public-documents`

## Catatan Export Admin

Export default admin memakai `.xlsx` asli melalui `App\Exports\AdminDataExport` berbasis Laravel Excel/PhpSpreadsheet, bukan HTML table yang diberi ekstensi Excel. Saat mengubah export PPDB/siswa/guru, pastikan kolom identitas dan kontak tetap disimpan sebagai teks agar NISN, NIS, nomor pendaftaran, dan WhatsApp tidak berubah format.

Verifikasi minimal perubahan export:

- `php artisan test --filter=AdminExportTest`
- Download `/admin/export/ppdb` dari sesi admin lokal dan pastikan content type workbook Excel modern, ekstensi `.xlsx`, serta file diawali byte `PK`.
- Buka ulang file dengan PhpSpreadsheet/Excel dan pastikan NISN/WhatsApp tetap bertipe string.

## Standar Perubahan Backend

Gunakan pola controller yang sudah ada, tetapi untuk fitur baru yang mulai kompleks sebaiknya pindahkan validasi ke FormRequest. Untuk aksi admin, selalu catat perubahan penting melalui `ActivityLogger`.

Saat mengubah data `Siswa`, perhatikan bahwa tabel ini dipakai untuk dua konteks: pendaftar PPDB dan siswa aktif. Jangan menghapus field atau mengubah makna status tanpa migrasi dan penyesuaian view admin.

## Standar Perubahan Frontend

Frontend menggunakan Blade, Tailwind, Alpine, dan FontAwesome. Pertahankan style dan komponen yang sudah ada. Untuk form admin, gunakan CSRF, validasi server-side, dan konfirmasi modal pada aksi destructive.

Untuk pesan dari server ke DOM, hindari `innerHTML` jika pesannya berasal dari input user atau exception. Gunakan `textContent` atau template statis dengan text node.

## Testing yang Perlu Ditambahkan

Prioritas test:

1. Homepage berhasil render setelah migration testing.
2. Submit PPDB sukses dengan file upload palsu.
3. Validasi unique NISN/NIS.
4. Cetak kartu hanya bisa dengan token valid setelah perbaikan.
5. Cek status pendaftaran diberi throttle.
6. Operator tidak bisa mengakses kelola admin.
7. Admin bisa update status PPDB.
8. Route toggle memakai PATCH/POST dan menolak GET setelah perbaikan.
9. Export PPDB menghasilkan `.xlsx` asli dan menjaga nilai identitas/kontak sebagai teks.

## Deployment Checklist

Sebelum deploy:

1. Pastikan `APP_ENV=production`.
2. Pastikan `APP_DEBUG=false`.
3. Pastikan `APP_URL` memakai domain final dan HTTPS.
4. Pastikan `.env` tidak masuk repository.
5. Pastikan PHP extension `zip` aktif sebelum `composer install`.
6. Jalankan `composer install --no-dev --optimize-autoloader`.
7. Jalankan `npm ci` lalu `npm run build`, atau deploy hasil build yang valid.
8. Jalankan `php artisan migrate --force`.
9. Jalankan `php artisan config:cache`.
10. Jalankan `php artisan route:cache`; sitemap sudah memakai controller dan route cache sudah diuji.
11. Pastikan scheduler/queue worker aktif jika fitur queue dipakai.
12. Pastikan folder storage dan cache writable.
13. Jalankan `php artisan ppdb:migrate-public-documents` jika ada data lama yang masih menunjuk `public/uploads`.
14. Jalankan `php artisan ppdb:generate-document-thumbnails`.
15. Pastikan dokumen PPDB tidak berada di public path setelah perbaikan storage.

## Backlog Prioritas

### Security

- Ganti route cetak kartu dari numeric ID ke token acak.
- Pindahkan dokumen pendaftar ke private storage.
- Ubah toggle banner/fasilitas dari GET ke PATCH/POST.
- Tambahkan throttle untuk cek status pendaftaran.
- Hilangkan detail exception dari response publik.
- Update dependency PHP sampai `composer audit` bersih.

### Reliability

- Perbaiki test feature dengan `RefreshDatabase`.
- Rapikan migration index overlap.
- Dokumentasikan satu standar database development.
- Tambahkan handling upload gagal agar file parsial tidak tertinggal.

### Maintainability

- Pertimbangkan FormRequest untuk pendaftaran, guru, fasilitas, jadwal, dan admin user.
- Pisahkan service upload public image dan private document.
- Tambahkan policy/gate untuk role admin/operator.
- Pindahkan sitemap closure ke controller jika ingin memakai route cache.
