<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ActivityLogger;
use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Jadwal;
use Illuminate\Http\Request;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        $query = Jadwal::with('guru');
        if ($request->hari) {
            $query->where('hari', $request->hari);
        }
        if ($request->kelas) {
            $query->where('kelas', $request->kelas);
        }
        if ($q = trim((string) $request->q)) {
            $query->where(function ($w) use ($q) {
                $w->where('mapel', 'like', "%$q%")
                    ->orWhere('ruangan', 'like', "%$q%")
                    ->orWhereHas('guru', fn ($g) => $g->where('nama', 'like', "%$q%"));
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
            'id' => 'nullable|integer|exists:jadwal,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai' => 'required|date_format:H:i',
            'jam_selesai' => 'required|date_format:H:i|after:jam_mulai',
            'mapel' => 'required|string|max:100',
            'id_guru' => 'required|exists:guru,id',
            'kelas' => 'required|string|max:10',
            'ruangan' => 'nullable|string|max:100',
        ], [
            'jam_selesai.after' => 'Jam selesai harus lebih besar dari jam mulai.',
        ]);

        $data = $request->only(['hari', 'jam_mulai', 'jam_selesai', 'mapel', 'id_guru', 'kelas', 'ruangan']);

        $conflict = Jadwal::where('hari', $request->hari)
            ->where('jam_mulai', '<', $request->jam_selesai)
            ->where('jam_selesai', '>', $request->jam_mulai)
            ->where(function ($query) use ($request) {
                $query->where('id_guru', $request->id_guru)
                    ->orWhere('kelas', $request->kelas);

                if ($request->filled('ruangan')) {
                    $query->orWhere('ruangan', $request->ruangan);
                }
            })
            ->when($request->id, function ($query) use ($request) {
                $query->where('id', '!=', $request->id);
            })
            ->first();

        if ($conflict) {
            $resource = match (true) {
                (int) $conflict->id_guru === (int) $request->id_guru => 'Guru '.$conflict->guru->nama,
                $conflict->kelas === $request->kelas => 'Kelas '.$conflict->kelas,
                default => 'Ruangan '.$conflict->ruangan,
            };

            return back()->withInput()->with(
                'error',
                "Jadwal bentrok dengan {$resource} pada jam tersebut (".
                substr($conflict->jam_mulai, 0, 5).'-'.substr($conflict->jam_selesai, 0, 5).')'
            );
        }

        if ($request->id) {
            $jadwal = Jadwal::findOrFail($request->id);
            $jadwal->update($data);
            ActivityLogger::log('update_jadwal', $jadwal, "Memperbarui jadwal {$jadwal->mapel} kelas {$jadwal->kelas}");
        } else {
            $jadwal = Jadwal::create($data);
            ActivityLogger::log('create_jadwal', $jadwal, "Menambahkan jadwal baru {$jadwal->mapel} kelas {$jadwal->kelas}");
        }

        return redirect()->route('admin.jadwal')->with('success', 'Jadwal berhasil disimpan');
    }

    public function destroy($id)
    {
        $jadwal = Jadwal::findOrFail($id);
        $mapel = $jadwal->mapel;
        $kelas = $jadwal->kelas;
        $jadwal->delete();
        ActivityLogger::log('delete_jadwal', null, "Menghapus jadwal {$mapel} kelas {$kelas}");

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
