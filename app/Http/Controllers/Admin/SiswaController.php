<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::query();
        if ($request->tahun) {
            $query->whereYear('tanggal_daftar', $request->tahun);
        }
        if ($request->kelas) {
            $query->where('kelas', $request->kelas);
        }
        if ($q = trim((string) $request->q)) {
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%$q%")
                  ->orWhere('nisn', 'like', "%$q%")
                  ->orWhere('nis', 'like', "%$q%")
                  ->orWhere('no_wa', 'like', "%$q%")
                  ->orWhere('nama_ortu', 'like', "%$q%");
            });
        }

        $siswa = $query->orderBy('kelas')->orderBy('nama')->paginate(20)->withQueryString();

        // Stats use unfiltered totals so user always sees overall numbers
        $totalSiswa = Siswa::count();
        $totalPending = Siswa::where('status_ppdb', 'pending')->count();
        
        $totalKelas = Siswa::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->count('kelas');
            
        $totalTahun = Siswa::whereNotNull('tanggal_daftar')
            ->selectRaw('DISTINCT substr(tanggal_daftar, 1, 4)')
            ->count();

        // For the UI grouping, we still need the list but let's select only what we need
        $allSiswa = Siswa::select('kelas', 'tanggal_daftar')->get();
        $groupByKelas = $allSiswa->groupBy(fn($s) => $s->kelas ?? 'Tanpa Kelas');
        $groupByTahun = $allSiswa->groupBy(fn($s) => $s->tanggal_daftar?->format('Y') ?? '-');

        $edit = $request->edit ? Siswa::find($request->edit) : null;

        // substr() portable across MySQL & SQLite (extracts 'YYYY' from datetime string)
        $tahunList = Siswa::selectRaw('DISTINCT substr(tanggal_daftar, 1, 4) as tahun')
            ->whereNotNull('tanggal_daftar')
            ->orderByDesc('tahun')
            ->pluck('tahun');
        $kelasList = Siswa::whereNotNull('kelas')->where('kelas', '!=', '')->distinct()->orderBy('kelas')->pluck('kelas');

        return view('admin.siswa', compact(
            'siswa', 'groupByKelas', 'groupByTahun', 'edit',
            'totalSiswa', 'totalKelas', 'totalTahun', 'totalPending',
            'tahunList', 'kelasList'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'         => 'required|string|max:100',
            'nisn'         => 'nullable|string|max:20',
            'nis'          => 'nullable|string|max:20',
            'kelas'        => 'nullable|string|max:50',
            'no_wa'        => 'nullable|string|max:20',
            'nama_ortu'    => 'nullable|string|max:100',
            'alamat'       => 'nullable|string',
        ]);

        $data = $request->only([
            'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
            'nisn', 'nis', 'kelas', 'no_wa', 'alamat', 'asal_sekolah', 'nama_ortu'
        ]);

        if ($request->id) {
            $siswa = Siswa::findOrFail($request->id);
            $siswa->update($data);
            \App\Helpers\ActivityLogger::log('update_siswa', $siswa, "Memperbarui data siswa {$siswa->nama}");
        } else {
            $siswa = Siswa::create($data);
            \App\Helpers\ActivityLogger::log('create_siswa', $siswa, "Menambahkan siswa baru {$siswa->nama}");
        }

        return redirect()->route('admin.siswa')->with('success', 'Data siswa berhasil disimpan');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $namaSiswa = $siswa->nama;

        // Hapus file dokumen jika ada
        foreach (['file_akte', 'file_kk', 'file_ktp_ortu', 'file_ijazah'] as $field) {
            if ($siswa->$field && file_exists(public_path($siswa->$field))) {
                @unlink(public_path($siswa->$field));
                \App\Helpers\ImageHelper::deleteThumbnail($siswa->$field);
            }
        }

        \App\Helpers\ActivityLogger::log('delete_siswa', null, "Menghapus data siswa {$namaSiswa}", ['data' => $siswa->toArray()]);
        
        $siswa->delete();
        return redirect()->route('admin.siswa')->with('success', 'Siswa berhasil dihapus');
    }
}
