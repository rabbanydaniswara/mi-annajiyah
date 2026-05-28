<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banner', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200)->nullable();
            $table->string('subtitle', 200)->nullable();
            $table->string('gambar', 255)->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
        });

        Schema::create('guru', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('mapel', 100);
            $table->string('jabatan', 100)->nullable();
            $table->string('nip', 50)->nullable();
            $table->string('no_telp', 15)->nullable();
            $table->string('foto', 255)->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('tampilkan')->default(true);
        });

        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->string('mapel', 100);
            $table->foreignId('id_guru')->constrained('guru')->onDelete('cascade');
            $table->string('kelas', 10);
            $table->string('ruangan', 20)->nullable();
        });

        Schema::create('kegiatan_kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('warna', 30)->default('green');
            $table->timestamps();
        });

        Schema::create('kegiatan_sekolah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')->nullable()->constrained('kegiatan_kategori')->nullOnDelete();
            $table->string('judul', 200);
            $table->text('deskripsi')->nullable();
            $table->string('gambar', 255)->nullable();
            $table->date('tanggal')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('konten_web', function (Blueprint $table) {
            $table->id();
            $table->string('tipe', 50);
            $table->string('judul', 200)->nullable();
            $table->text('konten')->nullable();
            $table->string('gambar', 255)->nullable();
            $table->integer('urutan')->default(0);
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('fasilitas', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->text('deskripsi')->nullable();
            $table->string('ikon', 100)->nullable();
            $table->string('gambar', 255)->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });

        Schema::create('siswa', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan'])->nullable();
            $table->string('nisn', 20)->nullable();
            $table->string('nis', 20)->nullable();
            $table->string('asal_sekolah', 150)->nullable();
            $table->string('akte_kelahiran', 50)->nullable();
            $table->string('file_akte', 255)->nullable();
            $table->string('no_kk', 50)->nullable();
            $table->string('file_kk', 255)->nullable();
            $table->text('alamat')->nullable();
            $table->string('nama_ortu', 100)->nullable();
            $table->string('file_ktp_ortu', 255)->nullable();
            $table->string('no_wa', 15)->nullable();
            $table->string('kelas', 10)->nullable();
            $table->timestamp('tanggal_daftar')->useCurrent();
            $table->enum('status_ppdb', ['pending', 'diterima', 'ditolak'])->default('pending');
            $table->datetime('tgl_verifikasi')->nullable();
            $table->string('file_ijazah', 255)->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal');
        Schema::dropIfExists('kegiatan_sekolah');
        Schema::dropIfExists('kegiatan_kategori');
        Schema::dropIfExists('konten_web');
        Schema::dropIfExists('fasilitas');
        Schema::dropIfExists('siswa');
        Schema::dropIfExists('guru');
        Schema::dropIfExists('banner');
    }
};
