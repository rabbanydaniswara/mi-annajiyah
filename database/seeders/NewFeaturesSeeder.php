<?php

namespace Database\Seeders;

use App\Models\Siswa;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        // Reset dependent tables for idempotent re-seeding (FK order matters)
        DB::table('jadwal')->delete();
        DB::table('kegiatan_sekolah')->delete();
        DB::table('kegiatan_kategori')->delete();
        DB::table('fasilitas')->delete();

        // Kategori Kegiatan
        $kategoriIds = [];
        $kategoris = [
            ['nama' => 'PPDB', 'warna' => 'blue'],
            ['nama' => 'Ekskul Pramuka', 'warna' => 'green'],
            ['nama' => 'Ekskul Menari', 'warna' => 'purple'],
            ['nama' => 'Kegiatan Ramadhan', 'warna' => 'yellow'],
        ];
        foreach ($kategoris as $k) {
            $id = DB::table('kegiatan_kategori')->insertGetId(array_merge($k, [
                'created_at' => now(), 'updated_at' => now()
            ]));
            $kategoriIds[$k['nama']] = $id;
        }

        // Fasilitas
        $fasilitas = [
            ['nama' => 'Ruang Kelas Nyaman', 'deskripsi' => 'Ruang kelas tertata untuk pembelajaran aktif, diskusi kelompok, hafalan, dan pendampingan wali kelas.', 'ikon' => 'fas fa-chalkboard', 'gambar' => 'uploads/fasilitas/ruang-kelas.webp', 'urutan' => 1],
            ['nama' => 'Lapangan dan Area Upacara', 'deskripsi' => 'Area terbuka untuk upacara, olahraga, pramuka, dan kegiatan karakter siswa.', 'ikon' => 'fas fa-futbol', 'gambar' => 'uploads/fasilitas/lapangan.webp', 'urutan' => 2],
            ['nama' => 'Pojok Literasi', 'deskripsi' => 'Ruang baca dan koleksi buku pendukung untuk menumbuhkan kebiasaan literasi sejak dini.', 'ikon' => 'fas fa-book-open', 'gambar' => 'uploads/fasilitas/literasi.webp', 'urutan' => 3],
            ['nama' => 'Musholla dan Pembiasaan Ibadah', 'deskripsi' => 'Fasilitas ibadah dan pembiasaan harian untuk shalat berjamaah, tadarus, dan kegiatan keagamaan.', 'ikon' => 'fas fa-mosque', 'gambar' => 'uploads/fasilitas/musholla.webp', 'urutan' => 4],
            ['nama' => 'Aula Kegiatan', 'deskripsi' => 'Ruang kegiatan bersama untuk pentas seni, pertemuan orang tua, dan agenda madrasah.', 'ikon' => 'fas fa-door-open', 'gambar' => 'uploads/fasilitas/aula.webp', 'urutan' => 5],
            ['nama' => 'Layanan PPDB', 'deskripsi' => 'Area pelayanan administrasi dan verifikasi berkas yang membantu wali murid saat pendaftaran.', 'ikon' => 'fas fa-clipboard-check', 'gambar' => 'uploads/fasilitas/layanan-ppdb.webp', 'urutan' => 6],
        ];
        foreach ($fasilitas as $f) {
            DB::table('fasilitas')->insert(array_merge([
                'aktif' => true,
                'created_at' => now(), 'updated_at' => now()
            ], $f));
        }

        // Data Guru sesuai daftar
        $guruData = [
            ['nama' => 'Marhali, S.Ag', 'mapel' => 'PAI (Pendidikan Agama Islam)', 'jabatan' => 'Guru PAI', 'foto' => 'uploads/guru/Marhali_S_Ag.webp', 'urutan' => 1],
            ['nama' => 'Haikal Fikri', 'mapel' => 'PAI (Pendidikan Agama Islam)', 'jabatan' => 'Guru PAI', 'foto' => 'uploads/guru/Haikal_Fikri.webp', 'urutan' => 2],
            ['nama' => 'Mardiyah, S.Ag', 'mapel' => 'Guru Kelas 1', 'jabatan' => 'Wali Kelas 1', 'foto' => 'uploads/guru/Mardiyah_S_Ag.webp', 'urutan' => 3],
            ['nama' => 'Pilawati, S.Pd.I', 'mapel' => 'Guru Kelas 2', 'jabatan' => 'Wali Kelas 2', 'foto' => 'uploads/guru/Pilawati_S_Pd_I.webp', 'urutan' => 4],
            ['nama' => 'Nurjanah, S.Pd.I', 'mapel' => 'Guru Kelas 3', 'jabatan' => 'Wali Kelas 3', 'foto' => 'uploads/guru/Nurjanah_S_Pd_I.webp', 'urutan' => 5],
            ['nama' => 'Widyastuti', 'mapel' => 'Guru Kelas 4A', 'jabatan' => 'Wali Kelas 4A', 'foto' => 'uploads/guru/Widyastuti.webp', 'urutan' => 6],
            ['nama' => 'Atiyah, S.Pd.I', 'mapel' => 'Guru Kelas 4B', 'jabatan' => 'Wali Kelas 4B', 'foto' => 'uploads/guru/Atiyah_S_Pd_I.webp', 'urutan' => 7],
            ['nama' => 'Leni Irmawati, S.Pd', 'mapel' => 'Guru Kelas 5', 'jabatan' => 'Wali Kelas 5', 'foto' => 'uploads/guru/Leni_Irmawati_S_Pd.webp', 'urutan' => 8],
            ['nama' => 'Selvi Septiana Anggraini, S.Pd.I', 'mapel' => 'Guru Kelas 6', 'jabatan' => 'Wali Kelas 6', 'foto' => 'uploads/guru/Selvi_Septiana_Anggraini_S_Pd_I.webp', 'urutan' => 9],
            ['nama' => 'Putri Nurlailawati, S.Ak', 'mapel' => '-', 'jabatan' => 'Kepala Sekolah', 'foto' => 'uploads/guru/Putri_NurlailawatiS_Ak.webp', 'urutan' => 0],
        ];

        // Hapus data guru lama jika ada, lalu insert baru
        DB::table('guru')->delete();
        foreach ($guruData as $g) {
            DB::table('guru')->insert(array_merge($g, ['tampilkan' => true]));
        }

        // Kegiatan dari foto
        $kp = $kategoriIds['PPDB'];
        $kpr = $kategoriIds['Ekskul Pramuka'];
        $km = $kategoriIds['Ekskul Menari'];
        $kr = $kategoriIds['Kegiatan Ramadhan'];

        $kegiatanAll = [
            // PPDB
            ['judul' => 'PPDB 2023-2024',          'tanggal' => '2023-07-01', 'gambar' => 'uploads/kegiatan/PPDB_2023-2024.webp',  'kategori_id' => $kp, 'deskripsi' => 'Penerimaan Peserta Didik Baru tahun ajaran 2023/2024 berlangsung lancar dengan antusiasme tinggi dari para orang tua dan calon siswa.'],
            ['judul' => 'PPDB 2024-2025',          'tanggal' => '2024-07-01', 'gambar' => 'uploads/kegiatan/PPDB_2024-2025.webp',  'kategori_id' => $kp, 'deskripsi' => 'Pendaftaran siswa baru periode 2024/2025 yang diikuti oleh banyak calon siswa dari berbagai daerah sekitar Pondok Aren.'],
            ['judul' => 'PPDB 2024-2025 (Sesi 2)', 'tanggal' => '2024-07-15', 'gambar' => 'uploads/kegiatan/PPDB_2024-20252.webp', 'kategori_id' => $kp, 'deskripsi' => 'Sesi kedua pendaftaran tahun ajaran 2024/2025 untuk mengakomodasi calon siswa yang belum terdaftar di sesi pertama.'],
            ['judul' => 'PPDB 2025-2026',          'tanggal' => '2025-07-01', 'gambar' => 'uploads/kegiatan/PPDB_2025-2026.webp',  'kategori_id' => $kp, 'deskripsi' => 'Pembukaan PPDB tahun ajaran 2025/2026 dengan berbagai program unggulan dan fasilitas yang lebih baik.'],
            ['judul' => 'Kegiatan PPDB',           'tanggal' => '2025-07-05', 'gambar' => 'uploads/kegiatan/PPDB.webp',            'kategori_id' => $kp, 'deskripsi' => 'Suasana pendaftaran murid baru di MI Annajiyah, dilayani langsung oleh panitia yang ramah dan informatif.'],
            ['judul' => 'Penerimaan Murid Baru',   'tanggal' => '2025-07-10', 'gambar' => 'uploads/kegiatan/PPDB2.webp',           'kategori_id' => $kp, 'deskripsi' => 'Proses verifikasi berkas dan pendaftaran ulang murid baru di lingkungan madrasah.'],
            // Pramuka
            ['judul' => 'Latihan Pramuka',             'tanggal' => '2025-09-01', 'gambar' => 'uploads/kegiatan/Pramuka.webp',  'kategori_id' => $kpr, 'deskripsi' => 'Latihan rutin pramuka mingguan untuk membentuk karakter disiplin, mandiri, dan kepemimpinan siswa.'],
            ['judul' => 'Kegiatan Pramuka Bersama',    'tanggal' => '2025-09-08', 'gambar' => 'uploads/kegiatan/Pramuka2.webp', 'kategori_id' => $kpr, 'deskripsi' => 'Kegiatan pramuka gabungan yang melatih kerjasama tim antar regu siswa.'],
            ['judul' => 'Pramuka - Keterampilan Tali', 'tanggal' => '2025-09-15', 'gambar' => 'uploads/kegiatan/Pramuka3.webp', 'kategori_id' => $kpr, 'deskripsi' => 'Sesi pelatihan simpul dan tali-temali sebagai keterampilan dasar pramuka.'],
            ['judul' => 'Pramuka - Outdoor',           'tanggal' => '2025-09-22', 'gambar' => 'uploads/kegiatan/Pramuka4.webp', 'kategori_id' => $kpr, 'deskripsi' => 'Aktivitas pramuka di luar ruangan untuk melatih ketangkasan dan kepekaan terhadap lingkungan.'],
            ['judul' => 'Pramuka - Apel Pagi',         'tanggal' => '2025-09-29', 'gambar' => 'uploads/kegiatan/Pramuka5.webp', 'kategori_id' => $kpr, 'deskripsi' => 'Apel pembukaan kegiatan pramuka untuk menanamkan disiplin dan semangat kepramukaan.'],
            ['judul' => 'Pramuka - Sandi Morse',       'tanggal' => '2025-10-06', 'gambar' => 'uploads/kegiatan/Pramuka6.webp', 'kategori_id' => $kpr, 'deskripsi' => 'Pembelajaran sandi morse sebagai bagian dari materi komunikasi pramuka.'],
            ['judul' => 'Pramuka - Pioneering',        'tanggal' => '2025-10-13', 'gambar' => 'uploads/kegiatan/Pramuka7.webp', 'kategori_id' => $kpr, 'deskripsi' => 'Praktik pioneering dengan tongkat dan tali untuk membangun struktur sederhana.'],
            ['judul' => 'Pramuka - Permainan Tim',     'tanggal' => '2025-10-20', 'gambar' => 'uploads/kegiatan/Pramuka8.webp', 'kategori_id' => $kpr, 'deskripsi' => 'Permainan kelompok yang membangun kekompakan dan strategi tim siswa.'],
            ['judul' => 'Pramuka - Yel-Yel',           'tanggal' => '2025-10-27', 'gambar' => 'uploads/kegiatan/Pramuka9.webp', 'kategori_id' => $kpr, 'deskripsi' => 'Latihan yel-yel kreatif untuk membangkitkan semangat dan kebersamaan regu.'],
            ['judul' => 'Pramuka - Penutupan Latihan', 'tanggal' => '2025-11-03', 'gambar' => 'uploads/kegiatan/Pramuka10.webp','kategori_id' => $kpr, 'deskripsi' => 'Sesi penutupan latihan pramuka dengan evaluasi dan refleksi kegiatan siswa.'],
            // Menari
            ['judul' => 'Latihan Tari Tradisional', 'tanggal' => '2025-08-01', 'gambar' => 'uploads/kegiatan/Ekskul_Menari.webp',  'kategori_id' => $km, 'deskripsi' => 'Latihan tari tradisional Nusantara untuk melestarikan budaya dan mengasah kreativitas seni siswa.'],
            ['judul' => 'Penampilan Tari Siswa',    'tanggal' => '2025-08-15', 'gambar' => 'uploads/kegiatan/Ekskul_Menari2.webp', 'kategori_id' => $km, 'deskripsi' => 'Penampilan tari oleh siswa pada acara madrasah, menampilkan kostum dan gerakan yang memukau.'],
            ['judul' => 'Pentas Seni Tari',         'tanggal' => '2025-09-05', 'gambar' => 'uploads/kegiatan/Ekskul_Menari3.webp', 'kategori_id' => $km, 'deskripsi' => 'Pentas seni tari sebagai puncak ekstrakurikuler menari dengan penonton orang tua dan tamu undangan.'],
            // Ramadhan
            ['judul' => 'Pesantren Ramadhan - Pembukaan',     'tanggal' => '2025-03-10', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun.webp',   'kategori_id' => $kr, 'deskripsi' => 'Pembukaan kegiatan Pesantren Ramadhan di madrasah dengan doa bersama dan tausiyah pengantar.'],
            ['judul' => 'Pesantren Ramadhan - Tadarus',       'tanggal' => '2025-03-12', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun2.webp',  'kategori_id' => $kr, 'deskripsi' => 'Sesi tadarus Al-Qur\'an bersama-sama untuk menambah pahala di bulan suci Ramadhan.'],
            ['judul' => 'Pesantren Ramadhan - Kultum',        'tanggal' => '2025-03-14', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun3.webp',  'kategori_id' => $kr, 'deskripsi' => 'Kuliah tujuh menit (kultum) oleh ustadz untuk memperdalam pemahaman agama siswa.'],
            ['judul' => 'Pesantren Ramadhan - Sholat',        'tanggal' => '2025-03-16', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun4.webp',  'kategori_id' => $kr, 'deskripsi' => 'Pelaksanaan sholat berjamaah sebagai pembiasaan ibadah bersama di bulan Ramadhan.'],
            ['judul' => 'Pesantren Ramadhan - Buka Bersama',  'tanggal' => '2025-03-18', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun5.webp',  'kategori_id' => $kr, 'deskripsi' => 'Buka puasa bersama yang mempererat ukhuwah antar siswa, guru, dan orang tua.'],
            ['judul' => 'Pesantren Ramadhan - Materi Fiqih',  'tanggal' => '2025-03-20', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun6.webp',  'kategori_id' => $kr, 'deskripsi' => 'Pembelajaran fiqih puasa, zakat, dan ibadah Ramadhan secara mendalam.'],
            ['judul' => 'Pesantren Ramadhan - Praktik Wudhu', 'tanggal' => '2025-03-22', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun7.webp',  'kategori_id' => $kr, 'deskripsi' => 'Praktik tata cara wudhu yang benar sesuai sunnah Rasulullah SAW.'],
            ['judul' => 'Zakat Fitrah - Pengumpulan',         'tanggal' => '2025-03-24', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun8.webp',  'kategori_id' => $kr, 'deskripsi' => 'Pengumpulan zakat fitrah dari siswa dan wali murid menjelang Hari Raya Idul Fitri.'],
            ['judul' => 'Zakat Fitrah - Penyaluran',          'tanggal' => '2025-03-26', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun9.webp',  'kategori_id' => $kr, 'deskripsi' => 'Penyaluran zakat fitrah kepada para mustahiq di sekitar lingkungan madrasah.'],
            ['judul' => 'Santunan Anak Yatim',                'tanggal' => '2025-03-27', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun10.webp', 'kategori_id' => $kr, 'deskripsi' => 'Pemberian santunan kepada anak yatim sebagai wujud kepedulian sosial keluarga besar madrasah.'],
            ['judul' => 'Santunan Dhuafa',                    'tanggal' => '2025-03-28', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun11.webp', 'kategori_id' => $kr, 'deskripsi' => 'Penyaluran bantuan kepada kaum dhuafa sebagai bentuk amal jariyah di bulan Ramadhan.'],
            ['judul' => 'Pembagian Bingkisan Lebaran',        'tanggal' => '2025-03-29', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun12.webp', 'kategori_id' => $kr, 'deskripsi' => 'Pembagian bingkisan lebaran kepada keluarga kurang mampu di sekitar madrasah.'],
            ['judul' => 'Halal Bi Halal',                     'tanggal' => '2025-04-05', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun13.webp', 'kategori_id' => $kr, 'deskripsi' => 'Acara halal bi halal pasca lebaran untuk saling memaafkan antar keluarga besar madrasah.'],
        ];

        foreach ($kegiatanAll as $kgt) {
            DB::table('kegiatan_sekolah')->insert(array_merge($kgt, ['created_at' => now()]));
        }

        $this->seedDemoStudents();
    }

    private function seedDemoStudents(): void
    {
        $tahunAjaran = date('Y') . '/' . (date('Y') + 1);
        $students = [
            ['nama' => 'Ahmad Zidan Ramadhan', 'jenis_kelamin' => 'Laki-laki', 'kelas' => '1A', 'status_ppdb' => 'diterima', 'nisn' => '9900000001', 'nis' => 'SIM-001', 'no_wa' => '6285174228001'],
            ['nama' => 'Aisyah Putri Zahra', 'jenis_kelamin' => 'Perempuan', 'kelas' => '1A', 'status_ppdb' => 'daftar_ulang', 'nisn' => '9900000002', 'nis' => 'SIM-002', 'no_wa' => '6285174228002'],
            ['nama' => 'Muhammad Rayyan Fadli', 'jenis_kelamin' => 'Laki-laki', 'kelas' => '1B', 'status_ppdb' => 'diverifikasi', 'nisn' => '9900000003', 'nis' => 'SIM-003', 'no_wa' => '6285174228003'],
            ['nama' => 'Naila Khairunnisa', 'jenis_kelamin' => 'Perempuan', 'kelas' => '1B', 'status_ppdb' => 'pending', 'nisn' => '9900000004', 'nis' => 'SIM-004', 'no_wa' => '6285174228004'],
            ['nama' => 'Bilal Aditya Nugraha', 'jenis_kelamin' => 'Laki-laki', 'kelas' => '2A', 'status_ppdb' => 'diterima', 'nisn' => '9900000005', 'nis' => 'SIM-005', 'no_wa' => '6285174228005'],
            ['nama' => 'Salsabila Nur Azizah', 'jenis_kelamin' => 'Perempuan', 'kelas' => '2A', 'status_ppdb' => 'berkas_kurang', 'nisn' => '9900000006', 'nis' => 'SIM-006', 'no_wa' => '6285174228006'],
            ['nama' => 'Rafa Alfarizi', 'jenis_kelamin' => 'Laki-laki', 'kelas' => '3A', 'status_ppdb' => 'diterima', 'nisn' => '9900000007', 'nis' => 'SIM-007', 'no_wa' => '6285174228007'],
            ['nama' => 'Khalisa Humaira', 'jenis_kelamin' => 'Perempuan', 'kelas' => '3A', 'status_ppdb' => 'pending', 'nisn' => '9900000008', 'nis' => 'SIM-008', 'no_wa' => '6285174228008'],
            ['nama' => 'Fathan Al Ghifari', 'jenis_kelamin' => 'Laki-laki', 'kelas' => '4A', 'status_ppdb' => 'diterima', 'nisn' => '9900000009', 'nis' => 'SIM-009', 'no_wa' => '6285174228009'],
            ['nama' => 'Maryam Hanifah', 'jenis_kelamin' => 'Perempuan', 'kelas' => '5A', 'status_ppdb' => 'diterima', 'nisn' => '9900000010', 'nis' => 'SIM-010', 'no_wa' => '6285174228010'],
            ['nama' => 'Yusuf Maulana', 'jenis_kelamin' => 'Laki-laki', 'kelas' => '6A', 'status_ppdb' => 'ditolak', 'nisn' => '9900000011', 'nis' => 'SIM-011', 'no_wa' => '6285174228011'],
            ['nama' => 'Hana Syakira', 'jenis_kelamin' => 'Perempuan', 'kelas' => '6A', 'status_ppdb' => 'pending', 'nisn' => '9900000012', 'nis' => 'SIM-012', 'no_wa' => '6285174228012'],
        ];

        foreach ($students as $index => $student) {
            Siswa::updateOrCreate(
                ['nisn' => $student['nisn']],
                array_merge($student, [
                    'tempat_lahir' => 'Tangerang Selatan',
                    'tanggal_lahir' => now()->subYears(7 + ($index % 5))->subMonths($index)->toDateString(),
                    'asal_sekolah' => 'TK/RA Sekitar Pondok Aren',
                    'nama_ortu' => 'Wali ' . $student['nama'],
                    'alamat' => 'Pondok Aren, Tangerang Selatan',
                    'tahun_ajaran' => $tahunAjaran,
                    'tanggal_daftar' => now()->subDays(12 - $index),
                    'tgl_verifikasi' => in_array($student['status_ppdb'], ['pending', 'berkas_kurang'], true) ? null : now()->subDays(3),
                ])
            );
        }
    }
}
