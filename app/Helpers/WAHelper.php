<?php

namespace App\Helpers;

class WAHelper
{
    public static function normalizePhone($number)
    {
        // Hilangkan spasi, strip, titik
        $number = preg_replace('/[^0-9]/', '', $number);

        // Jika mulai dari 0 → ubah ke 62
        if (substr($number, 0, 1) === "0") {
            $number = "62" . substr($number, 1);
        }

        return $number;
    }

    public static function send($target, $message)
    {
        // Gunakan WablasService agar semua pengiriman WA memakai Wablas
        // normalisasi nomor
        $target = self::normalizePhone($target);

        // Panggil WablasService yang sudah ada
        return WablasService::sendMessage($target, $message);
    }
}
