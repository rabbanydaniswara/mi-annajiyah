<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            // Kolom nisn dan nis sudah memiliki unique index di migration sebelumnya.
            $table->index('no_wa');
            $table->index('status_ppdb');
            $table->index('tanggal_daftar');
        });

        Schema::table('kegiatan_sekolah', function (Blueprint $table) {
            $table->index('tanggal');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa', function (Blueprint $table) {
            $table->dropIndex(['no_wa']);
            $table->dropIndex(['status_ppdb']);
            $table->dropIndex(['tanggal_daftar']);
        });

        Schema::table('kegiatan_sekolah', function (Blueprint $table) {
            $table->dropIndex(['tanggal']);
        });
    }
};
