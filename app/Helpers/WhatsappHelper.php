<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class WhatsappHelper
{
    public static function send($target, $message)
    {
        $url = env('WA_API_URL');
        $token = env('WA_API_TOKEN');

        return Http::withHeaders([
            'Authorization' => $token
        ])->post($url, [
            'target' => $target,
            'message' => $message
        ]);
    }
}
