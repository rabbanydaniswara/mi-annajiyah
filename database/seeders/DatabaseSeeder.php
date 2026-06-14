<?php

namespace Database\Seeders;

use App\Helpers\PpdbHelper;
use App\Models\Banner;
use App\Models\KontenWeb;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedInitialAdmin();

        foreach ($this->kontenWeb() as $konten) {
            KontenWeb::firstOrCreate(
                ['tipe' => $konten['tipe']],
                $konten
            );
        }

        foreach ($this->banners() as $banner) {
            Banner::firstOrCreate(
                ['urutan' => $banner['urutan']],
                $banner
            );
        }

        $this->call(NewFeaturesSeeder::class);

        if ($this->shouldSeedDemoData()) {
            $this->call(JadwalSeeder::class);
        }
    }

    private function kontenWeb(): array
    {
        return [
            [
                'tipe' => 'visi',
                'judul' => 'Visi',
                'konten' => 'Menjadi madrasah ibtidaiyah yang unggul dalam akhlak, prestasi, literasi, dan kepedulian sosial.',
                'gambar' => null,
                'urutan' => 1,
            ],
            [
                'tipe' => 'misi',
                'judul' => 'Misi',
                'konten' => "1. Menyelenggarakan pembelajaran aktif, kreatif, dan menyenangkan.\n2. Membiasakan ibadah harian, adab islami, dan karakter disiplin.\n3. Mengembangkan potensi akademik, seni, olahraga, dan kepramukaan siswa.\n4. Menjalin kerja sama yang baik antara madrasah, orang tua, yayasan, dan masyarakat.",
                'gambar' => null,
                'urutan' => 2,
            ],
            [
                'tipe' => 'sejarah',
                'judul' => 'Sejarah',
                'konten' => 'MI Annajiyah tumbuh dari semangat masyarakat dan tokoh pendidikan Islam untuk menghadirkan madrasah yang dekat dengan kebutuhan warga. Sejak berdiri, madrasah ini terus mengembangkan pembelajaran dasar yang memadukan ilmu pengetahuan, pembiasaan ibadah, dan kegiatan karakter.',
                'gambar' => null,
                'urutan' => 3,
            ],
            [
                'tipe' => 'alamat',
                'judul' => 'Alamat',
                'konten' => 'Jl. PLN No. 80, Pondok Karya, Kec. Pondok Aren, Kota Tangerang Selatan, Banten 15225',
                'gambar' => null,
                'urutan' => 4,
            ],
            [
                'tipe' => 'telepon',
                'judul' => 'Telepon',
                'konten' => '+62 851-7422-8000',
                'gambar' => null,
                'urutan' => 5,
            ],
            [
                'tipe' => 'email',
                'judul' => 'Email',
                'konten' => 'info@miannajiyah.sch.id',
                'gambar' => null,
                'urutan' => 6,
            ],
            [
                'tipe' => 'jam_op',
                'judul' => 'Jam Operasional',
                'konten' => 'Senin - Jumat: 07.00 - 13.30 WIB; Sabtu: 07.00 - 11.00 WIB',
                'gambar' => null,
                'urutan' => 7,
            ],
            [
                'tipe' => 'wa',
                'judul' => 'WhatsApp',
                'konten' => '085174228000',
                'gambar' => null,
                'urutan' => 8,
            ],
            [
                'tipe' => 'ig',
                'judul' => 'Instagram',
                'konten' => 'https://www.instagram.com/mi_annajiyah',
                'gambar' => null,
                'urutan' => 9,
            ],
            [
                'tipe' => 'tiktok',
                'judul' => 'TikTok',
                'konten' => 'https://www.tiktok.com/@mis.annajiyah',
                'gambar' => null,
                'urutan' => 10,
            ],
            [
                'tipe' => 'ppdb_tahun_ajaran',
                'judul' => 'Tahun Ajaran PPDB Aktif',
                'konten' => date('Y').'/'.(date('Y') + 1),
                'gambar' => null,
                'urutan' => 20,
            ],
            [
                'tipe' => 'ppdb_status',
                'judul' => 'Status Pendaftaran PPDB',
                'konten' => 'open',
                'gambar' => null,
                'urutan' => 21,
            ],
            [
                'tipe' => 'ppdb_pesan_tutup',
                'judul' => 'Pesan Publik Saat PPDB Ditutup',
                'konten' => PpdbHelper::DEFAULT_CLOSED_MESSAGE,
                'gambar' => null,
                'urutan' => 22,
            ],
        ];
    }

    private function banners(): array
    {
        return [
            [
                'judul' => 'MI Annajiyah',
                'subtitle' => 'Madrasah ibtidaiyah yang membimbing anak tumbuh cerdas, santun, dan percaya diri.',
                'gambar' => 'uploads/banner/banner2.webp',
                'urutan' => 1,
                'aktif' => 1,
            ],
            [
                'judul' => 'PPDB 2026/2027 Telah Dibuka',
                'subtitle' => 'Pendaftaran peserta didik baru dapat dilakukan secara online dengan proses yang mudah dan terpantau.',
                'gambar' => 'uploads/banner/banner1.webp',
                'urutan' => 2,
                'aktif' => 1,
            ],
        ];
    }

    private function seedInitialAdmin(): void
    {
        $username = env('INITIAL_ADMIN_USERNAME');
        $password = env('INITIAL_ADMIN_PASSWORD');

        if (app()->environment('production') && (! $username || ! $password)) {
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

    private function shouldSeedDemoData(): bool
    {
        return ! app()->environment('production')
            && filter_var(env('SEED_DEMO_DATA', false), FILTER_VALIDATE_BOOL);
    }
}
