<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewFeaturesSeeder extends Seeder
{
    public function run(): void
    {
        // Reset dependent tables for idempotent re-seeding (FK order matters)
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
            ['nama' => 'Ruang Kelas', 'deskripsi' => 'Ruang kelas nyaman dengan kipas angin, proyektor, dan sarana belajar lengkap.', 'ikon' => 'fas fa-chalkboard', 'urutan' => 1],
            ['nama' => 'Lapangan Olahraga', 'deskripsi' => 'Lapangan multifungsi untuk olahraga dan berbagai kegiatan outdoor siswa.', 'ikon' => 'fas fa-futbol', 'urutan' => 2],
            ['nama' => 'Perpustakaan', 'deskripsi' => 'Perpustakaan dengan koleksi buku pelajaran dan bacaan yang lengkap dan terawat.', 'ikon' => 'fas fa-book-open', 'urutan' => 3],
            ['nama' => 'Kantin Sehat', 'deskripsi' => 'Kantin dengan menu bergizi, bersih, dan terjangkau untuk menunjang kesehatan siswa.', 'ikon' => 'fas fa-utensils', 'urutan' => 4],
            ['nama' => 'Musholla', 'deskripsi' => 'Tempat ibadah yang bersih dan nyaman untuk siswa dan seluruh warga madrasah.', 'ikon' => 'fas fa-mosque', 'urutan' => 5],
            ['nama' => 'Aula Serbaguna', 'deskripsi' => 'Aula luas untuk berbagai kegiatan sekolah, upacara, dan acara resmi madrasah.', 'ikon' => 'fas fa-door-open', 'urutan' => 6],
        ];
        foreach ($fasilitas as $f) {
            DB::table('fasilitas')->insert(array_merge($f, [
                'aktif' => true, 'gambar' => null,
                'created_at' => now(), 'updated_at' => now()
            ]));
        }

        // Data Guru sesuai daftar
        $guruData = [
            ['nama' => 'Marhali, S.Ag', 'mapel' => 'PAI (Pendidikan Agama Islam)', 'jabatan' => 'Guru PAI', 'foto' => 'uploads/guru/Marhali_S_Ag.jpeg', 'urutan' => 1],
            ['nama' => 'Haikal Fikri', 'mapel' => 'PAI (Pendidikan Agama Islam)', 'jabatan' => 'Guru PAI', 'foto' => 'uploads/guru/Haikal_Fikri.jpeg', 'urutan' => 2],
            ['nama' => 'Mardiyah, S.Ag', 'mapel' => 'Guru Kelas 1', 'jabatan' => 'Wali Kelas 1', 'foto' => 'uploads/guru/Mardiyah_S_Ag.jpeg', 'urutan' => 3],
            ['nama' => 'Pilawati, S.Pd.I', 'mapel' => 'Guru Kelas 2', 'jabatan' => 'Wali Kelas 2', 'foto' => 'uploads/guru/Pilawati_S_Pd_I.jpeg', 'urutan' => 4],
            ['nama' => 'Nurjanah, S.Pd.I', 'mapel' => 'Guru Kelas 3', 'jabatan' => 'Wali Kelas 3', 'foto' => 'uploads/guru/Nurjanah_S_Pd_I.jpeg', 'urutan' => 5],
            ['nama' => 'Widyastuti', 'mapel' => 'Guru Kelas 4A', 'jabatan' => 'Wali Kelas 4A', 'foto' => 'uploads/guru/Widyastuti.jpeg', 'urutan' => 6],
            ['nama' => 'Atiyah, S.Pd.I', 'mapel' => 'Guru Kelas 4B', 'jabatan' => 'Wali Kelas 4B', 'foto' => 'uploads/guru/Atiyah_S_Pd_I.jpeg', 'urutan' => 7],
            ['nama' => 'Leni Irmawati, S.Pd', 'mapel' => 'Guru Kelas 5', 'jabatan' => 'Wali Kelas 5', 'foto' => 'uploads/guru/Leni_Irmawati_S_Pd.jpeg', 'urutan' => 8],
            ['nama' => 'Selvi Septiana Anggraini, S.Pd.I', 'mapel' => 'Guru Kelas 6', 'jabatan' => 'Wali Kelas 6', 'foto' => 'uploads/guru/Selvi_Septiana_Anggraini_S_Pd_I.jpeg', 'urutan' => 9],
            ['nama' => 'Putri Nurlailawati, S.Ak', 'mapel' => '-', 'jabatan' => 'Kepala Sekolah', 'foto' => 'uploads/guru/Putri_NurlailawatiS_Ak.jpeg', 'urutan' => 0],
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
            ['judul' => 'PPDB 2023-2024',          'tanggal' => '2023-07-01', 'gambar' => 'uploads/kegiatan/PPDB_2023-2024.jpeg',  'kategori_id' => $kp, 'deskripsi' => 'Penerimaan Peserta Didik Baru tahun ajaran 2023/2024 berlangsung lancar dengan antusiasme tinggi dari para orang tua dan calon siswa.'],
            ['judul' => 'PPDB 2024-2025',          'tanggal' => '2024-07-01', 'gambar' => 'uploads/kegiatan/PPDB_2024-2025.jpeg',  'kategori_id' => $kp, 'deskripsi' => 'Pendaftaran siswa baru periode 2024/2025 yang diikuti oleh banyak calon siswa dari berbagai daerah sekitar Pondok Aren.'],
            ['judul' => 'PPDB 2024-2025 (Sesi 2)', 'tanggal' => '2024-07-15', 'gambar' => 'uploads/kegiatan/PPDB_2024-20252.jpeg', 'kategori_id' => $kp, 'deskripsi' => 'Sesi kedua pendaftaran tahun ajaran 2024/2025 untuk mengakomodasi calon siswa yang belum terdaftar di sesi pertama.'],
            ['judul' => 'PPDB 2025-2026',          'tanggal' => '2025-07-01', 'gambar' => 'uploads/kegiatan/PPDB_2025-2026.jpeg',  'kategori_id' => $kp, 'deskripsi' => 'Pembukaan PPDB tahun ajaran 2025/2026 dengan berbagai program unggulan dan fasilitas yang lebih baik.'],
            ['judul' => 'Kegiatan PPDB',           'tanggal' => '2025-07-05', 'gambar' => 'uploads/kegiatan/PPDB.jpeg',            'kategori_id' => $kp, 'deskripsi' => 'Suasana pendaftaran murid baru di MI Annajiyah, dilayani langsung oleh panitia yang ramah dan informatif.'],
            ['judul' => 'Penerimaan Murid Baru',   'tanggal' => '2025-07-10', 'gambar' => 'uploads/kegiatan/PPDB2.jpeg',           'kategori_id' => $kp, 'deskripsi' => 'Proses verifikasi berkas dan pendaftaran ulang murid baru di lingkungan madrasah.'],
            // Pramuka
            ['judul' => 'Latihan Pramuka',             'tanggal' => '2025-09-01', 'gambar' => 'uploads/kegiatan/Pramuka.jpeg',  'kategori_id' => $kpr, 'deskripsi' => 'Latihan rutin pramuka mingguan untuk membentuk karakter disiplin, mandiri, dan kepemimpinan siswa.'],
            ['judul' => 'Kegiatan Pramuka Bersama',    'tanggal' => '2025-09-08', 'gambar' => 'uploads/kegiatan/Pramuka2.jpeg', 'kategori_id' => $kpr, 'deskripsi' => 'Kegiatan pramuka gabungan yang melatih kerjasama tim antar regu siswa.'],
            ['judul' => 'Pramuka - Keterampilan Tali', 'tanggal' => '2025-09-15', 'gambar' => 'uploads/kegiatan/Pramuka3.jpeg', 'kategori_id' => $kpr, 'deskripsi' => 'Sesi pelatihan simpul dan tali-temali sebagai keterampilan dasar pramuka.'],
            ['judul' => 'Pramuka - Outdoor',           'tanggal' => '2025-09-22', 'gambar' => 'uploads/kegiatan/Pramuka4.jpeg', 'kategori_id' => $kpr, 'deskripsi' => 'Aktivitas pramuka di luar ruangan untuk melatih ketangkasan dan kepekaan terhadap lingkungan.'],
            ['judul' => 'Pramuka - Apel Pagi',         'tanggal' => '2025-09-29', 'gambar' => 'uploads/kegiatan/Pramuka5.jpeg', 'kategori_id' => $kpr, 'deskripsi' => 'Apel pembukaan kegiatan pramuka untuk menanamkan disiplin dan semangat kepramukaan.'],
            ['judul' => 'Pramuka - Sandi Morse',       'tanggal' => '2025-10-06', 'gambar' => 'uploads/kegiatan/Pramuka6.jpeg', 'kategori_id' => $kpr, 'deskripsi' => 'Pembelajaran sandi morse sebagai bagian dari materi komunikasi pramuka.'],
            ['judul' => 'Pramuka - Pioneering',        'tanggal' => '2025-10-13', 'gambar' => 'uploads/kegiatan/Pramuka7.jpeg', 'kategori_id' => $kpr, 'deskripsi' => 'Praktik pioneering dengan tongkat dan tali untuk membangun struktur sederhana.'],
            ['judul' => 'Pramuka - Permainan Tim',     'tanggal' => '2025-10-20', 'gambar' => 'uploads/kegiatan/Pramuka8.jpeg', 'kategori_id' => $kpr, 'deskripsi' => 'Permainan kelompok yang membangun kekompakan dan strategi tim siswa.'],
            ['judul' => 'Pramuka - Yel-Yel',           'tanggal' => '2025-10-27', 'gambar' => 'uploads/kegiatan/Pramuka9.jpeg', 'kategori_id' => $kpr, 'deskripsi' => 'Latihan yel-yel kreatif untuk membangkitkan semangat dan kebersamaan regu.'],
            ['judul' => 'Pramuka - Penutupan Latihan', 'tanggal' => '2025-11-03', 'gambar' => 'uploads/kegiatan/Pramuka10.jpeg','kategori_id' => $kpr, 'deskripsi' => 'Sesi penutupan latihan pramuka dengan evaluasi dan refleksi kegiatan siswa.'],
            // Menari
            ['judul' => 'Latihan Tari Tradisional', 'tanggal' => '2025-08-01', 'gambar' => 'uploads/kegiatan/Ekskul_Menari.jpeg',  'kategori_id' => $km, 'deskripsi' => 'Latihan tari tradisional Nusantara untuk melestarikan budaya dan mengasah kreativitas seni siswa.'],
            ['judul' => 'Penampilan Tari Siswa',    'tanggal' => '2025-08-15', 'gambar' => 'uploads/kegiatan/Ekskul_Menari2.jpeg', 'kategori_id' => $km, 'deskripsi' => 'Penampilan tari oleh siswa pada acara madrasah, menampilkan kostum dan gerakan yang memukau.'],
            ['judul' => 'Pentas Seni Tari',         'tanggal' => '2025-09-05', 'gambar' => 'uploads/kegiatan/Ekskul_Menari3.jpeg', 'kategori_id' => $km, 'deskripsi' => 'Pentas seni tari sebagai puncak ekstrakurikuler menari dengan penonton orang tua dan tamu undangan.'],
            // Ramadhan
            ['judul' => 'Pesantren Ramadhan - Pembukaan',     'tanggal' => '2025-03-10', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun.jpeg',   'kategori_id' => $kr, 'deskripsi' => 'Pembukaan kegiatan Pesantren Ramadhan di madrasah dengan doa bersama dan tausiyah pengantar.'],
            ['judul' => 'Pesantren Ramadhan - Tadarus',       'tanggal' => '2025-03-12', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun2.jpeg',  'kategori_id' => $kr, 'deskripsi' => 'Sesi tadarus Al-Qur\'an bersama-sama untuk menambah pahala di bulan suci Ramadhan.'],
            ['judul' => 'Pesantren Ramadhan - Kultum',        'tanggal' => '2025-03-14', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun3.jpeg',  'kategori_id' => $kr, 'deskripsi' => 'Kuliah tujuh menit (kultum) oleh ustadz untuk memperdalam pemahaman agama siswa.'],
            ['judul' => 'Pesantren Ramadhan - Sholat',        'tanggal' => '2025-03-16', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun4.jpeg',  'kategori_id' => $kr, 'deskripsi' => 'Pelaksanaan sholat berjamaah sebagai pembiasaan ibadah bersama di bulan Ramadhan.'],
            ['judul' => 'Pesantren Ramadhan - Buka Bersama',  'tanggal' => '2025-03-18', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun5.jpeg',  'kategori_id' => $kr, 'deskripsi' => 'Buka puasa bersama yang mempererat ukhuwah antar siswa, guru, dan orang tua.'],
            ['judul' => 'Pesantren Ramadhan - Materi Fiqih',  'tanggal' => '2025-03-20', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun6.jpeg',  'kategori_id' => $kr, 'deskripsi' => 'Pembelajaran fiqih puasa, zakat, dan ibadah Ramadhan secara mendalam.'],
            ['judul' => 'Pesantren Ramadhan - Praktik Wudhu', 'tanggal' => '2025-03-22', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun7.jpeg',  'kategori_id' => $kr, 'deskripsi' => 'Praktik tata cara wudhu yang benar sesuai sunnah Rasulullah SAW.'],
            ['judul' => 'Zakat Fitrah - Pengumpulan',         'tanggal' => '2025-03-24', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun8.jpeg',  'kategori_id' => $kr, 'deskripsi' => 'Pengumpulan zakat fitrah dari siswa dan wali murid menjelang Hari Raya Idul Fitri.'],
            ['judul' => 'Zakat Fitrah - Penyaluran',          'tanggal' => '2025-03-26', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun9.jpeg',  'kategori_id' => $kr, 'deskripsi' => 'Penyaluran zakat fitrah kepada para mustahiq di sekitar lingkungan madrasah.'],
            ['judul' => 'Santunan Anak Yatim',                'tanggal' => '2025-03-27', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun10.jpeg', 'kategori_id' => $kr, 'deskripsi' => 'Pemberian santunan kepada anak yatim sebagai wujud kepedulian sosial keluarga besar madrasah.'],
            ['judul' => 'Santunan Dhuafa',                    'tanggal' => '2025-03-28', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun11.jpeg', 'kategori_id' => $kr, 'deskripsi' => 'Penyaluran bantuan kepada kaum dhuafa sebagai bentuk amal jariyah di bulan Ramadhan.'],
            ['judul' => 'Pembagian Bingkisan Lebaran',        'tanggal' => '2025-03-29', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun12.jpeg', 'kategori_id' => $kr, 'deskripsi' => 'Pembagian bingkisan lebaran kepada keluarga kurang mampu di sekitar madrasah.'],
            ['judul' => 'Halal Bi Halal',                     'tanggal' => '2025-04-05', 'gambar' => 'uploads/kegiatan/Pesantren_ramadhan,_Zakat_fitrah,_dan_santun13.jpeg', 'kategori_id' => $kr, 'deskripsi' => 'Acara halal bi halal pasca lebaran untuk saling memaafkan antar keluarga besar madrasah.'],
        ];

        foreach ($kegiatanAll as $kgt) {
            DB::table('kegiatan_sekolah')->insert(array_merge($kgt, ['created_at' => now()]));
        }
    }
}
