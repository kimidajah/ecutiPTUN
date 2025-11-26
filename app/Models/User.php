<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'sisa_cuti',
        'saldo_cuti_tahunan',
        'no_wa',
        'hr_id',
        'pimpinan_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Default value cuti
     */
    protected $attributes = [
        'saldo_cuti_tahunan' => 12,
        'sisa_cuti'           => 12,
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }
    public function cuti()
    {
        return $this->hasMany(Cuti::class);
    }

    public function hr()
    {
        return $this->belongsTo(User::class, 'hr_id');
    }

    public function pimpinan()
    {
        return $this->belongsTo(User::class, 'pimpinan_id');
    }

}
