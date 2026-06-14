# Panduan AI Agent

File ini dibuat agar AI agent lain bisa cepat memahami proyek tanpa menebak-nebak konteks.

## Cara Mulai

1. Baca `TODO.md` terlebih dahulu.
2. Baca `docs/PROJECT_RULES.md` untuk aturan perubahan kode.
3. Baca `docs/PROJECT_STRUCTURE.md` untuk memahami modul dan alur aplikasi.
4. Baca `docs/REMEDIATION_TRACKER.md` dan `docs/APP_IMPROVEMENT_ROADMAP.md` untuk prioritas pekerjaan.
5. Jika task terkait performa/media, baca `docs/PERFORMANCE_MEDIA_PRESERVATION_PLAN.md`.

## Ringkasan Proyek

- Aplikasi: SPMB/PPDB MI Annajiyah.
- Framework: Laravel 12.
- Frontend build: Vite, Tailwind CSS.
- Database lokal saat ini: mengikuti konfigurasi `.env`.
- Area admin memiliki role `admin` dan `operator`.
- Hosting belum menjadi fokus langsung; proyek masih fase perbaikan dan pengembangan.

## Dokumen Penting

- `TODO.md`: tracker aktif yang wajib diperbarui.
- `docs/PROJECT_AUDIT.md`: hasil audit dan risiko.
- `docs/PROJECT_STRUCTURE.md`: struktur aplikasi, route, model, dan alur data.
- `docs/PROJECT_RULES.md`: aturan teknis proyek.
- `docs/DEVELOPMENT_GUIDELINES.md`: setup, verifikasi, testing, dan deployment.
- `docs/REMEDIATION_TRACKER.md`: tracker perbaikan audit.
- `docs/APP_IMPROVEMENT_ROADMAP.md`: roadmap pengembangan sebelum hosting.
- `docs/HOSTING_READINESS.md`: checklist hosting.
- `docs/ROLE_MATRIX.md`: hak akses admin dan operator.
- `docs/CONTENT_MEDIA_CHECKLIST.md`: checklist konten/media.
- `docs/BACKUP_GUIDE.md`: panduan backup.
- `docs/PERFORMANCE_MEDIA_PRESERVATION_PLAN.md`: optimasi performa dan aturan preservasi media.

## Aturan Data dan Media

- Jangan menghapus data publik atau media baseline tanpa instruksi eksplisit.
- Seeder harus preservatif: tidak boleh mengosongkan data publik yang sudah ada.
- Media publik yang dipakai web berada di `public/uploads`.
- Dokumen pendaftaran sensitif harus tetap di `storage/app/private/ppdb`, bukan di `public`.
- Varian media yang dipakai web seperti `*_card.webp` dan `*_hero.webp` adalah asset web dan boleh ikut Git jika memang dipakai tampilan.
- Screenshot QA dan file sementara di `storage` tidak perlu di-commit.
- `public/build` tidak perlu di-commit karena bisa dibuat ulang dengan `npm run build`.

## Aturan Khusus Saat Ini

- Nomor WhatsApp pendaftaran harus disimpan sesuai input lokal jika user mengetik `08...`.
- Link WhatsApp admin boleh mengonversi nomor hanya saat membuat URL `wa.me`.
- Admin session saat ini masih bisa login di beberapa perangkat. Task pembatasan session ada di `TODO.md`.
- Jika mengubah foto guru/fasilitas/kegiatan/banner dari admin, cek juga varian `thumb`, `card`, atau `hero` sesuai jenis media.

## Verifikasi Yang Disarankan

Gunakan sesuai dampak perubahan:

```bash
php artisan test
npm run build
php artisan route:cache
php artisan route:clear
php artisan view:cache
php artisan view:clear
```

Untuk perubahan UI/media publik, lakukan browser QA sederhana:

- `/`
- `/fasilitas`
- `/kegiatan`
- `/tenaga-pendidik`
- `/pendaftaran`
- `/cek-pendaftaran`

## Aturan Git

- Jangan commit/push tanpa izin user.
- Sebelum commit, cek:

```bash
git status --short
git diff --check
git diff --cached --check
```

- Jangan stage file berikut:
  - `.env`
  - `vendor`
  - `node_modules`
  - `storage`
  - `public/build`
  - database lokal
  - dokumen private PPDB

- Setelah pekerjaan selesai, update `TODO.md`.
