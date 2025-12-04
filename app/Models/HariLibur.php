<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HariLibur extends Model
{
    use HasFactory;

    protected $table = 'hari_libur';

    protected $fillable = [
        'tanggal',
        'nama_hari_libur',
        'keterangan',
        'tahun',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
