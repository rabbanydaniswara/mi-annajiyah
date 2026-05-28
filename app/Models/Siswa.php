<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Siswa extends Model
{
    public $timestamps = false;
    protected $table = 'siswa';
    protected $fillable = [
        'registration_token', 'nama', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin',
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

    protected static function booted(): void
    {
        static::creating(function (Siswa $siswa) {
            if (!$siswa->registration_token) {
                do {
                    $token = Str::random(40);
                } while (self::where('registration_token', $token)->exists());

                $siswa->registration_token = $token;
            }
        });
    }
}
