<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PpdbController extends Controller
{
    public function index(Request $request)
    {
        $query = Siswa::query();
        if ($request->status && in_array($request->status, ['pending', 'diterima', 'ditolak'])) {
            $query->where('status_ppdb', $request->status);
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

        $pendaftar = $query->orderByDesc('tanggal_daftar')->paginate(20)->withQueryString();
        $totalPending = Siswa::where('status_ppdb', 'pending')->count();
        $totalDiterima = Siswa::where('status_ppdb', 'diterima')->count();
        $totalDitolak = Siswa::where('status_ppdb', 'ditolak')->count();
        $totalSemua = Siswa::count();

        return view('admin.ppdb', compact('pendaftar', 'totalPending', 'totalDiterima', 'totalDitolak', 'totalSemua'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:siswa,id',
            'status' => 'required|in:pending,diterima,ditolak',
        ]);

        $siswa = Siswa::findOrFail($request->id);
        $oldStatus = $siswa->status_ppdb;
        $siswa->update([
            'status_ppdb' => $request->status,
            'tgl_verifikasi' => now(),
        ]);

        \App\Helpers\ActivityLogger::log(
            'update_status', 
            $siswa, 
            "Mengubah status pendaftaran {$siswa->nama} dari {$oldStatus} menjadi {$request->status}",
            ['old' => $oldStatus, 'new' => $request->status]
        );

        return redirect()->route('admin.ppdb')->with('success', 'Status berhasil diperbarui');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $namaSiswa = $siswa->nama;

        // Hapus file
        foreach (['file_akte', 'file_kk', 'file_ktp_ortu', 'file_ijazah'] as $field) {
            if ($siswa->$field && file_exists(public_path($siswa->$field))) {
                unlink(public_path($siswa->$field));
                \App\Helpers\ImageHelper::deleteThumbnail($siswa->$field);
            }
        }

        \App\Helpers\ActivityLogger::log(
            'delete_ppdb', 
            null, 
            "Menghapus data pendaftaran {$namaSiswa}",
            ['nama' => $namaSiswa, 'data' => $siswa->toArray()]
        );

        $siswa->delete();
        return redirect()->route('admin.ppdb')->with('success', 'Data pendaftar berhasil dihapus');
    }
}
