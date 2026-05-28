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

## Perintah Harian

- Route list: `php artisan route:list`
- Test: `php artisan test`
- Build asset: `npm run build`
- Validasi Composer: `composer validate --strict`
- Audit PHP dependency: `composer audit`
- Audit Node dependency: `npm audit --audit-level=moderate`
- Clear config cache: `php artisan config:clear`
- Generate thumbnail: `php artisan thumbnails:generate`
- Regenerate thumbnail: `php artisan thumbnails:regenerate`

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

## Deployment Checklist

Sebelum deploy:

1. Pastikan `APP_ENV=production`.
2. Pastikan `APP_DEBUG=false`.
3. Pastikan `APP_URL` memakai domain final dan HTTPS.
4. Pastikan `.env` tidak masuk repository.
5. Jalankan `composer install --no-dev --optimize-autoloader`.
6. Jalankan `npm ci` lalu `npm run build`, atau deploy hasil build yang valid.
7. Jalankan `php artisan migrate --force`.
8. Jalankan `php artisan config:cache`.
9. Jalankan `php artisan route:cache` jika route closure sitemap sudah dipindah ke controller. Saat ini ada closure di `routes/web.php`, jadi route cache perlu diuji dulu.
10. Pastikan scheduler/queue worker aktif jika fitur queue dipakai.
11. Pastikan folder storage dan cache writable.
12. Pastikan dokumen PPDB tidak berada di public path setelah perbaikan storage.

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
