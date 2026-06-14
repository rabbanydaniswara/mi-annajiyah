<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Helpers\DocumentHelper;
use App\Helpers\PpdbHelper;
use App\Helpers\PublicCacheHelper;
use App\Http\Controllers\Controller;
use App\Models\Siswa;
use Illuminate\Http\Request;

class PpdbController extends Controller
{
    private const DOCUMENT_FIELDS = ['file_akte', 'file_kk', 'file_ktp_ortu', 'file_ijazah'];

    public function index(Request $request)
    {
        $query = Siswa::query();
        if ($request->status && array_key_exists($request->status, PpdbHelper::statusOptions())) {
            $query->where('status_ppdb', $request->status);
        }
        if ($request->tahun_ajaran) {
            $query->where('tahun_ajaran', $request->tahun_ajaran);
        }
        if ($request->kelas) {
            $query->where('kelas', $request->kelas);
        }
        if ($request->tanggal_dari) {
            $query->whereDate('tanggal_daftar', '>=', $request->tanggal_dari);
        }
        if ($request->tanggal_sampai) {
            $query->whereDate('tanggal_daftar', '<=', $request->tanggal_sampai);
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
        if ($request->kelas) {
            $statsQuery->where('kelas', $request->kelas);
        }

        $totalPending = (clone $statsQuery)->where('status_ppdb', 'pending')->count();
        $totalDiterima = (clone $statsQuery)->where('status_ppdb', 'diterima')->count();
        $totalDitolak = (clone $statsQuery)->where('status_ppdb', 'ditolak')->count();
        $totalSemua = (clone $statsQuery)->count();
        $tahunAjaranList = Siswa::whereNotNull('tahun_ajaran')
            ->distinct()
            ->orderByDesc('tahun_ajaran')
            ->pluck('tahun_ajaran');
        $kelasList = Siswa::whereNotNull('kelas')
            ->where('kelas', '!=', '')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');
        $statusOptions = PpdbHelper::statusOptions();

        return view('admin.ppdb', compact('pendaftar', 'totalPending', 'totalDiterima', 'totalDitolak', 'totalSemua', 'tahunAjaranList', 'kelasList', 'statusOptions'));
    }

    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:siswa,id',
            'status' => 'required|in:'.implode(',', array_keys(PpdbHelper::statusOptions())),
            'catatan_verifikasi' => 'nullable|string|max:2000',
        ]);

        $siswa = Siswa::findOrFail($request->id);
        $oldStatus = $siswa->status_ppdb;
        $siswa->update([
            'status_ppdb' => $request->status,
            'tgl_verifikasi' => now(),
            'catatan_verifikasi' => $request->has('catatan_verifikasi')
                ? $request->catatan_verifikasi
                : $siswa->catatan_verifikasi,
        ]);

        ActivityLogger::log(
            'update_status',
            $siswa,
            "Mengubah status pendaftaran {$siswa->nama} dari {$oldStatus} menjadi {$request->status}",
            ['old' => $oldStatus, 'new' => $request->status]
        );

        PublicCacheHelper::clearStats();

        return redirect()->route('admin.ppdb')->with('success', 'Status berhasil diperbarui');
    }

    public function bulkUpdateStatus(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer|exists:siswa,id',
            'status' => 'required|in:'.implode(',', array_keys(PpdbHelper::statusOptions())),
            'catatan_verifikasi' => 'nullable|string|max:2000',
        ]);

        $siswas = Siswa::whereIn('id', $validated['ids'])->get();

        foreach ($siswas as $siswa) {
            $oldStatus = $siswa->status_ppdb;
            $siswa->update([
                'status_ppdb' => $validated['status'],
                'tgl_verifikasi' => now(),
                'catatan_verifikasi' => $validated['catatan_verifikasi'] ?? $siswa->catatan_verifikasi,
            ]);

            ActivityLogger::log(
                'bulk_update_status',
                $siswa,
                "Mengubah status pendaftaran {$siswa->nama} dari {$oldStatus} menjadi {$validated['status']}",
                ['old' => $oldStatus, 'new' => $validated['status']]
            );
        }

        PublicCacheHelper::clearStats();

        return redirect()->route('admin.ppdb')->with('success', $siswas->count().' data pendaftar berhasil diperbarui');
    }

    public function document(Siswa $siswa, string $field)
    {
        abort_unless(in_array($field, self::DOCUMENT_FIELDS, true), 404);

        $path = $siswa->{$field};
        $absolutePath = DocumentHelper::absolutePath($path);

        abort_unless($absolutePath && is_file($absolutePath), 404);

        return response()->file($absolutePath, [
            'Cache-Control' => 'private, no-store, no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function documentThumbnail(Siswa $siswa, string $field)
    {
        abort_unless(in_array($field, self::DOCUMENT_FIELDS, true), 404);

        $path = $siswa->{$field};
        abort_unless(DocumentHelper::isImage($path), 404);

        $absolutePath = DocumentHelper::thumbnailAbsolutePath($path)
            ?: DocumentHelper::absolutePath($path);

        abort_unless($absolutePath && is_file($absolutePath), 404);

        return response()->file($absolutePath, [
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);
        $namaSiswa = $siswa->nama;
        $documentPaths = collect(self::DOCUMENT_FIELDS)
            ->map(fn (string $field) => $siswa->{$field})
            ->filter()
            ->all();
        $auditIdentity = PpdbHelper::auditIdentity($siswa);

        $siswa->delete();

        ActivityLogger::log(
            'delete_ppdb',
            null,
            "Menghapus data pendaftaran {$namaSiswa}",
            $auditIdentity
        );

        foreach ($documentPaths as $path) {
            DocumentHelper::delete($path);
        }

        PublicCacheHelper::clearStats();

        return redirect()->route('admin.ppdb')->with('success', 'Data pendaftar berhasil dihapus');
    }
}
