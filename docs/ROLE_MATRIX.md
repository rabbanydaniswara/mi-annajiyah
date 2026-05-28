# Matriks Hak Akses Admin dan Operator

Dokumen ini menjadi acuan hak akses area admin MI Annajiyah.

## Role

- `admin`: pengelola penuh aplikasi, termasuk akun admin/operator dan audit log.
- `operator`: petugas operasional harian untuk PPDB, data siswa, konten, jadwal, guru, dan fasilitas.

## Matriks Menu

| Area | Admin | Operator | Catatan |
| --- | --- | --- | --- |
| Dashboard | Bisa melihat | Bisa melihat | Berisi ringkasan PPDB, jadwal, dan statistik. |
| Kelola PPDB | Bisa mengelola | Bisa mengelola | Termasuk verifikasi, catatan internal, dokumen private, export, dan bulk status. |
| Kelola Konten | Bisa mengelola | Bisa mengelola | Termasuk profil sekolah, banner, kegiatan, kategori, dan pengaturan tahun ajaran PPDB. |
| Kelola Jadwal | Bisa mengelola | Bisa mengelola | Data jadwal pelajaran. |
| Kelola Guru | Bisa mengelola | Bisa mengelola | Data guru dan tampilan publik. |
| Kelola Fasilitas | Bisa mengelola | Bisa mengelola | Data fasilitas dan tampilan publik. |
| Kelola Siswa | Bisa mengelola | Bisa mengelola | Data siswa internal/non-PPDB. |
| Export Data | Bisa export | Bisa export | Export mengikuti filter yang dikirim dari halaman terkait. |
| Ganti Password Sendiri | Bisa | Bisa | Wajib memasukkan password lama. |
| Kelola Admin/Operator | Bisa | Tidak bisa | Dibatasi middleware `admin`. |
| Log Aktivitas Sistem | Bisa melihat/filter | Tidak bisa | Termasuk filter user, action, model, dan tanggal. |

## Aturan Implementasi

- Route manajemen akun berada dalam middleware `admin`.
- Link Kelola Admin disembunyikan dari sidebar operator.
- Operator tetap dapat masuk dashboard, PPDB, konten, jadwal, guru, fasilitas, siswa, export, dan ganti password.
- Perubahan penting dicatat ke `activity_logs`.
