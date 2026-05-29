<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ImageHelper;
use App\Helpers\PublicCacheHelper;
use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\Request;

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
            'nama'    => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'ikon'    => 'nullable|string|max:100',
            'urutan'  => 'nullable|integer',
            'gambar'  => 'nullable|image|mimetypes:image/jpeg,image/png|max:3072',
        ]);

        $data = $request->only(['nama', 'deskripsi', 'ikon', 'urutan']);
        $data['aktif'] = $request->has('aktif') ? 1 : 0;

        if ($request->hasFile('gambar')) {
            $data['gambar'] = ImageHelper::uploadAndOptimize($request->file('gambar'), 'uploads/fasilitas', 'fasilitas');
            ImageHelper::generateThumbnailFor($data['gambar']);
            ImageHelper::generateVariantFor($data['gambar'], 'card', 560, 64);
        }

        if ($request->id) {
            $fas = Fasilitas::findOrFail($request->id);
            if (isset($data['gambar']) && $fas->gambar && file_exists(public_path($fas->gambar))) {
                @unlink(public_path($fas->gambar));
                ImageHelper::deleteThumbnail($fas->gambar);
            }
            $fas->update($data);
            \App\Helpers\ActivityLogger::log('update_fasilitas', $fas, "Memperbarui fasilitas {$fas->nama}");
            $msg = 'Fasilitas berhasil diperbarui!';
        } else {
            $fas = Fasilitas::create($data);
            \App\Helpers\ActivityLogger::log('create_fasilitas', $fas, "Menambahkan fasilitas baru {$fas->nama}");
            $msg = 'Fasilitas berhasil ditambahkan!';
        }

        PublicCacheHelper::clearContent();

        return redirect()->route('admin.fasilitas')->with('success', $msg);
    }

    public function toggle($id)
    {
        $fas = Fasilitas::findOrFail($id);
        $fas->update(['aktif' => !$fas->aktif]);
        $status = $fas->aktif ? 'Mengaktifkan' : 'Menonaktifkan';
        \App\Helpers\ActivityLogger::log('toggle_fasilitas', $fas, "{$status} fasilitas {$fas->nama}");
        PublicCacheHelper::clearContent();
        return redirect()->route('admin.fasilitas');
    }

    public function destroy($id)
    {
        $fas = Fasilitas::findOrFail($id);
        $nama = $fas->nama;
        if ($fas->gambar && file_exists(public_path($fas->gambar))) {
            @unlink(public_path($fas->gambar));
            ImageHelper::deleteThumbnail($fas->gambar);
        }
        $fas->delete();
        \App\Helpers\ActivityLogger::log('delete_fasilitas', null, "Menghapus fasilitas {$nama}");
        PublicCacheHelper::clearContent();
        return redirect()->route('admin.fasilitas')->with('success', 'Fasilitas berhasil dihapus!');
    }
}
