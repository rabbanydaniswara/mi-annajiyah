<?php

namespace App\Helpers;

use App\Models\KontenWeb;
use App\Models\Siswa;

class PpdbHelper
{
    public static function statusOptions(): array
    {
        return [
            'pending' => 'Pending',
            'berkas_kurang' => 'Berkas Kurang',
            'diverifikasi' => 'Diverifikasi',
            'diterima' => 'Diterima',
            'ditolak' => 'Ditolak',
            'daftar_ulang' => 'Daftar Ulang',
        ];
    }

    public static function statusLabel(?string $status): string
    {
        return self::statusOptions()[$status] ?? 'Pending';
    }

    public static function publicStatusLabel(?string $status): string
    {
        return match ($status) {
            'diterima', 'daftar_ulang' => 'Lolos Seleksi',
            'ditolak' => 'Tidak Lolos',
            'berkas_kurang' => 'Berkas Perlu Dilengkapi',
            'diverifikasi' => 'Sedang Diverifikasi',
            default => 'Menunggu Verifikasi',
        };
    }

    public static function statusTone(?string $status): string
    {
        return match ($status) {
            'diterima', 'daftar_ulang' => 'green',
            'ditolak' => 'red',
            'berkas_kurang' => 'orange',
            'diverifikasi' => 'blue',
            default => 'yellow',
        };
    }

    public static function activeAcademicYear(): string
    {
        $value = KontenWeb::where('tipe', 'ppdb_tahun_ajaran')->value('konten');

        return self::normalizeAcademicYear($value ?: self::defaultAcademicYear());
    }

    public static function defaultAcademicYear(): string
    {
        $year = (int) now()->format('Y');

        return $year . '/' . ($year + 1);
    }

    public static function normalizeAcademicYear(string $value): string
    {
        $value = trim($value);

        if (preg_match('/^(\d{4})\s*[\/-]\s*(\d{4})$/', $value, $matches)) {
            return $matches[1] . '/' . $matches[2];
        }

        return self::defaultAcademicYear();
    }

    public static function generateRegistrationNumber(string $academicYear): string
    {
        $startYear = substr(self::normalizeAcademicYear($academicYear), 0, 4);
        $sequence = Siswa::where('tahun_ajaran', self::normalizeAcademicYear($academicYear))->count() + 1;

        do {
            $number = sprintf('PPDB-%s-%04d', $startYear, $sequence);
            $sequence++;
        } while (Siswa::where('nomor_pendaftaran', $number)->exists());

        return $number;
    }
}
