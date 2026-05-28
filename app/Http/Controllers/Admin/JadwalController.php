<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\{Jadwal, Guru};
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = Jadwal::with('guru');
        if ($request->hari) $query->where('hari', $request->hari);
        if ($request->kelas) $query->where('kelas', $request->kelas);
        if ($q = trim((string) $request->q)) {
            $query->where(function ($w) use ($q) {
                $w->where('mapel', 'like', "%$q%")
                  ->orWhere('ruangan', 'like', "%$q%")
                  ->orWhereHas('guru', fn($g) => $g->where('nama', 'like', "%$q%"));
            });
        }

        $jadwal = $query->orderByRaw(self::HARI_ORDER_SQL)
            ->orderBy('jam_mulai')
            ->get();
        $guru = Guru::orderBy('nama')->get();
        $edit = $request->edit ? Jadwal::find($request->edit) : null;
        $totalJadwal = Jadwal::count();
        $totalGuru = Guru::count();
        $totalKelas = Jadwal::distinct('kelas')->count('kelas');
        $kelasList = Jadwal::whereNotNull('kelas')->where('kelas', '!=', '')->distinct()->orderBy('kelas')->pluck('kelas');

        return view('admin.jadwal', compact('jadwal', 'guru', 'edit', 'totalJadwal', 'totalGuru', 'totalKelas', 'kelasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'mapel' => 'required|string|max:100',
            'id_guru' => 'required|exists:guru,id',
            'kelas' => 'required|string|max:10',
        ], [
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
        ]);

        $data = $request->only(['hari', 'jam_mulai', 'jam_selesai', 'mapel', 'id_guru', 'kelas', 'ruangan']);

        // Conflict Detection
        $conflict = Jadwal::where('hari', $request->hari)
            ->where(function($query) use ($request) {
                $query->whereBetween('jam_mulai', [$request->jam_mulai, $request->jam_selesai])
                      ->orWhereBetween('jam_selesai', [$request->jam_mulai, $request->jam_selesai])
                      ->orWhere(function($q) use ($request) {
                          $q->where('jam_mulai', '<=', $request->jam_mulai)
                            ->where('jam_selesai', '>=', $request->jam_selesai);
                      });
            })
            ->where(function($query) use ($request) {
                $query->where('id_guru', $request->id_guru)
                      ->orWhere('kelas', $request->kelas)
                      ->orWhere('ruangan', $request->ruangan);
            })
            ->when($request->id, function($query) use ($request) {
                $query->where('id', '!=', $request->id);
            })
            ->first();

        if ($conflict) {
            $msg = "Jadwal bentrok dengan ";
            if ($conflict->id_guru == $request->id_guru) $msg .= "Guru " . $conflict->guru->nama;
            elseif ($conflict->kelas == $request->kelas) $msg .= "Kelas " . $conflict->kelas;
            else $msg .= "Ruangan " . $conflict->ruangan;
            
            return back()->withInput()->with('error', $msg . " pada jam tersebut (" . substr($conflict->jam_mulai,0,5) . "-" . substr($conflict->jam_selesai,0,5) . ")");
        }

        if ($request->id) {
            $jadwal = Jadwal::findOrFail($request->id);
            $jadwal->update($data);
            \App\Helpers\ActivityLogger::log('update_jadwal', $jadwal, "Memperbarui jadwal {$jadwal->mapel} kelas {$jadwal->kelas}");
        } else {
            $jadwal = Jadwal::create($data);
            \App\Helpers\ActivityLogger::log('create_jadwal', $jadwal, "Menambahkan jadwal baru {$jadwal->mapel} kelas {$jadwal->kelas}");
        }

        return redirect()->route('admin.jadwal')->with('success', 'Jadwal berhasil disimpan');
    }

    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $mapel = $jadwal->mapel;
        $kelas = $jadwal->kelas;
        $jadwal->delete();
        \App\Helpers\ActivityLogger::log('delete_jadwal', null, "Menghapus jadwal {$mapel} kelas {$kelas}");
        return redirect()->route('admin.jadwal')->with('success', 'Jadwal berhasil dihapus');
    }

    public function print()
    {
        $jadwal = Jadwal::with('guru')
            ->orderByRaw(self::HARI_ORDER_SQL)
            ->orderBy('jam_mulai')
            ->get();
        return view('admin.jadwal-print', compact('jadwal'));
    }

    private const HARI_ORDER_SQL = "CASE hari WHEN 'Senin' THEN 1 WHEN 'Selasa' THEN 2 WHEN 'Rabu' THEN 3 WHEN 'Kamis' THEN 4 WHEN 'Jumat' THEN 5 WHEN 'Sabtu' THEN 6 ELSE 7 END";
}
