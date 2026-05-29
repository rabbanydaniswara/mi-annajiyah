<?php

namespace App\Http\Controllers;

use App\Helpers\PpdbHelper;
use App\Models\Banner;
use App\Models\Fasilitas;
use App\Models\Guru;
use App\Models\KegiatanSekolah;
use App\Models\KegiatanKategori;
use App\Models\KontenWeb;
use App\Models\Siswa;

class HomeController extends Controller
{
    public function index()
    {
        $banners = Banner::where('aktif', 1)->orderBy('urutan')->get();
        if ($banners->isEmpty()) {
            $banners = collect([
                (object)['judul' => 'MI Annajiyah', 'subtitle' => 'Madrasah ibtidaiyah yang membimbing anak tumbuh cerdas, santun, dan percaya diri.', 'gambar' => 'uploads/banner/banner2.jpg'],
                (object)['judul' => 'PPDB 2026/2027 Telah Dibuka', 'subtitle' => 'Daftarkan putra-putri Anda sekarang juga!', 'gambar' => 'uploads/banner/banner1.jpg'],
            ]);
        }

        $visi     = KontenWeb::where('tipe', 'visi')->first();
        $misi     = KontenWeb::where('tipe', 'misi')->first();
        $sejarah  = KontenWeb::where('tipe', 'sejarah')->first();
        // Ambil 1-2 kegiatan per kategori agar bervariasi di homepage
        $kategoris = KegiatanKategori::all();
        $kegiatan  = collect();
        foreach ($kategoris as $kat) {
            $items = KegiatanSekolah::with('kategori')
                ->where('kategori_id', $kat->id)
                ->whereNotNull('gambar')
                ->orderByDesc('tanggal')
                ->limit(2)
                ->get();
            $kegiatan = $kegiatan->merge($items);
        }
        // Jika kurang dari 6 (kategori sedikit), tambah dari yang belum masuk
        if ($kegiatan->count() < 6) {
            $existing = $kegiatan->pluck('id')->toArray();
            $extra = KegiatanSekolah::with('kategori')
                ->whereNotNull('gambar')
                ->whereNotIn('id', $existing)
                ->orderByDesc('tanggal')
                ->limit(6 - $kegiatan->count())
                ->get();
            $kegiatan = $kegiatan->merge($extra);
        }
        // Acak urutan agar tidak monoton, lalu ambil 6
        $kegiatan = $kegiatan->shuffle()->take(6);
        $guruList = Guru::where('tampilkan', 1)->orderBy('urutan')->limit(7)->get();
        $fasilitas = Fasilitas::where('aktif', 1)->orderBy('urutan')->get();

        $totalSiswa    = Siswa::count();
        $totalGuru     = Guru::count();
        $totalPendaftar = Siswa::where('status_ppdb', 'pending')->count();

        return view('public.index', compact(
            'banners', 'visi', 'misi', 'sejarah', 'kegiatan', 'guruList', 'fasilitas',
            'totalSiswa', 'totalGuru', 'totalPendaftar'
        ));
    }

    public function pendaftaran()
    {
        return view('public.pendaftaran', [
            'ppdbTahunAjaran' => PpdbHelper::activeAcademicYear(),
        ]);
    }

    public function tenagaPendidik()
    {
        $guruList = Guru::where('tampilkan', 1)->orderBy('urutan')->get();
        return view('public.guru', compact('guruList'));
    }

    public function fasilitas()
    {
        $fasilitas = Fasilitas::where('aktif', 1)->orderBy('urutan')->get();
        return view('public.fasilitas', compact('fasilitas'));
    }

    public function kegiatan()
    {
        $kategoris = KegiatanKategori::with(['kegiatan' => function ($q) {
            $q->orderByDesc('tanggal');
        }])->orderBy('nama')->get();

        $allKegiatan = KegiatanSekolah::with('kategori')->orderByDesc('tanggal')->get();
        $filterKategori = request('kategori');

        if ($filterKategori) {
            $kegiatan = KegiatanSekolah::with('kategori')
                ->where('kategori_id', $filterKategori)
                ->orderByDesc('tanggal')
                ->paginate(12);
        } else {
            $kegiatan = KegiatanSekolah::with('kategori')
                ->orderByDesc('tanggal')
                ->paginate(12);
        }

        return view('public.kegiatan', compact('kegiatan', 'kategoris', 'filterKategori'));
    }

    public function cekPendaftaran()
    {
        $cari = trim((string) request('q', ''));
        $hasil = null;

        if ($cari !== '' && mb_strlen($cari) <= 30) {
            $hasil = Siswa::where('nomor_pendaftaran', $cari)
                ->orWhere('nisn', $cari)
                ->orWhere('no_wa', $cari)
                ->orWhere('nis', $cari)
                ->orderByDesc('id')
                ->first();
        }

        return view('public.cek-pendaftaran', compact('cari', 'hasil'));
    }
}
