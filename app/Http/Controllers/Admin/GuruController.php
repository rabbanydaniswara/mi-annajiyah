<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Helpers\ImageHelper;
use App\Helpers\PublicCacheHelper;
use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;
use Throwable;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = Guru::query();
        if ($q = trim((string) $request->q)) {
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%$q%")
                    ->orWhere('mapel', 'like', "%$q%")
                    ->orWhere('jabatan', 'like', "%$q%")
                    ->orWhere('nip', 'like', "%$q%");
            });
        }
        $guru = $query->orderBy('urutan')->orderBy('nama')->paginate(15)->withQueryString();
        $edit = $request->edit ? Guru::find($request->edit) : null;
        $totalGuru = Guru::count();
        $totalMapel = Guru::distinct('mapel')->count('mapel');

        return view('admin.guru', compact('guru', 'edit', 'totalGuru', 'totalMapel'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id' => 'nullable|exists:guru,id',
            'nama' => 'required|string|max:100',
            'mapel' => 'required|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'nip' => 'nullable|string|max:50',
            'no_telp' => 'nullable|string|max:20',
            'urutan' => 'nullable|integer',
            'foto' => 'nullable|image|mimetypes:image/jpeg,image/png|max:3072',
        ]);

        $guru = $request->id ? Guru::findOrFail($request->id) : null;
        $oldPhoto = $guru?->foto;
        $newPhoto = null;
        $data = $request->only(['nama', 'mapel', 'jabatan', 'nip', 'no_telp', 'urutan']);
        $data['tampilkan'] = $request->has('tampilkan') ? 1 : 0;

        try {
            if ($request->hasFile('foto')) {
                $newPhoto = ImageHelper::uploadAndOptimize($request->file('foto'), 'uploads/guru', 'guru');
                $data['foto'] = $newPhoto;
                ImageHelper::generateThumbnailFor($newPhoto);
                ImageHelper::generateVariantFor($newPhoto, 'card', 480, 64);
            }

            if ($guru) {
                $guru->update($data);
                ActivityLogger::log('update_guru', $guru, "Memperbarui data guru {$guru->nama}");
            } else {
                $guru = Guru::create($data);
                ActivityLogger::log('create_guru', $guru, "Menambahkan guru baru {$guru->nama}");
            }
        } catch (Throwable $e) {
            ImageHelper::deleteImageSet($newPhoto);
            throw $e;
        }

        if ($newPhoto && $oldPhoto) {
            ImageHelper::deleteImageSet($oldPhoto);
        }

        PublicCacheHelper::clearContent();

        return redirect()->route('admin.guru')->with('success', 'Data guru berhasil disimpan');
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        $namaGuru = $guru->nama;

        if ($guru->jadwal()->count() > 0) {
            return redirect()->route('admin.guru')->with('error', 'Guru masih memiliki jadwal, tidak bisa dihapus!');
        }
        $photo = $guru->foto;

        ActivityLogger::log('delete_guru', null, "Menghapus data guru {$namaGuru}");
        $guru->delete();
        ImageHelper::deleteImageSet($photo);
        PublicCacheHelper::clearContent();

        return redirect()->route('admin.guru')->with('success', 'Guru berhasil dihapus');
    }
}
