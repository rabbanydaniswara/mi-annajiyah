<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KontenWeb extends Model
{
    public $timestamps = false;
    protected $table = 'konten_web';
    protected $fillable = ['tipe', 'judul', 'konten', 'gambar', 'urutan'];
}
