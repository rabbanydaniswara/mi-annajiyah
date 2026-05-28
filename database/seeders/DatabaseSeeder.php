<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedInitialAdmin();

        // Konten web
        DB::table('konten_web')->insert([
            ['tipe' => 'visi', 'judul' => 'Visi', 'konten' => 'Menjadikan Madrasah Ibtidaiyah yang berkualitas, berkarakter islami dan berprestasi.', 'gambar' => null, 'urutan' => 1],
            ['tipe' => 'misi', 'judul' => 'Misi', 'konten' => "1. Menyelenggarakan pendidikan yang berkualitas\n2. Membentuk karakter siswa yang islami\n3. Mengembangkan potensi siswa secara optimal\n4. Menciptakan lingkungan belajar yang kondusif", 'gambar' => null, 'urutan' => 2],
            ['tipe' => 'sejarah', 'judul' => 'Sejarah', 'konten' => 'MI Annajiyah berdiri sejak tahun 1995. MI ini didirikan oleh para tokoh masyarakat dan ulama setempat yang memiliki visi untuk menyediakan pendidikan berkualitas berbasis Islam.', 'gambar' => null, 'urutan' => 3],
            ['tipe' => 'alamat',  'judul' => 'Alamat',  'konten' => 'Jl. PLN No. 80, Pondok Karya, Kec. Pondok Aren, Kota Tangerang Selatan, Banten 15225', 'gambar' => null, 'urutan' => 4],
            ['tipe' => 'telepon', 'judul' => 'Telepon', 'konten' => '+62 21 1234 5678', 'gambar' => null, 'urutan' => 5],
            ['tipe' => 'email',   'judul' => 'Email',   'konten' => 'info@miannajiyah.sch.id', 'gambar' => null, 'urutan' => 6],
            ['tipe' => 'jam_op',  'judul' => 'Jam Operasional', 'konten' => 'Senin - Jumat: 07.00 - 14.00 WIB', 'gambar' => null, 'urutan' => 7],
            ['tipe' => 'ppdb_tahun_ajaran', 'judul' => 'Tahun Ajaran PPDB Aktif', 'konten' => date('Y') . '/' . (date('Y') + 1), 'gambar' => null, 'urutan' => 20],
        ]);

        // Banner
        DB::table('banner')->insert([
            ['judul' => 'Selamat Datang di MI Annajiyah', 'subtitle' => 'Madrasah Unggulan Berprestasi', 'gambar' => 'uploads/banner/banner2.jpg', 'urutan' => 1, 'aktif' => 1],
            ['judul' => 'PPDB 2026/2027 Telah Dibuka', 'subtitle' => 'Daftarkan putra-putri Anda sekarang juga!', 'gambar' => 'uploads/banner/banner1.jpg', 'urutan' => 2, 'aktif' => 1],
        ]);

        // Run additional seeders (kategori, fasilitas, guru, kegiatan)
        $this->call(NewFeaturesSeeder::class);
    }

    private function seedInitialAdmin(): void
    {
        $username = env('INITIAL_ADMIN_USERNAME');
        $password = env('INITIAL_ADMIN_PASSWORD');

        if (app()->environment('production') && (!$username || !$password)) {
            $this->command?->warn('Skipping initial admin seeder in production. Set INITIAL_ADMIN_USERNAME and INITIAL_ADMIN_PASSWORD if needed.');
            return;
        }

        $username = $username ?: 'admin';
        $password = $password ?: 'admin123';

        if (app()->environment('production') && strlen($password) < 12) {
            throw new RuntimeException('INITIAL_ADMIN_PASSWORD must be at least 12 characters in production.');
        }

        User::updateOrCreate(
            ['username' => $username],
            [
                'password' => Hash::make($password),
                'role' => 'admin',
            ]
        );
    }
}
