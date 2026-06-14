<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Helpers\ImageHelper;
use App\Helpers\PhoneHelper;
use App\Helpers\PpdbHelper;
use App\Helpers\PublicCacheHelper;
use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\KegiatanKategori;
use App\Models\KegiatanSekolah;
use App\Models\KontenWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class KontenController extends Controller
{
    public function index()
    {
        $visi = KontenWeb::where('tipe', 'visi')->first();
        $misi = KontenWeb::where('tipe', 'misi')->first();
        $sejarah = KontenWeb::where('tipe', 'sejarah')->first();
        $kegiatan = KegiatanSekolah::with('kategori')->orderByDesc('tanggal')->get();
        $kategoris = KegiatanKategori::orderBy('nama')->get();
        $banners = Banner::orderBy('urutan')->get();
        $editKegiatan = request('edit_kegiatan') ? KegiatanSekolah::find(request('edit_kegiatan')) : null;
        $editKategori = request('edit_kategori') ? KegiatanKategori::find(request('edit_kategori')) : null;
        $editBanner = request('edit_banner') ? Banner::find(request('edit_banner')) : null;
        $ppdbSettings = PpdbHelper::settings();

        return view('admin.konten', compact(
            'visi',
            'misi',
            'sejarah',
            'kegiatan',
            'kategoris',
            'banners',
            'editKegiatan',
            'editKategori',
            'editBanner',
            'ppdbSettings'
        ));
    }

    public function updateKonten(Request $request)
    {
        $base = $request->validate([
            'tipe' => ['required', Rule::in(['visi', 'misi', 'sejarah', 'ppdb_settings', 'kontak_multi'])],
        ]);

        if ($base['tipe'] === 'ppdb_settings') {
            $validated = $request->validate([
                'tahun_ajaran' => ['required', 'regex:/^\d{4}[\/-]\d{4}$/'],
                'status_pendaftaran' => ['required', Rule::in(['open', 'closed'])],
                'pesan_tutup' => ['nullable', 'string', 'max:1000', 'required_if:status_pendaftaran,closed'],
            ], [
                'tahun_ajaran.regex' => 'Format tahun ajaran harus seperti 2026/2027.',
                'status_pendaftaran.required' => 'Status pendaftaran wajib dipilih.',
                'pesan_tutup.required_if' => 'Pesan publik wajib diisi ketika PPDB ditutup.',
            ]);

            DB::transaction(function () use ($validated): void {
                $settings = [
                    'ppdb_tahun_ajaran' => [
                        'judul' => 'Tahun Ajaran PPDB Aktif',
                        'konten' => PpdbHelper::normalizeAcademicYear($validated['tahun_ajaran']),
                        'urutan' => 20,
                    ],
                    'ppdb_status' => [
                        'judul' => 'Status Pendaftaran PPDB',
                        'konten' => $validated['status_pendaftaran'],
                        'urutan' => 21,
                    ],
                    'ppdb_pesan_tutup' => [
                        'judul' => 'Pesan Publik Saat PPDB Ditutup',
                        'konten' => trim((string) ($validated['pesan_tutup'] ?? '')) ?: PpdbHelper::DEFAULT_CLOSED_MESSAGE,
                        'urutan' => 22,
                    ],
                ];

                foreach ($settings as $type => $data) {
                    KontenWeb::updateOrCreate(['tipe' => $type], $data);
                }
            });

            $statusLabel = $validated['status_pendaftaran'] === 'open' ? 'dibuka' : 'ditutup';
            ActivityLogger::log('update_ppdb_settings', null, "Memperbarui pengaturan PPDB; pendaftaran {$statusLabel}");
            PublicCacheHelper::clearContent();

            return redirect()->route('admin.konten', ['tab' => 'ppdb'])->with('success', 'Pengaturan PPDB berhasil disimpan!');
        }

        if ($base['tipe'] === 'kontak_multi') {
            $contactItems = collect($request->input('konten_items', []))
                ->map(fn ($value) => is_string($value) ? trim($value) : $value)
                ->all();
            $request->merge(['konten_items' => $contactItems]);

            $validator = Validator::make($request->all(), [
                'konten_items' => ['required', 'array:alamat,telepon,email,wa,ig,tiktok,jam_op'],
                'konten_items.alamat' => ['nullable', 'string', 'max:500'],
                'konten_items.telepon' => ['nullable', 'string', 'max:50'],
                'konten_items.email' => ['nullable', 'email', 'max:150'],
                'konten_items.wa' => [
                    'nullable',
                    'string',
                    'max:30',
                    function (string $attribute, mixed $value, \Closure $fail): void {
                        if ($value !== null && $value !== '' && PhoneHelper::sanitizeIndonesianWhatsapp((string) $value) === null) {
                            $fail('Nomor WhatsApp harus memakai format Indonesia, contoh 081234567890.');
                        }
                    },
                ],
                'konten_items.ig' => ['nullable', 'url', 'max:255'],
                'konten_items.tiktok' => ['nullable', 'url', 'max:255'],
                'konten_items.jam_op' => ['nullable', 'string', 'max:500'],
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->route('admin.konten', ['tab' => 'kontak'])
                    ->withErrors($validator)
                    ->withInput()
                    ->with('contact_validation_failed', true);
            }

            $validated = $validator->validated();

            $contacts = [
                'alamat' => ['judul' => 'Alamat', 'urutan' => 4],
                'telepon' => ['judul' => 'Telepon', 'urutan' => 5],
                'email' => ['judul' => 'Email', 'urutan' => 6],
                'jam_op' => ['judul' => 'Jam Operasional', 'urutan' => 7],
                'wa' => ['judul' => 'WhatsApp', 'urutan' => 8],
                'ig' => ['judul' => 'Instagram', 'urutan' => 9],
                'tiktok' => ['judul' => 'TikTok', 'urutan' => 10],
            ];

            DB::transaction(function () use ($contacts, $validated): void {
                foreach ($contacts as $tipe => $metadata) {
                    $value = $validated['konten_items'][$tipe] ?? null;

                    if ($tipe === 'wa' && $value !== null && $value !== '') {
                        $value = PhoneHelper::sanitizeIndonesianWhatsapp($value);
                        if ($value === null) {
                            throw ValidationException::withMessages([
                                'konten_items.wa' => 'Nomor WhatsApp tidak valid.',
                            ]);
                        }
                    }

                    KontenWeb::updateOrCreate(
                        ['tipe' => $tipe],
                        [
                            'judul' => $metadata['judul'],
                            'konten' => $value === '' ? null : $value,
                            'urutan' => $metadata['urutan'],
                        ]
                    );
                }
            });

            ActivityLogger::log('update_konten', null, 'Memperbarui informasi kontak website');
            PublicCacheHelper::clearContent();

            return redirect()->route('admin.konten', ['tab' => 'kontak'])->with('success', 'Kontak berhasil diupdate!');
        }

        $validated = $request->validate([
            'konten' => ['nullable', 'string', 'max:10000'],
        ]);

        KontenWeb::updateOrCreate(
            ['tipe' => $base['tipe']],
            ['konten' => $validated['konten'] ?? '']
        );
        ActivityLogger::log('update_konten', null, "Memperbarui konten website: {$base['tipe']}");
        PublicCacheHelper::clearContent();

        return redirect()->route('admin.konten', ['tab' => $base['tipe']])->with('success', 'Konten berhasil disimpan!');
    }

    public function storeKegiatan(Request $request)
    {
        $request->validate([
            'id' => 'nullable|exists:kegiatan_sekolah,id',
            'judul' => 'required|string|max:200',
            'deskripsi' => 'nullable|string|max:1000',
            'tanggal' => 'required|date',
            'kategori_id' => 'nullable|exists:kegiatan_kategori,id',
            'gambar_kegiatan' => 'nullable|image|mimetypes:image/jpeg,image/png|max:5120',
        ]);

        $kegiatan = $request->id ? KegiatanSekolah::findOrFail($request->id) : null;
        $oldImage = $kegiatan?->gambar;
        $newImage = null;
        $data = [
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'tanggal' => $request->tanggal,
            'kategori_id' => $request->kategori_id ?: null,
        ];

        try {
            if ($request->hasFile('gambar_kegiatan')) {
                $newImage = ImageHelper::uploadAndOptimize($request->file('gambar_kegiatan'), 'uploads/kegiatan', 'kegiatan');
                $data['gambar'] = $newImage;
                ImageHelper::generateThumbnailFor($newImage);
                ImageHelper::generateVariantFor($newImage, 'card', 420, 45);
            }

            if ($kegiatan) {
                $kegiatan->update($data);
                ActivityLogger::log('update_kegiatan', $kegiatan, "Memperbarui kegiatan {$kegiatan->judul}");
                $message = 'Kegiatan berhasil diperbarui';
            } else {
                $kegiatan = KegiatanSekolah::create($data);
                ActivityLogger::log('create_kegiatan', $kegiatan, "Menambahkan kegiatan baru {$kegiatan->judul}");
                $message = 'Kegiatan berhasil ditambahkan';
            }
        } catch (Throwable $e) {
            ImageHelper::deleteImageSet($newImage);
            throw $e;
        }

        if ($newImage && $oldImage) {
            ImageHelper::deleteImageSet($oldImage);
        }

        PublicCacheHelper::clearContent();

        return redirect()->route('admin.konten', ['tab' => 'kegiatan'])->with('success', $message);
    }

    public function destroyKegiatan($id)
    {
        $keg = KegiatanSekolah::findOrFail($id);
        $judul = $keg->judul;
        $image = $keg->gambar;
        $keg->delete();
        ImageHelper::deleteImageSet($image);
        ActivityLogger::log('delete_kegiatan', null, "Menghapus kegiatan {$judul}");
        PublicCacheHelper::clearContent();

        return redirect()->route('admin.konten', ['tab' => 'kegiatan'])->with('success', 'Kegiatan berhasil dihapus');
    }

    // --- Kategori Kegiatan ---
    public function storeKategori(Request $request)
    {
        $request->validate([
            'id' => 'nullable|exists:kegiatan_kategori,id',
            'nama' => 'required|string|max:100',
            'warna' => 'nullable|string|max:30',
        ]);

        $kat = $request->id ? KegiatanKategori::findOrFail($request->id) : null;
        $data = ['nama' => $request->nama, 'warna' => $request->warna ?? 'green'];

        if ($kat) {
            $kat->update($data);
            ActivityLogger::log('update_kategori', $kat, "Memperbarui kategori kegiatan {$kat->nama}");
            $message = 'Kategori berhasil diperbarui!';
        } else {
            $kat = KegiatanKategori::create($data);
            ActivityLogger::log('create_kategori', $kat, "Menambahkan kategori kegiatan {$kat->nama}");
            $message = 'Kategori berhasil ditambahkan!';
        }

        PublicCacheHelper::clearContent();

        return redirect()->route('admin.konten', ['tab' => 'kegiatan'])->with('success', $message);
    }

    public function destroyKategori($id)
    {
        $kategori = KegiatanKategori::findOrFail($id);
        $namaKat = $kategori->nama;
        $images = $kategori->kegiatan->pluck('gambar')->filter()->all();

        DB::transaction(function () use ($kategori): void {
            $kategori->kegiatan()->delete();
            $kategori->delete();
        });

        foreach ($images as $image) {
            ImageHelper::deleteImageSet($image);
        }
        ActivityLogger::log('delete_kategori', null, "Menghapus kategori kegiatan {$namaKat} beserta seluruh kegiatannya");
        PublicCacheHelper::clearContent();

        return redirect()->route('admin.konten', ['tab' => 'kegiatan'])->with('success', 'Kategori berhasil dihapus!');
    }

    // --- Banner ---
    public function storeBanner(Request $request)
    {
        $validated = $request->validate([
            'judul_banner' => 'required|string|max:200',
            'subtitle_banner' => 'nullable|string|max:255',
            'urutan_banner' => 'nullable|integer|min:0|max:999',
            'gambar_banner' => 'required|image|mimetypes:image/jpeg,image/png|max:5120',
        ]);

        $gambar = null;

        try {
            $gambar = ImageHelper::uploadAndOptimize($request->file('gambar_banner'), 'uploads/banner', 'banner');
            ImageHelper::generateVariantFor($gambar, 'hero', 1600, 72);
            ImageHelper::generateVariantFor($gambar, 'card', 640, 68);

            $banner = Banner::create([
                'judul' => $validated['judul_banner'],
                'subtitle' => $validated['subtitle_banner'] ?? null,
                'gambar' => $gambar,
                'urutan' => $validated['urutan_banner'] ?? 1,
                'aktif' => true,
            ]);
        } catch (Throwable $e) {
            ImageHelper::deleteImageSet($gambar);
            throw $e;
        }

        ActivityLogger::log('create_banner', $banner, "Menambahkan banner baru {$banner->judul}");
        PublicCacheHelper::clearContent();

        return redirect()->route('admin.konten', ['tab' => 'banner'])->with('success', 'Banner berhasil ditambahkan');
    }

    public function updateBanner(Request $request)
    {
        $validated = $request->validate([
            'banner_id' => 'required|exists:banner,id',
            'judul_banner' => 'required|string|max:200',
            'subtitle_banner' => 'nullable|string|max:255',
            'urutan_banner' => 'nullable|integer|min:0|max:999',
            'gambar_banner_edit' => 'nullable|image|mimetypes:image/jpeg,image/png|max:5120',
        ]);

        $banner = Banner::findOrFail($validated['banner_id']);
        $oldImage = $banner->gambar;
        $newImage = null;
        $data = [
            'judul' => $validated['judul_banner'],
            'subtitle' => $validated['subtitle_banner'] ?? null,
            'urutan' => $validated['urutan_banner'] ?? 1,
        ];

        try {
            if ($request->hasFile('gambar_banner_edit')) {
                $newImage = ImageHelper::uploadAndOptimize($request->file('gambar_banner_edit'), 'uploads/banner', 'banner');
                $data['gambar'] = $newImage;
                ImageHelper::generateThumbnailFor($newImage);
                ImageHelper::generateVariantFor($newImage, 'hero', 1600, 72);
                ImageHelper::generateVariantFor($newImage, 'card', 640, 68);
            }

            $banner->update($data);
        } catch (Throwable $e) {
            ImageHelper::deleteImageSet($newImage);
            throw $e;
        }

        if ($newImage) {
            ImageHelper::deleteImageSet($oldImage);
        }

        ActivityLogger::log('update_banner', $banner, "Memperbarui banner {$banner->judul}");
        PublicCacheHelper::clearContent();

        return redirect()->route('admin.konten', ['tab' => 'banner'])->with('success', 'Banner berhasil diupdate');
    }

    public function toggleBanner($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->update(['aktif' => ! $banner->aktif]);
        $status = $banner->aktif ? 'Mengaktifkan' : 'Menonaktifkan';
        ActivityLogger::log('toggle_banner', $banner, "{$status} banner {$banner->judul}");
        PublicCacheHelper::clearContent();

        return redirect()->route('admin.konten', ['tab' => 'banner']);
    }

    public function destroyBanner($id)
    {
        $banner = Banner::findOrFail($id);
        $judul = $banner->judul;
        $image = $banner->gambar;
        $banner->delete();
        ImageHelper::deleteImageSet($image);
        ActivityLogger::log('delete_banner', null, "Menghapus banner {$judul}");
        PublicCacheHelper::clearContent();

        return redirect()->route('admin.konten', ['tab' => 'banner'])->with('success', 'Banner berhasil dihapus');
    }
}
