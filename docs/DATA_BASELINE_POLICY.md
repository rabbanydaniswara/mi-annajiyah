# Kebijakan Baseline Data

Tanggal ditetapkan: 2026-06-09

## Tujuan

Baseline proyek hanya berisi data dan media yang diperlukan untuk menampilkan halaman publik MI Annajiyah. Data operasional yang akan tumbuh setelah website digunakan tidak termasuk baseline.

## Data Baseline Yang Dipertahankan

- `users`: satu akun admin utama untuk mengelola aplikasi.
- `banner`: banner halaman utama.
- `guru`: profil dan foto tenaga pendidik.
- `fasilitas`: fasilitas yang ditampilkan ke publik.
- `kegiatan_kategori`: kategori galeri/kegiatan.
- `kegiatan_sekolah`: galeri dan kegiatan publik.
- `konten_web`: visi, misi, sejarah, kontak, tahun ajaran PPDB, dan konfigurasi konten publik.
- Media publik yang dirujuk data tersebut di `public/uploads`.
- Migration dan struktur tabel aplikasi.

Jumlah baseline saat ditetapkan:

| Data | Jumlah |
|---|---:|
| Admin | 1 |
| Banner | 2 |
| Guru | 10 |
| Fasilitas | 6 |
| Kategori kegiatan | 4 |
| Kegiatan/galeri | 32 |
| Konten web | 10 |
| Referensi media publik | 50 |

## Data Non-Baseline

Data berikut harus kosong pada salinan baseline:

- `siswa`: data pendaftar dan siswa operasional.
- `jadwal`: jadwal mata pelajaran.
- `activity_logs`: riwayat aktivitas runtime.
- `sessions` dan `password_reset_tokens`.
- `cache`, `cache_locks`, `jobs`, `job_batches`, dan `failed_jobs`.
- Dokumen pendaftar di `storage/app/private/ppdb`.
- Thumbnail dokumen pendaftar di `storage/app/private/ppdb-thumbs`.

Saat cleanup, `users.active_session_id` direset agar session lama tidak ikut menjadi baseline.

## Seeder

`DatabaseSeeder` dan `NewFeaturesSeeder` bersifat preservatif. Data siswa/jadwal demo hanya boleh dibuat jika:

- environment bukan production; dan
- `SEED_DEMO_DATA=true`.

Nilai tersebut harus tetap `false` atau tidak diset untuk baseline dan production.

## Strategi Lokal dan Production

Lokal dan production adalah dua environment dari satu proyek, bukan dua proyek yang dikerjakan terpisah.

- Repository lokal/Git menjadi sumber tunggal kode, migration, view, asset source, test, dan dokumentasi.
- Perubahan kode dibuat dan diuji satu kali di lokal, lalu artifact/build yang sama dideploy ke production.
- Database production menjadi sumber data operasional nyata setelah website digunakan.
- Database lokal hanya memakai baseline bersih atau data testing sintetis.
- Data pendaftar dan dokumen private production tidak disalin kembali ke lokal untuk pekerjaan rutin.
- Perubahan konten publik melalui admin production dicatat dan, jika perlu dijadikan baseline baru, diekspor secara khusus tanpa data operasional.
- Setelah deploy, production cukup menjalani migration, cache refresh, dan smoke test terarah; full test tetap dijalankan di lokal.

## Backup Cleanup 2026-06-09

Production sebelum cleanup:

```text
/home/miak7156/backups/baseline-clean-before-20260609-003705
```

Production setelah cleanup:

```text
/home/miak7156/backups/baseline-clean-after-20260609-004423
```

Backup lokal sebelum dan setelah cleanup:

```text
%TEMP%\miannajiyah-baseline-clean-local-20260609-003706
```

Backup sebelum cleanup memuat database serta dokumen private lama sehingga hanya boleh disimpan di lokasi private.
