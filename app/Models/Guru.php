<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guru extends Model
{
    public $timestamps = false;

    protected $table = 'guru';

    protected $fillable = ['nama', 'mapel', 'jabatan', 'nip', 'no_telp', 'foto', 'urutan', 'tampilkan'];

    protected $casts = ['tampilkan' => 'boolean'];

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'id_guru');
    }
}
