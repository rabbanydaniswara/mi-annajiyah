<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    protected $table = 'fasilitas';
    protected $fillable = ['nama', 'deskripsi', 'ikon', 'gambar', 'urutan', 'aktif'];
    protected $casts = ['aktif' => 'boolean'];
}
