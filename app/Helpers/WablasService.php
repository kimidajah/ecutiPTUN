<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WablasService
{
    public static function sendMessage($phone, $message)
    {
        $apiUrl = env('WABLAS_HOST') . '/api/send-message';

        $response = Http::withHeaders([
            'Authorization' => env('WABLAS_TOKEN')
        ])->post($apiUrl, [
            'phone' => $phone,   // contoh: 6281234567890
            'message' => $message,
            'secret' => false,
            'priority' => false,
        ]);

        return $response->json();
    }
}
