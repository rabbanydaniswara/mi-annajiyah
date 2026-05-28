<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{KontenWeb, KegiatanSekolah, KegiatanKategori, Banner};
use Illuminate\Http\Request;

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
        $editBanner = request('edit_banner') ? Banner::find(request('edit_banner')) : null;

        return view('admin.konten', compact('visi', 'misi', 'sejarah', 'kegiatan', 'kategoris', 'banners', 'editBanner'));
    }

    public function updateKonten(Request $request)
    {
        if ($request->tipe === 'kontak_multi') {
            foreach ($request->konten_items as $tipe => $value) {
                KontenWeb::updateOrCreate(['tipe' => $tipe], ['konten' => $value]);
            }
            \App\Helpers\ActivityLogger::log('update_konten', null, "Memperbarui informasi kontak website");
            return redirect()->route('admin.konten', ['tab' => 'kontak'])->with('success', 'Kontak berhasil diupdate!');
        }

        KontenWeb::updateOrCreate(
            ['tipe' => $request->tipe],
            ['konten' => $request->konten]
        );
        \App\Helpers\ActivityLogger::log('update_konten', null, "Memperbarui konten website: {$request->tipe}");
        return redirect()->route('admin.konten', ['tab' => $request->tipe])->with('success', 'Konten berhasil disimpan!');
    }

    public function storeKegiatan(Request $request)
    {
        $request->validate([
            'judul'          => 'required|string|max:200',
            'tanggal'        => 'required|date',
            'kategori_id'    => 'nullable|exists:kegiatan_kategori,id',
            'gambar_kegiatan' => 'nullable|image|mimetypes:image/jpeg,image/png|max:5120',
        ]);

        $gambar = null;
        if ($request->hasFile('gambar_kegiatan')) {
            $gambar = \App\Helpers\ImageHelper::uploadAndOptimize($request->file('gambar_kegiatan'), 'uploads/kegiatan', 'kegiatan');
            \App\Helpers\ImageHelper::generateThumbnailFor($gambar);
        }

        $kegiatan = KegiatanSekolah::create([
            'judul'      => $request->judul,
            'deskripsi'  => $request->deskripsi,
            'tanggal'    => $request->tanggal,
            'kategori_id' => $request->kategori_id ?: null,
            'gambar'     => $gambar,
        ]);

        \App\Helpers\ActivityLogger::log('create_kegiatan', $kegiatan, "Menambahkan kegiatan baru {$kegiatan->judul}");

        return redirect()->route('admin.konten')->with('success', 'Kegiatan berhasil ditambahkan');
    }

    public function destroyKegiatan($id)
    {
        $keg = KegiatanSekolah::findOrFail($id);
        $judul = $keg->judul;
        if ($keg->gambar && file_exists(public_path($keg->gambar))) {
            @unlink(public_path($keg->gambar));
            \App\Helpers\ImageHelper::deleteThumbnail($keg->gambar);
        }
        $keg->delete();
        \App\Helpers\ActivityLogger::log('delete_kegiatan', null, "Menghapus kegiatan {$judul}");
        return redirect()->route('admin.konten')->with('success', 'Kegiatan berhasil dihapus');
    }

    // --- Kategori Kegiatan ---
    public function storeKategori(Request $request)
    {
        $request->validate(['nama' => 'required|string|max:100', 'warna' => 'nullable|string|max:30']);
        $kat = KegiatanKategori::create(['nama' => $request->nama, 'warna' => $request->warna ?? 'green']);
        \App\Helpers\ActivityLogger::log('create_kategori', $kat, "Menambahkan kategori kegiatan {$kat->nama}");
        return redirect()->route('admin.konten')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function destroyKategori($id)
    {
        $kategori = KegiatanKategori::findOrFail($id);
        $namaKat = $kategori->nama;
        // Hapus kegiatan terkait beserta gambarnya
        foreach ($kategori->kegiatan as $keg) {
            if ($keg->gambar && file_exists(public_path($keg->gambar))) {
                @unlink(public_path($keg->gambar));
                \App\Helpers\ImageHelper::deleteThumbnail($keg->gambar);
            }
            $keg->delete();
        }
        $kategori->delete();
        \App\Helpers\ActivityLogger::log('delete_kategori', null, "Menghapus kategori kegiatan {$namaKat} beserta seluruh kegiatannya");
        return redirect()->route('admin.konten')->with('success', 'Kategori berhasil dihapus!');
    }

    // --- Banner ---
    public function storeBanner(Request $request)
    {
        $request->validate([
            'judul_banner'  => 'required|string|max:200',
            'gambar_banner' => 'required|image|mimetypes:image/jpeg,image/png|max:5120',
        ]);

        $gambar = \App\Helpers\ImageHelper::uploadAndOptimize($request->file('gambar_banner'), 'uploads/banner', 'banner');

        $banner = Banner::create([
            'judul'    => $request->judul_banner,
            'subtitle' => $request->subtitle_banner,
            'gambar'   => $gambar,
            'urutan'   => $request->urutan_banner ?? 1,
            'aktif'    => true,
        ]);

        \App\Helpers\ActivityLogger::log('create_banner', $banner, "Menambahkan banner baru {$banner->judul}");

        return redirect()->route('admin.konten')->with('success', 'Banner berhasil ditambahkan');
    }

    public function updateBanner(Request $request)
    {
        $banner = Banner::findOrFail($request->banner_id);
        $data = [
            'judul'    => $request->judul_banner,
            'subtitle' => $request->subtitle_banner,
            'urutan'   => $request->urutan_banner,
        ];

        if ($request->hasFile('gambar_banner_edit')) {
            if ($banner->gambar && file_exists(public_path($banner->gambar))) {
                @unlink(public_path($banner->gambar));
                \App\Helpers\ImageHelper::deleteThumbnail($banner->gambar);
            }
            $data['gambar'] = \App\Helpers\ImageHelper::uploadAndOptimize($request->file('gambar_banner_edit'), 'uploads/banner', 'banner');
            \App\Helpers\ImageHelper::generateThumbnailFor($data['gambar']);
        }

        $banner->update($data);
        \App\Helpers\ActivityLogger::log('update_banner', $banner, "Memperbarui banner {$banner->judul}");
        return redirect()->route('admin.konten')->with('success', 'Banner berhasil diupdate');
    }

    public function toggleBanner($id)
    {
        $banner = Banner::findOrFail($id);
        $banner->update(['aktif' => !$banner->aktif]);
        $status = $banner->aktif ? 'Mengaktifkan' : 'Menonaktifkan';
        \App\Helpers\ActivityLogger::log('toggle_banner', $banner, "{$status} banner {$banner->judul}");
        return redirect()->route('admin.konten');
    }

    public function destroyBanner($id)
    {
        $banner = Banner::findOrFail($id);
        $judul = $banner->judul;
        if ($banner->gambar && file_exists(public_path($banner->gambar))) {
            @unlink(public_path($banner->gambar));
            \App\Helpers\ImageHelper::deleteThumbnail($banner->gambar);
        }
        $banner->delete();
        \App\Helpers\ActivityLogger::log('delete_banner', null, "Menghapus banner {$judul}");
        return redirect()->route('admin.konten')->with('success', 'Banner berhasil dihapus');
    }
}
