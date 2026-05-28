<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Siswa table indexes
        try {
            Schema::table('siswa', function (Blueprint $table) {
                $table->index('nama', 'idx_siswa_nama');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('siswa', function (Blueprint $table) {
                $table->index('no_wa', 'idx_siswa_no_wa');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('siswa', function (Blueprint $table) {
                $table->index('status_ppdb', 'idx_siswa_status');
            });
        } catch (\Exception $e) {}

        try {
            Schema::table('siswa', function (Blueprint $table) {
                $table->index('tanggal_daftar', 'idx_siswa_tgl_daftar');
            });
        } catch (\Exception $e) {}

        // Kegiatan table indexes
        try {
            Schema::table('kegiatan_sekolah', function (Blueprint $table) {
                $table->index('tanggal', 'idx_kegiatan_tgl');
            });
        } catch (\Exception $e) {}
    }

    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropIndex('idx_siswa_nama');
            $table->dropIndex('idx_siswa_no_wa');
            $table->dropIndex('idx_siswa_status');
            $table->dropIndex('idx_siswa_tgl_daftar');
        });

        Schema::table('kegiatan_sekolah', function (Blueprint $table) {
            $table->dropIndex('idx_kegiatan_tgl');
        });
    }
};
