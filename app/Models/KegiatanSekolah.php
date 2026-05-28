<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KegiatanSekolah extends Model
{
    public $timestamps = false;
    protected $table = 'kegiatan_sekolah';
    protected $fillable = ['judul', 'deskripsi', 'gambar', 'tanggal', 'kategori_id'];
    protected $casts = ['tanggal' => 'date'];

    public function kategori()
    {
        return $this->belongsTo(KegiatanKategori::class, 'kategori_id');
    }
}
