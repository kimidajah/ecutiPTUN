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
        $url = env('WA_API_URL');
        $token = env('WA_API_TOKEN');

        // normalisasi nomor
        $target = self::normalizePhone($target);

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => [
                'target' => $target,
                'message' => $message
            ],
            CURLOPT_HTTPHEADER => [
                "Authorization: $token"
            ]
        ]);

        $response = curl_exec($curl);
        curl_close($curl);

        return $response;
    }
}
