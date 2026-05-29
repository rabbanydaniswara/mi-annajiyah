<?php

namespace App\Http\Controllers;

use App\Helpers\PublicCacheHelper;
use App\Helpers\PpdbHelper;
use App\Models\Banner;
use App\Models\Fasilitas;
use App\Models\Guru;
use App\Models\KegiatanSekolah;
use App\Models\KegiatanKategori;
use App\Models\KontenWeb;
use App\Models\Siswa;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        $cachedContent = Cache::remember(PublicCacheHelper::HOME_CONTENT, now()->addHours(1), function () {
            $banners = Banner::where('aktif', 1)
                ->orderBy('urutan')
                ->get(['id', 'judul', 'subtitle', 'gambar', 'urutan', 'aktif'])
                ->map(fn ($banner) => $banner->only(['id', 'judul', 'subtitle', 'gambar', 'urutan', 'aktif']))
                ->all();

            if (empty($banners)) {
                $banners = [
                    ['judul' => 'MI Annajiyah', 'subtitle' => 'Madrasah ibtidaiyah yang membimbing anak tumbuh cerdas, santun, dan percaya diri.', 'gambar' => 'uploads/banner/banner2.jpg'],
                    ['judul' => 'PPDB 2026/2027 Telah Dibuka', 'subtitle' => 'Daftarkan putra-putri Anda sekarang juga!', 'gambar' => 'uploads/banner/banner1.jpg'],
                ];
            }

            $konten = KontenWeb::whereIn('tipe', ['visi', 'misi', 'sejarah'])
                ->get(['tipe', 'judul', 'konten', 'gambar', 'urutan'])
                ->keyBy('tipe')
                ->map(fn ($item) => $item->only(['tipe', 'judul', 'konten', 'gambar', 'urutan']));

            $kategoris = KegiatanKategori::get(['id', 'nama', 'warna']);
            $kegiatan  = collect();
            foreach ($kategoris as $kat) {
                $items = KegiatanSekolah::with('kategori:id,nama,warna')
                    ->where('kategori_id', $kat->id)
                    ->whereNotNull('gambar')
                    ->orderByDesc('tanggal')
                    ->limit(2)
                    ->get(['id', 'judul', 'deskripsi', 'gambar', 'tanggal', 'kategori_id']);
                $kegiatan = $kegiatan->merge($this->serializeActivities($items));
            }

            if ($kegiatan->count() < 6) {
                $existing = $kegiatan->pluck('id')->all();
                $extra = KegiatanSekolah::with('kategori:id,nama,warna')
                    ->whereNotNull('gambar')
                    ->whereNotIn('id', $existing)
                    ->orderByDesc('tanggal')
                    ->limit(6 - $kegiatan->count())
                    ->get(['id', 'judul', 'deskripsi', 'gambar', 'tanggal', 'kategori_id']);
                $kegiatan = $kegiatan->merge($this->serializeActivities($extra));
            }

            return [
                'banners' => $banners,
                'visi' => $konten->get('visi'),
                'misi' => $konten->get('misi'),
                'sejarah' => $konten->get('sejarah'),
                'kegiatan' => $kegiatan->shuffle()->take(6)->values()->all(),
                'guruList' => Guru::where('tampilkan', 1)
                    ->orderBy('urutan')
                    ->limit(7)
                    ->get(['id', 'nama', 'mapel', 'jabatan', 'nip', 'foto', 'urutan', 'tampilkan'])
                    ->map(fn ($guru) => $guru->only(['id', 'nama', 'mapel', 'jabatan', 'nip', 'foto', 'urutan', 'tampilkan']))
                    ->all(),
                'fasilitas' => Fasilitas::where('aktif', 1)
                    ->orderBy('urutan')
                    ->get(['id', 'nama', 'deskripsi', 'ikon', 'gambar', 'urutan', 'aktif'])
                    ->map(fn ($fasilitas) => $fasilitas->only(['id', 'nama', 'deskripsi', 'ikon', 'gambar', 'urutan', 'aktif']))
                    ->all(),
            ];
        });

        $content = [
            'banners' => $this->objectCollection($cachedContent['banners']),
            'visi' => $this->optionalObject($cachedContent['visi'] ?? null),
            'misi' => $this->optionalObject($cachedContent['misi'] ?? null),
            'sejarah' => $this->optionalObject($cachedContent['sejarah'] ?? null),
            'kegiatan' => $this->activityCollection($cachedContent['kegiatan']),
            'guruList' => $this->objectCollection($cachedContent['guruList']),
            'fasilitas' => $this->objectCollection($cachedContent['fasilitas']),
        ];

        $stats = Cache::remember(PublicCacheHelper::HOME_STATS, now()->addMinutes(5), function () {
            return [
                'totalSiswa' => Siswa::count(),
                'totalGuru' => Guru::count(),
                'totalPendaftar' => Siswa::where('status_ppdb', 'pending')->count(),
            ];
        });

        return view('public.index', array_merge($content, $stats));
    }

    public function pendaftaran()
    {
        return view('public.pendaftaran', [
            'ppdbTahunAjaran' => PpdbHelper::activeAcademicYear(),
        ]);
    }

    public function tenagaPendidik()
    {
        $guruList = $this->objectCollection(Cache::remember(PublicCacheHelper::GURU_LIST, now()->addHours(1), function () {
            return Guru::where('tampilkan', 1)
                ->orderBy('urutan')
                ->get(['id', 'nama', 'mapel', 'jabatan', 'nip', 'foto', 'urutan', 'tampilkan'])
                ->map(fn ($guru) => $guru->only(['id', 'nama', 'mapel', 'jabatan', 'nip', 'foto', 'urutan', 'tampilkan']))
                ->all();
        }));

        return view('public.guru', compact('guruList'));
    }

    public function fasilitas()
    {
        $fasilitas = $this->objectCollection(Cache::remember(PublicCacheHelper::FASILITAS_LIST, now()->addHours(1), function () {
            return Fasilitas::where('aktif', 1)
                ->orderBy('urutan')
                ->get(['id', 'nama', 'deskripsi', 'ikon', 'gambar', 'urutan', 'aktif'])
                ->map(fn ($fasilitas) => $fasilitas->only(['id', 'nama', 'deskripsi', 'ikon', 'gambar', 'urutan', 'aktif']))
                ->all();
        }));

        return view('public.fasilitas', compact('fasilitas'));
    }

    public function kegiatan()
    {
        $kategoris = $this->objectCollection(Cache::remember(PublicCacheHelper::KEGIATAN_CATEGORIES, now()->addHours(1), function () {
            return KegiatanKategori::withCount('kegiatan')
                ->orderBy('nama')
                ->get(['id', 'nama', 'warna'])
                ->map(fn ($kategori) => [
                    'id' => $kategori->id,
                    'nama' => $kategori->nama,
                    'warna' => $kategori->warna,
                    'kegiatan_count' => $kategori->kegiatan_count,
                ])
                ->all();
        }));

        $filterKategori = request('kategori');

        $kegiatan = KegiatanSekolah::with('kategori:id,nama,warna')
            ->when($filterKategori, fn ($query) => $query->where('kategori_id', $filterKategori))
            ->orderByDesc('tanggal')
            ->paginate(12, ['id', 'judul', 'deskripsi', 'gambar', 'tanggal', 'kategori_id']);

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

    private function serializeActivities($activities)
    {
        return $activities->map(fn ($activity) => [
            'id' => $activity->id,
            'judul' => $activity->judul,
            'deskripsi' => $activity->deskripsi,
            'gambar' => $activity->gambar,
            'tanggal' => $activity->tanggal?->toDateString(),
            'kategori_id' => $activity->kategori_id,
            'kategori' => $activity->kategori ? [
                'id' => $activity->kategori->id,
                'nama' => $activity->kategori->nama,
                'warna' => $activity->kategori->warna,
            ] : null,
        ]);
    }

    private function objectCollection(array $items)
    {
        return collect($items)->map(fn ($item) => (object) $item);
    }

    private function activityCollection(array $items)
    {
        return collect($items)->map(function ($item) {
            $activity = (object) $item;
            $activity->tanggal = $item['tanggal'] ? \Illuminate\Support\Carbon::parse($item['tanggal']) : null;
            $activity->kategori = $item['kategori'] ? (object) $item['kategori'] : null;

            return $activity;
        });
    }

    private function optionalObject($item): ?object
    {
        return $item ? (object) $item : null;
    }
}
