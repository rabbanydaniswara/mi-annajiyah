<?php

namespace App\Http\Controllers\Admin;

use App\Exports\AdminDataExport;
use App\Helpers\PpdbHelper;
use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Excel as ExcelWriter;
use Maatwebsite\Excel\Facades\Excel as ExcelFacade;

class ExportController extends Controller
{
    public function export($type)
    {
        $format = request('format', 'xlsx');

        if ($type === 'ppdb' || $type === 'siswa') {
            $query = Siswa::query();
            if ($type === 'ppdb' && request('tahun_ajaran')) {
                $query->where('tahun_ajaran', request('tahun_ajaran'));
            }
            if ($type === 'ppdb' && request('kelas')) {
                $query->where('kelas', request('kelas'));
            }
            if ($type === 'ppdb' && request('status') && array_key_exists(request('status'), PpdbHelper::statusOptions())) {
                $query->where('status_ppdb', request('status'));
            }
            if ($type === 'ppdb' && request('tanggal_dari')) {
                $query->whereDate('tanggal_daftar', '>=', request('tanggal_dari'));
            }
            if ($type === 'ppdb' && request('tanggal_sampai')) {
                $query->whereDate('tanggal_daftar', '<=', request('tanggal_sampai'));
            }
            if ($type === 'ppdb' && ($q = trim((string) request('q')))) {
                $query->where(function ($w) use ($q) {
                    $w->where('nama', 'like', "%$q%")
                        ->orWhere('nomor_pendaftaran', 'like', "%$q%")
                        ->orWhere('nisn', 'like', "%$q%")
                        ->orWhere('nis', 'like', "%$q%")
                        ->orWhere('no_wa', 'like', "%$q%")
                        ->orWhere('nama_ortu', 'like', "%$q%");
                });
            }

            $data = $query->orderBy('kelas')->orderBy('nama')->get();
            $title = $type === 'ppdb' ? 'Data PPDB MI Annajiyah' : 'Data Siswa MI Annajiyah';
            $view = 'admin.print-siswa';
            $filename = $type.'_export_'.date('Ymd_His');
            $headers = $type === 'ppdb'
                ? ['No', 'No Pendaftaran', 'Tahun Ajaran', 'Tanggal Daftar', 'Nama Lengkap', 'NISN', 'NIS', 'Kelas Tujuan', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Asal Sekolah', 'No WhatsApp', 'Alamat', 'Nama Orang Tua', 'Status PPDB', 'Tanggal Verifikasi', 'Catatan Verifikasi']
                : ['No', 'No Pendaftaran', 'Tahun Ajaran', 'Nama Lengkap', 'NISN', 'NIS', 'Kelas', 'No WhatsApp', 'Alamat', 'Nama Orang Tua', 'Status PPDB'];
            $rows = $data->values()->map(function ($s, $index) use ($type) {
                if ($type === 'ppdb') {
                    return [
                        $index + 1,
                        $s->nomor_pendaftaran,
                        $s->tahun_ajaran,
                        $s->tanggal_daftar?->format('d/m/Y H:i') ?? '-',
                        $s->nama,
                        $s->nisn,
                        $s->nis,
                        $s->kelas,
                        $s->jenis_kelamin,
                        $s->tempat_lahir,
                        $s->tanggal_lahir ? Carbon::parse($s->tanggal_lahir)->format('d/m/Y') : '-',
                        $s->asal_sekolah,
                        $s->no_wa,
                        $s->alamat,
                        $s->nama_ortu,
                        PpdbHelper::statusLabel($s->status_ppdb),
                        $s->tgl_verifikasi ? Carbon::parse($s->tgl_verifikasi)->format('d/m/Y H:i') : '-',
                        $s->catatan_verifikasi,
                    ];
                }

                return [
                    $index + 1,
                    $s->nomor_pendaftaran,
                    $s->tahun_ajaran,
                    $s->nama,
                    $s->nisn,
                    $s->nis,
                    $s->kelas,
                    $s->no_wa,
                    $s->alamat,
                    $s->nama_ortu,
                    PpdbHelper::statusLabel($s->status_ppdb),
                ];
            });
            $columnWidths = $type === 'ppdb'
                ? [6, 18, 14, 18, 28, 18, 16, 14, 16, 18, 16, 24, 18, 36, 24, 18, 20, 36]
                : [6, 18, 14, 28, 18, 16, 12, 18, 36, 24, 18];
        } elseif ($type === 'guru') {
            $data = Guru::orderBy('nama')->get();
            $title = 'Data Guru MI Annajiyah';
            $view = 'admin.print-guru';
            $filename = 'guru_export_'.date('Ymd_His');
            $headers = ['No', 'Nama Guru', 'Jabatan', 'Mata Pelajaran', 'NIP', 'No Telepon'];
            $rows = $data->values()->map(fn ($g, $index) => [$index + 1, $g->nama, $g->jabatan, $g->mapel, $g->nip, $g->no_telp]);
            $columnWidths = [6, 28, 24, 28, 20, 18];
        } else {
            abort(404, 'Tipe export tidak valid');
        }

        if ($format === 'pdf') {
            return view($view, compact('data', 'title'));
        }

        return ExcelFacade::download(
            new AdminDataExport(
                $title,
                $this->exportMetadata($type, $data->count()),
                $headers,
                $rows->all(),
                $columnWidths,
                $type === 'ppdb' ? 'Data PPDB' : ($type === 'guru' ? 'Data Guru' : 'Data Siswa')
            ),
            "{$filename}.xlsx",
            ExcelWriter::XLSX
        );
    }

    private function exportMetadata(string $type, int $total): array
    {
        $metadata = [
            'Dicetak: '.now()->format('d/m/Y H:i:s'),
            'Total data: '.$total,
        ];

        if ($type !== 'ppdb') {
            return $metadata;
        }

        $filters = array_filter([
            request('tahun_ajaran') ? 'Tahun ajaran '.request('tahun_ajaran') : null,
            request('kelas') ? 'Kelas '.request('kelas') : null,
            request('status') ? 'Status '.(PpdbHelper::statusOptions()[request('status')] ?? request('status')) : null,
            request('tanggal_dari') ? 'Dari '.request('tanggal_dari') : null,
            request('tanggal_sampai') ? 'Sampai '.request('tanggal_sampai') : null,
            request('q') ? 'Pencarian "'.request('q').'"' : null,
        ]);

        $metadata[] = 'Filter: '.($filters ? implode(', ', $filters) : 'Semua data');

        return $metadata;
    }
}
