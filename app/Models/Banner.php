<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    public $timestamps = false;

    protected $table = 'banner';

    protected $fillable = ['judul', 'subtitle', 'gambar', 'urutan', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];
}
