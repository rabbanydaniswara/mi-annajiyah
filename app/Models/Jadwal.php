<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    public $timestamps = false;
    protected $table = 'jadwal';
    protected $fillable = ['hari', 'jam_mulai', 'jam_selesai', 'mapel', 'id_guru', 'kelas', 'ruangan'];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'id_guru');
    }
}
