# App Improvement Roadmap

Tanggal update: 2026-05-28  
Status hosting: ditunda. Fokus saat ini adalah memperbaiki dan mengembangkan aplikasi/web sebelum production deploy.

## Ringkasan Kondisi

Fondasi teknis utama sudah diperbaiki:

- Dependency sudah diarahkan ke stack aman Laravel 12 + PHP `^8.2`.
- Dokumen PPDB sudah masuk private storage.
- Cetak kartu memakai token acak.
- Route toggle admin sudah memakai method aman.
- Test utama untuk PPDB, role admin/operator, seeder, dan route safety sudah tersedia.
- Migration index dan seeder admin default sudah dirapikan.

## Fokus Berikutnya

### 1. Workflow PPDB

Tujuan: membuat PPDB lebih cocok untuk kerja panitia sehari-hari.

Prioritas:

- Tahun ajaran PPDB aktif. Selesai: dapat diatur dari admin dan tersimpan sebagai snapshot di pendaftar baru.
- Nomor pendaftaran publik yang rapi. Selesai: memakai format `PPDB-YYYY-0001`, bukan ID database.
- Status PPDB yang lebih lengkap.
- Catatan verifikasi internal.
- Filter dan export PPDB yang lebih kuat.

### 2. Admin dan Operator

Tujuan: membuat hak akses dan pekerjaan admin lebih jelas.

Prioritas:

- Matriks role admin/operator.
- Ganti password user login.
- Dashboard ringkas PPDB.
- Filter log aktivitas.
- Bulk action untuk data PPDB jika dibutuhkan.

### 3. UX Pendaftaran Publik

Tujuan: mengurangi error input wali murid dan membuat proses daftar lebih nyaman.

Prioritas:

- Format nomor WhatsApp konsisten.
- Preview file sebelum submit.
- Pesan validasi yang lebih ramah.
- Informasi kontak dan CTA yang lebih jelas.

### 4. Konten dan Media

Tujuan: konten sekolah mudah dirawat tanpa mengganggu data sensitif.

Prioritas:

- Rapikan manajemen media banner, guru, kegiatan, dan fasilitas.
- Perbaiki warning font Inter saat build.
- Evaluasi teks homepage, alamat, kontak, dan media sosial.
- Konsistenkan thumbnail dan penghapusan file.

### 5. Kualitas dan Keamanan Lanjutan

Tujuan: menjaga aplikasi tetap stabil saat fitur bertambah.

Prioritas:

- Test update status PPDB.
- Test CRUD konten utama.
- Refactor upload publik vs dokumen private jika mulai membesar.
- Prosedur backup lokal sebelum migration besar.

## Hosting

Hosting belum dikerjakan sekarang. Saat nanti sudah siap, gunakan checklist `HOSTING_READINESS.md` dan bagian Fase 10 di `REMEDIATION_TRACKER.md`.

Sebelum hosting wajib ada:

- Domain dan paket hosting final.
- Backup database dan upload.
- `.env` production final.
- Full verification lulus.
- Document root mengarah ke `public`.
