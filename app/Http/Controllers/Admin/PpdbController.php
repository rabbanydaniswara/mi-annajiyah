<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\DocumentHelper;
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
        if ($request->tahun_ajaran) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }
        if ($q = trim((string) $request->q)) {
            $query->where(function ($w) use ($q) {
                $w->where('nama', 'like', "%$q%")
                  ->orWhere('nomor_pendaftaran', 'like', "%$q%")
                  ->orWhere('nisn', 'like', "%$q%")
                  ->orWhere('nis', 'like', "%$q%")
                  ->orWhere('no_wa', 'like', "%$q%")
                  ->orWhere('nama_ortu', 'like', "%$q%");
            });
        }

        $pendaftar = $query->orderByDesc('tanggal_daftar')->paginate(20)->withQueryString();
        $statsQuery = Siswa::query();
        if ($request->tahun_ajaran) {
            $statsQuery->where('tahun_ajaran', $request->tahun_ajaran);
        }

        $totalPending = (clone $statsQuery)->where('status_ppdb', 'pending')->count();
        $totalDiterima = (clone $statsQuery)->where('status_ppdb', 'diterima')->count();
        $totalDitolak = (clone $statsQuery)->where('status_ppdb', 'ditolak')->count();
        $totalSemua = (clone $statsQuery)->count();
        $tahunAjaranList = Siswa::whereNotNull('tahun_ajaran')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran');

        return view('admin.ppdb', compact('pendaftar', 'totalPending', 'totalDiterima', 'totalDitolak', 'totalSemua', 'tahunAjaranList'));
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

    public function document(Siswa $siswa, string $field)
    {
        abort_unless(in_array($field, ['file_akte', 'file_kk', 'file_ktp_ortu', 'file_ijazah'], true), 404);

        $path = $siswa->{$field};
        $absolutePath = DocumentHelper::absolutePath($path);

        abort_unless($absolutePath && is_file($absolutePath), 404);

        return response()->file($absolutePath, [
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $namaSiswa = $siswa->nama;

        // Hapus file
        foreach (['file_akte', 'file_kk', 'file_ktp_ortu', 'file_ijazah'] as $field) {
            DocumentHelper::delete($siswa->$field);
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
