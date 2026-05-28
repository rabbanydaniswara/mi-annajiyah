<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\PpdbHelper;
use App\Http\Controllers\Controller;
use App\Models\{Siswa, Guru};

class ExportController extends Controller
{
    public function export($type)
    {
        $format = request('format', 'xls');

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

            $data = $query->orderBy('kelas')->orderBy('nama')->get();
            $title = $type === 'ppdb' ? "Data PPDB MI Annajiyah" : "Data Siswa MI Annajiyah";
            $view = 'admin.print-siswa';
            $filename = $type . "_export_" . date('Ymd_His');
            $headers = ['No Pendaftaran', 'Tahun Ajaran', 'Nama Lengkap', 'NISN', 'Kelas', 'No WhatsApp', 'Alamat', 'Nama Orang Tua', 'Status PPDB'];
            $rows = $data->map(fn($s) => [$s->nomor_pendaftaran, $s->tahun_ajaran, $s->nama, $s->nisn, $s->kelas, $s->no_wa, $s->alamat, $s->nama_ortu, $s->status_ppdb]);
        } elseif ($type === 'guru') {
            $data = Guru::orderBy('nama')->get();
            $title = "Data Guru MI Annajiyah";
            $view = 'admin.print-guru';
            $filename = "guru_export_" . date('Ymd_His');
            $headers = ['ID', 'Nama Guru', 'Jabatan', 'Mata Pelajaran', 'NIP', 'No Telepon'];
            $rows = $data->map(fn($g) => [$g->id, $g->nama, $g->jabatan, $g->mapel, $g->nip, $g->no_telp]);
        } else {
            abort(404, 'Tipe export tidak valid');
        }

        if ($format === 'pdf') {
            return view($view, compact('data', 'title'));
        }

        return response()->streamDownload(function () use ($title, $headers, $rows) {
            echo "<table border='1'>";
            echo "<tr><th colspan='" . count($headers) . "' style='font-size:16px; text-align:center;'>{$title}</th></tr>";
            echo "<tr><th colspan='" . count($headers) . "' style='text-align:center;'>Dicetak: " . date('d-m-Y H:i:s') . "</th></tr>";
            echo "<tr><td colspan='" . count($headers) . "'></td></tr>";
            echo "<tr>";
            foreach ($headers as $header) {
                echo "<th style='background:#0b3b1e; color:white;'>{$header}</th>";
            }
            echo "</tr>";
            foreach ($rows as $row) {
                echo "<tr>";
                foreach ($row as $cell) {
                    echo "<td>" . e($cell ?? '-') . "</td>";
                }
                echo "</tr>";
            }
            echo "</table>";
        }, "{$filename}.xls", [
            'Content-Type' => 'application/vnd.ms-excel',
        ]);
    }
}
