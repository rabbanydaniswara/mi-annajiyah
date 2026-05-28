<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Jadwal;
use App\Models\Guru;

class JadwalSeeder extends Seeder
{
    public function run()
    {
        $gurus = Guru::all();
        if ($gurus->isEmpty()) {
            return;
        }

        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $mapels = [
            'Bahasa Indonesia', 'Matematika', 'IPA', 'IPS', 'Al-Qur\'an Hadits', 
            'Akidah Akhlak', 'Fiqih', 'SKI', 'Bahasa Arab', 'PJOK', 'Seni Budaya'
        ];
        $kelas = ['1A', '1B', '2A', '2B', '3A', '4A', '5A', '6A'];

        foreach ($hari as $h) {
            foreach ($kelas as $k) {
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
                        'mapel' => $mapels[array_rand($mapels)],
                        'id_guru' => $gurus->random()->id,
                        'kelas' => $k,
                        'ruangan' => 'Ruang ' . $k
                    ]);

                    $startTime = $endTime; // No gap or small gap
                }
            }
        }
    }
}
