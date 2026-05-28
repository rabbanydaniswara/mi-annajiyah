<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE siswa MODIFY status_ppdb ENUM('pending','berkas_kurang','diverifikasi','diterima','ditolak','daftar_ulang') NOT NULL DEFAULT 'pending'");
        }

        Schema::table('siswa', function (Blueprint $table) {
            $table->text('catatan_verifikasi')->nullable()->after('tgl_verifikasi');
        });
    }

    public function down(): void
    {
        DB::table('siswa')
            ->whereIn('status_ppdb', ['berkas_kurang', 'diverifikasi', 'daftar_ulang'])
            ->update(['status_ppdb' => 'pending']);

        Schema::table('siswa', function (Blueprint $table) {
            $table->dropColumn('catatan_verifikasi');
        });

        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE siswa MODIFY status_ppdb ENUM('pending','diterima','ditolak') NOT NULL DEFAULT 'pending'");
        }
    }
};
