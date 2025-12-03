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
    'catatan_pimpinan',
    'alamat_selama_cuti',
    'telp_selama_cuti',
];


    public function user()
    {
        return $this->belongsTo(User::class);
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
