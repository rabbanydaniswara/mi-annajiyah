<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PpdbHelper;
use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jadwal;
use App\Models\KegiatanSekolah;
use App\Models\Siswa;

class DashboardController extends Controller
{
    public function index()
    {
        $totalSiswa = Siswa::count();
        $totalGuru = Guru::count();
        $totalJadwal = Jadwal::count();
        $totalPendaftar = Siswa::where('status_ppdb', 'pending')->count();
        $totalDiterima = Siswa::where('status_ppdb', 'diterima')->count();
        $totalKegiatan = KegiatanSekolah::count();
        $statusOptions = PpdbHelper::statusOptions();
        $ppdbSettings = PpdbHelper::settings();
        $ppdbStatusCounts = Siswa::selectRaw('status_ppdb, COUNT(*) as total')
            ->groupBy('status_ppdb')
            ->pluck('total', 'status_ppdb');
        $perluTindakLanjut = Siswa::whereIn('status_ppdb', ['pending', 'berkas_kurang'])
            ->orderByDesc('tanggal_daftar')
            ->limit(5)
            ->get();

        // Chart data (7 hari terakhir)
        $chartData = [];
        $chartLabels = [];

        $stats = Siswa::selectRaw('DATE(tanggal_daftar) as date, COUNT(*) as count')
            ->where('tanggal_daftar', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date');

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $chartLabels[] = now()->subDays($i)->format('d M');
            $chartData[] = $stats[$date] ?? 0;
        }

        // Jadwal Hari Ini
        $today = now()->translatedFormat('l'); // Senin, Selasa, etc.
        $jadwalHariIni = Jadwal::with('guru')
            ->where('hari', $today)
            ->orderBy('jam_mulai')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalSiswa', 'totalGuru', 'totalJadwal',
            'totalPendaftar', 'totalDiterima', 'totalKegiatan',
            'chartData', 'chartLabels', 'jadwalHariIni',
            'statusOptions', 'ppdbStatusCounts', 'perluTindakLanjut', 'ppdbSettings'
        ));
    }
}
