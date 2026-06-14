# Rencana Perbaikan Temuan Audit

Tanggal dibuat: 2026-06-01

Dokumen ini menjadi rencana kerja untuk menindaklanjuti temuan pada `docs/FULL_PROJECT_AUDIT_2026-06-01.md`. Perbaikan disusun bertahap agar risiko perubahan kecil, mudah diuji, dan tidak mencampur urusan keamanan, performa, UI, dan dokumentasi dalam satu langkah besar.

## Prinsip Pengerjaan

- Jangan commit/push tanpa izin user.
- Pisahkan perubahan berdasarkan tema agar mudah diperiksa.
- Jalankan test sesuai dampak perubahan.
- Jangan hapus data publik, media baseline, atau dokumen PPDB tanpa instruksi eksplisit.
- Setelah setiap tahap selesai, update `TODO.md`.
- Jika menyentuh media, pastikan database dan file fisik tetap konsisten.

## Tahap 0 - Rapikan Baseline Kerja

Tujuan: memastikan perubahan aktif tidak tercampur sebelum memperbaiki temuan audit.

Checklist:

- `[x]` Cek ulang `git status --short --branch`.
- `[x]` Identifikasi file yang memang bagian proyek dan file yang hanya sementara.
- `[x]` Pastikan file lock Word seperti `~$*.docx` tidak ikut Git jika muncul lagi.
- `[x]` Kelompokkan perubahan yang belum commit:
  - fitur single active admin session,
  - perbaikan HTTPS asset lokal/online,
  - dokumen root/internal,
  - laporan Word,
  - asset guru yang sedang dipakai,
  - laporan audit dan rencana perbaikan.
- `[x]` `Kel 4_LaporanAkhirRPL.docx` tetap lokal dan tidak ikut push ke repository.

Verifikasi:

- `git status --short --branch`
- `git diff --check`

## Tahap 1 - Hardening Admin dan Operator

Tujuan: mengurangi risiko lockout dan akun lemah pada area admin.

Checklist:

- `[x]` Naikkan minimal password akun admin/operator baru dari 6 menjadi minimal 8 karakter.
- `[x]` Tambahkan validasi `password_confirmation` saat membuat/mengganti password admin dari menu kelola admin.
- `[x]` Cegah admin menghapus akun yang sedang login.
- `[x]` Cegah penghapusan atau perubahan role jika itu akan menghilangkan admin terakhir.
- `[x]` Reset `active_session_id` saat password akun admin/operator diganti oleh admin.
- `[x]` Tambahkan/ubah test untuk skenario:
  - password terlalu pendek ditolak,
  - admin tidak bisa menghapus akun sendiri,
  - admin terakhir tidak bisa dihapus/didemote,
  - operator tetap tidak bisa akses kelola admin.

Verifikasi:

- `php artisan test --filter=AdminRoleAccessTest`
- `php artisan test --filter=AdminOperationalTest`
- `php artisan test --filter=AdminSingleSessionTest`
- `php artisan test`

## Tahap 2 - Lengkapi Validasi Input Admin

Tujuan: mencegah data tidak konsisten dan error database yang tidak ramah pengguna.

Checklist:

- `[x]` Tambahkan whitelist/validasi `tipe` pada update konten umum.
- `[x]` Validasi `konten_items` untuk update kontak multi.
- `[x]` Validasi update banner:
  - `banner_id` wajib dan harus ada,
  - `judul_banner` wajib,
  - `subtitle_banner` nullable dengan batas panjang,
  - `urutan_banner` integer,
  - `gambar_banner_edit` harus image jpeg/png dengan ukuran aman.
- `[x]` Validasi tambah kategori kegiatan agar nama kategori tidak kosong/aneh.
- `[x]` Validasi data siswa admin:
  - `nisn` unique dengan pengecualian id saat edit,
  - `nis` unique dengan pengecualian id saat edit,
  - tanggal lahir valid jika diisi,
  - jenis kelamin hanya pilihan yang disediakan.
- `[x]` Tambahkan test untuk validasi konten/banner/siswa yang rawan.

Verifikasi:

- `php artisan test --filter=ContentManagementTest`
- `php artisan test --filter=PpdbWorkflowTest`
- `php artisan test`

## Tahap 3 - Cleanup Media dan File Varian

Tujuan: mencegah file gambar lama menumpuk ketika gambar diganti/dihapus.

Checklist:

- `[x]` Tambahkan helper terpusat untuk menghapus satu set gambar:
  - original,
  - `_thumb.webp`,
  - `_card.webp`,
  - `_hero.webp`.
- `[x]` Ganti penghapusan manual di:
  - `GuruController`,
  - `FasilitasController`,
  - `KontenController` untuk kegiatan dan banner.
- `[x]` Pastikan helper tidak menghapus file di luar `public/uploads`.
- `[x]` Tambahkan test/verifikasi untuk penghapusan varian gambar.
- `[x]` Cek ulang database dan file fisik setelah perubahan.

Verifikasi:

- `php artisan test --filter=ContentManagementTest`
- Cek file fisik di `public/uploads`.
- Browser check `/`, `/tenaga-pendidik`, `/fasilitas`, `/kegiatan`.

## Tahap 4 - Perbaikan Performa Ringan

Tujuan: menjaga website tetap ringan dan tidak bergantung pada resource eksternal yang tidak perlu.

Checklist:

- `[x]` Ganti placeholder transparent pixel hero dengan data URI yang valid.
- `[x]` Buat fallback chart sederhana tanpa CDN Chart.js.
- `[x]` Evaluasi halaman admin jadwal yang memuat banyak image tag.
- `[x]` Pastikan foto jadwal memakai thumbnail kecil dan lazy loading.
- `[x]` Evaluasi asset upload besar yang masih tracked; original besar dipertahankan, tampilan memakai varian kecil.

Verifikasi:

- `npm run build`
- Browser check dashboard admin.
- Browser check homepage.
- Browser audit broken image.

## Tahap 5 - Perbaikan Responsif Mobile

Tujuan: menghilangkan horizontal overflow pada mobile.

Checklist:

- `[x]` Perbaiki overflow dekorasi/animasi pada homepage mobile.
- `[x]` Perbaiki admin PPDB mobile agar lebar halaman tidak mengikuti tabel penuh.
- `[x]` Pastikan scroll horizontal hanya terjadi di container tabel jika memang tabel lebar.
- `[x]` Uji browser ringan pada viewport in-app 502px dan halaman terdampak; overflow tidak muncul.

Verifikasi:

- Browser check mobile:
  - `/`,
  - `/pendaftaran`,
  - `/admin/ppdb`.
- Pastikan `document.documentElement.scrollWidth <= clientWidth + toleransi kecil` pada halaman publik utama.

## Tahap 6 - Sinkronisasi Dokumentasi

Tujuan: memastikan agent/developer lain tidak salah membaca stack dan aturan proyek.

Checklist:

- `[x]` Update `docs/PROJECT_AUDIT.md` agar stack aktual sesuai kondisi saat ini.
- `[x]` Update `docs/PROJECT_STRUCTURE.md` agar stack sesuai composer/package aktual.
- `[x]` Pastikan `AGENTS.md`, `README_INTERNAL.md`, dan `TODO.md` selaras.
- `[x]` Tambahkan catatan bahwa stack saat ini adalah Laravel 12, PHP `^8.2`, Vite 6, Tailwind 4.

Verifikasi:

- `rg -n "Laravel major lama|Vite major lama" docs README.md README_INTERNAL.md AGENTS.md TODO.md`

## Tahap 7 - Final Verification

Tujuan: memastikan seluruh perbaikan tidak menimbulkan regresi.

Checklist:

- `[x]` Jalankan full test.
- `[x]` Jalankan build production.
- `[x]` Jalankan cache verification.
- `[x]` Browser check halaman publik utama.
- `[x]` Browser check halaman admin utama.
- `[x]` Cek media/database tidak broken.
- `[x]` Update `TODO.md` dengan tahap yang selesai.

Verifikasi final:

```bash
php artisan test
npm run build
php artisan route:cache
php artisan route:clear
php artisan config:cache
php artisan config:clear
php artisan view:cache
php artisan view:clear
git diff --check
git status --short --branch
```

## Urutan Rekomendasi Eksekusi

1. Tahap 0: rapikan baseline kerja.
2. Tahap 1: hardening admin/operator.
3. Tahap 2: validasi input admin.
4. Tahap 3: cleanup media.
5. Tahap 4 dan 5: performa ringan dan responsif mobile.
6. Tahap 6: sinkronisasi dokumentasi.
7. Tahap 7: final verification.
