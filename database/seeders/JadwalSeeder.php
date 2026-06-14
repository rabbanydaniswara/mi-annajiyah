<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Jadwal;
use Illuminate\Database\Seeder;

class JadwalSeeder extends Seeder
{
    public function run()
    {
        if (Jadwal::exists()) {
            return;
        }

        $gurus = Guru::orderBy('urutan')->orderBy('nama')->get();
        if ($gurus->isEmpty()) {
            return;
        }

        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $mapels = [
            'Bahasa Indonesia', 'Matematika', 'IPA', 'IPS', 'Al-Qur\'an Hadits',
            'Akidah Akhlak', 'Fiqih', 'SKI', 'Bahasa Arab', 'PJOK', 'Seni Budaya',
        ];
        $kelas = ['1A', '1B', '2A', '2B', '3A', '4A', '5A', '6A'];

        foreach ($hari as $hariIndex => $h) {
            foreach ($kelas as $kelasIndex => $k) {
                $startTime = 7; // Start at 07:00
                $maxSessions = 4;

                for ($i = 0; $i < $maxSessions; $i++) {
                    $jamMulai = sprintf('%02d:00', $startTime);
                    $endTime = $startTime + 1; // 1 hour session
                    $jamSelesai = sprintf('%02d:00', $endTime);

                    Jadwal::create([
                        'hari' => $h,
                        'jam_mulai' => $jamMulai,
                        'jam_selesai' => $jamSelesai,
                        'mapel' => $mapels[($hariIndex + $kelasIndex + $i) % count($mapels)],
                        'id_guru' => $gurus[($hariIndex + $kelasIndex + $i) % $gurus->count()]->id,
                        'kelas' => $k,
                        'ruangan' => 'Ruang '.$k,
                    ]);

                    $startTime = $endTime; // No gap or small gap
                }
            }
        }
    }
}
