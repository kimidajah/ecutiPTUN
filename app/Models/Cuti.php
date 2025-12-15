<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    use HasFactory;

    protected $table = 'cuti';

protected $fillable = [
    'user_id',
    'jenis_cuti',
    'tanggal_mulai',
    'tanggal_selesai',
    'lama_cuti',
    'alasan',
    'status',
    'catatan_hr',
    'catatan_ketua',
    'catatan_pimpinan',
    'alamat_selama_cuti',
    'telp_selama_cuti',
    'bukti_file',
    'atasan_id',
    'pimpinan_id',
    'kategori_atasan',
    'kategori_pimpinan',
];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function atasan()
    {
        return $this->belongsTo(User::class, 'atasan_id');
    }

    public function pimpinan()
    {
        return $this->belongsTo(User::class, 'pimpinan_id');
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'warning',   // pending
        };
    }

}
