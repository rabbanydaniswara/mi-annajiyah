<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->string('nomor_pendaftaran', 32)->nullable()->unique()->after('registration_token');
            $table->string('tahun_ajaran', 20)->nullable()->index()->after('nomor_pendaftaran');
        });

        $counters = [];

        DB::table('siswa')
            ->select('id', 'tanggal_daftar')
            ->orderBy('id')
            ->chunkById(100, function ($rows) use (&$counters) {
                foreach ($rows as $row) {
                    $tahunAjaran = $this->academicYearFromDate($row->tanggal_daftar);
                    $startYear = substr($tahunAjaran, 0, 4);
                    $counters[$tahunAjaran] = ($counters[$tahunAjaran] ?? 0) + 1;

                    DB::table('siswa')->where('id', $row->id)->update([
                        'tahun_ajaran' => $tahunAjaran,
                        'nomor_pendaftaran' => sprintf('PPDB-%s-%04d', $startYear, $counters[$tahunAjaran]),
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropUnique(['nomor_pendaftaran']);
            $table->dropIndex(['tahun_ajaran']);
            $table->dropColumn(['nomor_pendaftaran', 'tahun_ajaran']);
        });
    }

    private function academicYearFromDate(?string $date): string
    {
        $year = $date ? (int) substr($date, 0, 4) : (int) now()->format('Y');

        return $year.'/'.($year + 1);
    }
};
