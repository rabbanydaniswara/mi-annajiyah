# Admin Function Audit Tracker

Tanggal mulai: 2026-06-09

## Tujuan

Memastikan seluruh fungsi tambah, edit, update, toggle, filter, export, dan hapus pada area admin bekerja dengan benar. Prioritas pertama adalah bug penyimpanan tab Kontak.

## Aturan Pengujian

- Perubahan kode dilakukan dan diuji di lokal terlebih dahulu.
- Data baseline publik tidak boleh hilang selama audit.
- Pengujian destructive memakai database testing atau data QA sementara yang dibersihkan kembali.
- Production hanya menerima patch yang sudah lulus full test dan browser QA lokal.
- Backup production dibuat sebelum deployment patch.
- `siamiannajiyah.my.id` tidak boleh diubah.

## Phase A - Pemetaan dan Bug Kontak

Status: `[x]` Selesai

- `[x]` Petakan route, controller, form, dan test area admin.
- `[x]` Periksa data kontak lokal dan production.
- `[x]` Identifikasi alur validasi yang mengembalikan form tanpa pesan error.
- `[x]` Identifikasi URL TikTok yang tersimpan pada field WhatsApp.
- `[x]` Perbaiki penyimpanan kontak, tampilan error, dan old input.
- `[x]` Tambahkan regression test kontak valid, tidak valid, kosong, dan cache.

## Phase B - Audit Modul Konten

Status: `[x]` Selesai

- `[x]` Visi, misi, dan sejarah.
- `[x]` Pengaturan tahun ajaran PPDB.
- `[x]` Kontak dan sosial media.
- `[x]` Banner: tambah, edit, toggle, dan hapus.
- `[x]` Kategori kegiatan: tambah, edit, dan hapus.
- `[x]` Kegiatan: tambah, edit, dan hapus.
- `[x]` Verifikasi invalidasi cache publik.

## Phase C - Audit Modul Data

Status: `[x]` Selesai

- `[x]` Guru: tambah, edit, ganti foto, visibility, dan hapus.
- `[x]` Fasilitas: tambah, edit, ganti foto, toggle, dan hapus.
- `[x]` Jadwal: tambah, edit, validasi bentrok, print, dan hapus.
- `[x]` Siswa: tambah, edit, validasi identitas, filter, export, dan hapus.
- `[x]` PPDB: status tunggal, bulk status, detail, dokumen, export, dan hapus.

## Phase D - Audit Akun dan Keamanan

Status: `[x]` Selesai

- `[x]` Login, logout, dan single active session.
- `[x]` Ganti password sendiri.
- `[x]` Tambah dan edit admin/operator.
- `[x]` Proteksi akun sendiri dan admin terakhir.
- `[x]` Pembatasan role operator.
- `[x]` Filter activity log.

## Phase E - QA dan Deployment

Status: `[x]` Selesai

- `[x]` Full automated test.
- `[x]` Pint, build, route/view cache.
- `[x]` Browser QA lokal seluruh halaman admin.
- `[x]` Backup production sebelum patch.
- `[x]` Deploy artifact patch yang sama ke production.
- `[x]` Browser QA production dan cleanup data QA.
- `[x]` Perbarui `TODO.md` dan dokumentasi akhir.

## Temuan Awal

1. Tab Kontak tidak menampilkan error validasi per field.
2. Setelah validasi gagal, nilai input kembali mengambil database, bukan request sebelumnya, sehingga terlihat seperti penyimpanan tidak berjalan.
3. Nilai baseline `wa` berisi URL TikTok, bukan nomor WhatsApp.
4. Test yang ada hanya menguji penolakan email/key tidak valid; belum membuktikan seluruh kontak valid tersimpan dan tampil kembali.
5. Form kegiatan dan kategori hanya menyediakan tambah/hapus, belum memiliki fungsi edit.

## Hasil Verifikasi Lokal

- `php artisan test`: 65 test lulus dengan 377 assertion.
- `vendor/bin/pint --test`, `npm run build`, dan `git diff --check`: lulus.
- Route cache dan view cache berhasil dibuat serta dibersihkan kembali.
- Browser QA desktop dan mobile mencakup seluruh halaman utama admin tanpa broken image, overflow, atau console error.
- Penyimpanan kontak valid dan invalid diuji dari browser; nilai lama berhasil dipulihkan setelah QA.
- Fungsi edit kegiatan dan kategori ditambahkan dan diuji.
- Akun serta seluruh data QA lokal sudah dihapus; baseline kembali ke 1 admin, 0 siswa, 0 jadwal, dan 0 activity log.

## Hasil Deployment dan QA Production

- Backup sebelum patch: `/home/miak7156/backups/admin-contact-patch-before-20260609-065418`.
- Patch runtime, migration, dan asset build berhasil dipasang ke `miannajiyah.site`.
- Migration `2026_06_09_000000_separate_whatsapp_and_tiktok_contacts` berstatus `Ran`.
- Simpan kontak valid, validasi kontak invalid, old input, invalidasi cache, dan render footer publik diuji langsung melalui browser.
- Seluruh halaman admin utama diuji pada desktop dan viewport mobile tanpa broken image, overflow, warning, atau console error.
- Tersedia fungsi edit untuk 32 kegiatan dan 4 kategori pada production.
- Data kontak dikembalikan ke baseline setelah QA.
- Akun, session, dan activity log QA production sudah dihapus.
- Baseline akhir production: 1 admin, 0 siswa, 0 jadwal, 10 guru, 6 fasilitas, 4 kategori, 32 kegiatan, 2 banner, 11 konten, dan 0 activity log.
- `miannajiyah.site` dan `siamiannajiyah.my.id` tetap merespons HTTPS 200; folder domain lama tidak diubah.
