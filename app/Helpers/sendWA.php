<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

if (!function_exists('sendWA')) {

    /**
     * Kirim pesan WhatsApp
     */
    function sendWA($nomor, $pesan)
    {
        try {

            // HINDARI FORMAT 08 → konversi ke 628
            if (substr($nomor, 0, 1) === '0') {
                $nomor = '62' . substr($nomor, 1);
            }

            $apiUrl = env('WA_API_URL');
            $token  = env('WA_API_TOKEN');

            $response = Http::withToken($token)->post($apiUrl, [
                'to'      => $nomor,
                'message' => $pesan,
            ]);

            // Log jika gagal
            if (!$response->successful()) {
                Log::error('WA Error:', [
                    'nomor' => $nomor,
                    'pesan' => $pesan,
                    'res'   => $response->body()
                ]);
            }

        } catch (\Exception $e) {

            Log::error("WA Exception: " . $e->getMessage());
        }
    }
}
