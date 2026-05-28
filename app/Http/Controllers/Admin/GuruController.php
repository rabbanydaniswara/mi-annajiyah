<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use Illuminate\Http\Request;

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
            'id'     => 'nullable|exists:guru,id',
            'nama'   => 'required|string|max:100',
            'mapel'  => 'required|string|max:100',
            'jabatan' => 'nullable|string|max:100',
            'nip'    => 'nullable|string|max:50',
            'no_telp' => 'nullable|string|max:20',
            'urutan' => 'nullable|integer',
            'foto'   => 'nullable|image|mimetypes:image/jpeg,image/png|max:3072',
        ]);

        $data = $request->only(['nama', 'mapel', 'jabatan', 'nip', 'no_telp', 'urutan']);
        $data['tampilkan'] = $request->has('tampilkan') ? 1 : 0;

        if ($request->hasFile('foto')) {
            $data['foto'] = \App\Helpers\ImageHelper::uploadAndOptimize($request->file('foto'), 'uploads/guru', 'guru');
            \App\Helpers\ImageHelper::generateThumbnailFor($data['foto']);
        }

        if ($request->id) {
            $guru = Guru::findOrFail($request->id);
            if (isset($data['foto']) && $guru->foto && file_exists(public_path($guru->foto))) {
                @unlink(public_path($guru->foto));
                \App\Helpers\ImageHelper::deleteThumbnail($guru->foto);
            }
            $guru->update($data);
            \App\Helpers\ActivityLogger::log('update_guru', $guru, "Memperbarui data guru {$guru->nama}");
        } else {
            $guru = Guru::create($data);
            \App\Helpers\ActivityLogger::log('create_guru', $guru, "Menambahkan guru baru {$guru->nama}");
        }

        return redirect()->route('admin.guru')->with('success', 'Data guru berhasil disimpan');
    }

    public function destroy($id)
    {
        $guru = Guru::findOrFail($id);
        $namaGuru = $guru->nama;

        if ($guru->jadwal()->count() > 0) {
            return redirect()->route('admin.guru')->with('error', 'Guru masih memiliki jadwal, tidak bisa dihapus!');
        }
        if ($guru->foto && file_exists(public_path($guru->foto))) {
            @unlink(public_path($guru->foto));
            \App\Helpers\ImageHelper::deleteThumbnail($guru->foto);
        }

        \App\Helpers\ActivityLogger::log('delete_guru', null, "Menghapus data guru {$namaGuru}");
        
        $guru->delete();
        return redirect()->route('admin.guru')->with('success', 'Guru berhasil dihapus');
    }
}
