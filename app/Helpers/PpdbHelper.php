<?php

namespace App\Helpers;

use App\Models\KontenWeb;
use App\Models\Siswa;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PpdbHelper
{
    public const DEFAULT_CLOSED_MESSAGE = 'Pendaftaran PPDB saat ini telah ditutup. Silakan hubungi panitia untuk informasi periode pendaftaran berikutnya.';

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
        return self::settings()['academic_year'];
    }

    public static function settings(): array
    {
        $values = KontenWeb::whereIn('tipe', [
            'ppdb_tahun_ajaran',
            'ppdb_status',
            'ppdb_pesan_tutup',
        ])->pluck('konten', 'tipe');

        return self::settingsFrom($values);
    }

    public static function settingsFrom(iterable $values): array
    {
        $values = collect($values);
        $closedMessage = trim((string) $values->get('ppdb_pesan_tutup'));

        return [
            'academic_year' => self::normalizeAcademicYear(
                (string) ($values->get('ppdb_tahun_ajaran') ?: self::defaultAcademicYear())
            ),
            'is_open' => $values->get('ppdb_status', 'open') !== 'closed',
            'closed_message' => $closedMessage !== '' ? $closedMessage : self::DEFAULT_CLOSED_MESSAGE,
        ];
    }

    public static function isOpen(): bool
    {
        return self::settings()['is_open'];
    }

    public static function closedMessage(): string
    {
        return self::settings()['closed_message'];
    }

    public static function defaultAcademicYear(): string
    {
        $year = (int) now()->format('Y');

        return $year.'/'.($year + 1);
    }

    public static function normalizeAcademicYear(string $value): string
    {
        $value = trim($value);

        if (preg_match('/^(\d{4})\s*[\/-]\s*(\d{4})$/', $value, $matches)) {
            return $matches[1].'/'.$matches[2];
        }

        return self::defaultAcademicYear();
    }

    public static function generateRegistrationNumber(string $academicYear): string
    {
        $academicYear = self::normalizeAcademicYear($academicYear);
        $startYear = substr($academicYear, 0, 4);
        $prefix = "PPDB-{$startYear}-";
        $latestNumber = Siswa::where('tahun_ajaran', $academicYear)
            ->where('nomor_pendaftaran', 'like', $prefix.'%')
            ->orderByDesc('nomor_pendaftaran')
            ->value('nomor_pendaftaran');
        $sequence = $latestNumber
            ? ((int) substr($latestNumber, strlen($prefix))) + 1
            : 1;

        do {
            $number = sprintf('PPDB-%s-%04d', $startYear, $sequence);
            $sequence++;
        } while (Siswa::where('nomor_pendaftaran', $number)->exists());

        return $number;
    }

    public static function createSiswa(array $attributes): Siswa
    {
        $academicYear = self::normalizeAcademicYear(
            (string) ($attributes['tahun_ajaran'] ?? self::activeAcademicYear())
        );
        $attributes['tahun_ajaran'] = $academicYear;
        $lockKey = 'ppdb-registration-number:'.str_replace('/', '-', $academicYear);

        return Cache::lock($lockKey, 15)->block(10, function () use ($attributes) {
            return DB::transaction(fn () => Siswa::create($attributes));
        });
    }

    public static function auditIdentity(Siswa $siswa): array
    {
        return [
            'id' => $siswa->id,
            'nomor_pendaftaran' => $siswa->nomor_pendaftaran,
            'status_ppdb' => $siswa->status_ppdb,
            'tahun_ajaran' => $siswa->tahun_ajaran,
        ];
    }
}
