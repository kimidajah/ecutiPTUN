<?php

namespace App\Helpers;

class FormatHelper
{
    public static function notifHR($cuti)
    {
        return 
"📢 *Pengajuan Cuti Baru*

Nama: {$cuti->user->name}
Jenis Cuti: {$cuti->jenis_cuti}
Tanggal: {$cuti->tanggal_mulai} s/d {$cuti->tanggal_selesai}

Lama: {$cuti->lama_cuti} Hari
Alasan: {$cuti->alasan}";
    }

    public static function notifPimpinan($cuti)
    {
        return 
"📢 *Validasi Cuti dari HR*

Nama: {$cuti->user->name}
Jenis Cuti: {$cuti->jenis_cuti}
Tanggal: {$cuti->tanggal_mulai} s/d {$cuti->tanggal_selesai}

Lama: {$cuti->lama_cuti} Hari
Alasan: {$cuti->alasan}

Status: Menunggu persetujuan pimpinan.";

    }

    public static function notifKetua($cuti)
    {
        return 
"📢 *Validasi Cuti dari HR*

Nama: {$cuti->user->name}
Jenis Cuti: {$cuti->jenis_cuti}
Tanggal: {$cuti->tanggal_mulai} s/d {$cuti->tanggal_selesai}

Lama: {$cuti->lama_cuti} Hari
Alasan: {$cuti->alasan}

Status: Menunggu persetujuan ketua.";

    }

    // ============================
    // NOTIF: CUTI DITOLAK PEGAWAI
    // ============================
    public static function notifPegawaiRejected($cuti)
    {
        return "
❌ *Pengajuan Cuti Ditolak*

Halo *{$cuti->user->name}*,  
Pengajuan cuti Anda telah *DITOLAK*.

*Detail Cuti Anda:*  
• Jenis : {$cuti->jenis_cuti}  
• Tanggal : {$cuti->tanggal_mulai} s/d {$cuti->tanggal_selesai}  
• Lama : {$cuti->lama_cuti} Hari  
• Alasan : {$cuti->alasan}

Mohon hubungi HR untuk informasi lebih lanjut.
";
    }

    public static function notifPegawaiApprovedHR($cuti)
{
    return "
✅ *Pengajuan Cuti Disetujui hr*

Halo *{$cuti->user->name}*,
Pengajuan cuti Anda telah *DISETUJUI OLEH HR*.

*Detail Cuti Anda:*  
• Jenis : {$cuti->jenis_cuti}  
• Tanggal : {$cuti->tanggal_mulai} s/d {$cuti->tanggal_selesai}  
• Lama : {$cuti->lama_cuti} Hari  
• Alasan : {$cuti->alasan}

";
}

    public static function notifPegawaiApprovedPimpinan($cuti)
{
    return "
✅ *Pengajuan Cuti Disetujui Pimpinan*

Halo *{$cuti->user->name}*,
Pengajuan cuti Anda telah *DISETUJUI OLEH PIMPINAN*.

*Detail Cuti Anda:*  
• Jenis : {$cuti->jenis_cuti}  
• Tanggal : {$cuti->tanggal_mulai} s/d {$cuti->tanggal_selesai}  
• Lama : {$cuti->lama_cuti} Hari  
• Alasan : {$cuti->alasan}

";
}

    public static function notifPegawaiApprovedKetua($cuti)
{
    return "
✅ *Pengajuan Cuti Disetujui Ketua*

Halo *{$cuti->user->name}*,
Pengajuan cuti Anda telah *DISETUJUI OLEH KETUA*.

*Detail Cuti Anda:*  
• Jenis : {$cuti->jenis_cuti}  
• Tanggal : {$cuti->tanggal_mulai} s/d {$cuti->tanggal_selesai}  
• Lama : {$cuti->lama_cuti} Hari  
• Alasan : {$cuti->alasan}

";
}

}