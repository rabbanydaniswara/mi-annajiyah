<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanKategori extends Model
{
    protected $table = 'kegiatan_kategori';
    protected $fillable = ['nama', 'warna'];

    public function kegiatan()
    {
        return $this->hasMany(KegiatanSekolah::class, 'kategori_id');
    }
}
