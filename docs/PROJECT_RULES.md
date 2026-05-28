# Rules Proyek

Dokumen ini berisi aturan teknis yang sebaiknya diikuti setiap kali mengubah aplikasi SPMB Annajiyah.

## Keamanan Data

1. Jangan pernah membuat data pendaftar dapat diakses publik hanya dengan numeric ID.
2. Dokumen identitas seperti akte, KK, KTP, dan ijazah harus disimpan di private storage.
3. Akses dokumen pendaftar wajib lewat controller yang mengecek auth dan role.
4. Jangan tampilkan exception internal ke user.
5. Jangan commit `.env`, database dump produksi, atau file upload privat.
6. Password default tidak boleh dipakai di production.

## Route dan HTTP Method

1. GET hanya untuk membaca data.
2. Aksi yang mengubah data harus memakai POST, PUT, PATCH, atau DELETE dengan CSRF.
3. Route publik yang bisa dipakai untuk enumerasi data harus diberi throttle.
4. Route admin harus berada di prefix `/admin` dan minimal memakai middleware `auth`.
5. Aksi khusus super admin harus memakai middleware `admin`.

## Validasi Input

1. Semua input request wajib divalidasi di controller atau FormRequest.
2. Validasi upload harus mengecek ekstensi, MIME type, ukuran, dan konteks penggunaan.
3. Field yang punya constraint database unik harus punya validasi unik yang sesuai untuk create dan update.
4. Jangan memakai `$request->all()` untuk mass assignment.
5. Gunakan `$request->only()` atau array eksplisit.

## Database dan Migration

1. Migration tidak boleh menyembunyikan error dengan `catch` kosong.
2. Index harus diberi nama eksplisit jika ada kemungkinan overlap lintas database.
3. Jangan membuat migration yang menduplikasi index migration sebelumnya.
4. Perubahan destructive harus dibuat reversible atau diberi catatan jelas.
5. Seeder production tidak boleh membuat akun default dengan password mudah ditebak.

## View dan Frontend

1. Hindari `{!! !!}` kecuali HTML sudah disanitasi.
2. Data dari server yang dimasukkan ke JavaScript DOM sebaiknya memakai `textContent`, bukan `innerHTML`.
3. Link `target="_blank"` sebaiknya menambahkan `rel="noopener noreferrer"`.
4. Form destructive harus tetap punya konfirmasi dan method spoofing yang benar.
5. Jangan menaruh logic query database berat di view.

## Upload Gambar dan File

1. File publik boleh memakai `public/uploads` hanya jika memang aman dilihat semua orang.
2. File dokumen pendaftar harus private.
3. Helper upload harus dipisah antara public image dan private document.
4. Hapus file lama hanya setelah file baru berhasil tersimpan.
5. Hindari `@unlink` tanpa logging jika penghapusan file gagal penting untuk audit.

## Testing dan Verifikasi

1. Sebelum rilis, jalankan `composer validate --strict`.
2. Jalankan `composer audit` dan selesaikan advisory high/medium.
3. Jalankan `npm audit --audit-level=moderate`.
4. Jalankan `php artisan test`.
5. Jalankan `npm run build`.
6. Jalankan `php -l` atau Pint/static check sebelum merge.
7. Untuk perubahan route, cek `php artisan route:list`.

## Dokumentasi

1. Update dokumen di `docs` jika mengubah alur pendaftaran, storage, route utama, role, atau deployment.
2. Catat migration penting dan dampaknya ke data existing.
3. Jika ada workaround sementara, beri batas waktu atau tiket follow-up.
