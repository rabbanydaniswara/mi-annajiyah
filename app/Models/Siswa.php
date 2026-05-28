<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    public $timestamps = false;
    protected $table = 'siswa';
    protected $fillable = [
        'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
        'nisn', 'nis', 'akte_kelahiran', 'file_akte',
        'no_kk', 'file_kk', 'alamat', 'asal_sekolah', 'nama_ortu',
        'file_ktp_ortu', 'no_wa', 'kelas', 'tanggal_daftar',
        'status_ppdb', 'tgl_verifikasi', 'file_ijazah'
    ];
    protected $casts = [
        'tanggal_lahir' => 'date',
        'tanggal_daftar' => 'datetime',
        'tgl_verifikasi' => 'datetime',
    ];
}
