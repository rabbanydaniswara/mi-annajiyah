<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Helpers\ImageHelper;
use App\Helpers\PublicCacheHelper;
use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\Request;
use Throwable;

class FasilitasController extends Controller
{
    public function index()
    {
        $fasilitas = Fasilitas::orderBy('urutan')->get();
        $edit = request('edit') ? Fasilitas::find(request('edit')) : null;

        return view('admin.fasilitas', compact('fasilitas', 'edit'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'ikon' => 'nullable|string|max:100',
            'urutan' => 'nullable|integer',
            'gambar' => 'nullable|image|mimetypes:image/jpeg,image/png|max:3072',
        ]);

        $fas = $request->id ? Fasilitas::findOrFail($request->id) : null;
        $oldImage = $fas?->gambar;
        $newImage = null;
        $data = $request->only(['nama', 'deskripsi', 'ikon', 'urutan']);
        $data['aktif'] = $request->has('aktif') ? 1 : 0;

        try {
            if ($request->hasFile('gambar')) {
                $newImage = ImageHelper::uploadAndOptimize($request->file('gambar'), 'uploads/fasilitas', 'fasilitas');
                $data['gambar'] = $newImage;
                ImageHelper::generateThumbnailFor($newImage);
                ImageHelper::generateVariantFor($newImage, 'card', 560, 64);
            }

            if ($fas) {
                $fas->update($data);
                ActivityLogger::log('update_fasilitas', $fas, "Memperbarui fasilitas {$fas->nama}");
                $msg = 'Fasilitas berhasil diperbarui!';
            } else {
                $fas = Fasilitas::create($data);
                ActivityLogger::log('create_fasilitas', $fas, "Menambahkan fasilitas baru {$fas->nama}");
                $msg = 'Fasilitas berhasil ditambahkan!';
            }
        } catch (Throwable $e) {
            ImageHelper::deleteImageSet($newImage);
            throw $e;
        }

        if ($newImage && $oldImage) {
            ImageHelper::deleteImageSet($oldImage);
        }

        PublicCacheHelper::clearContent();

        return redirect()->route('admin.fasilitas')->with('success', $msg);
    }

    public function toggle($id)
    {
        $fas = Fasilitas::findOrFail($id);
        $fas->update(['aktif' => ! $fas->aktif]);
        $status = $fas->aktif ? 'Mengaktifkan' : 'Menonaktifkan';
        ActivityLogger::log('toggle_fasilitas', $fas, "{$status} fasilitas {$fas->nama}");
        PublicCacheHelper::clearContent();

        return redirect()->route('admin.fasilitas');
    }

    public function destroy($id)
    {
        $fas = Fasilitas::findOrFail($id);
        $nama = $fas->nama;
        $image = $fas->gambar;
        $fas->delete();
        ImageHelper::deleteImageSet($image);
        ActivityLogger::log('delete_fasilitas', null, "Menghapus fasilitas {$nama}");
        PublicCacheHelper::clearContent();

        return redirect()->route('admin.fasilitas')->with('success', 'Fasilitas berhasil dihapus!');
    }
}
