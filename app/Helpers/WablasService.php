<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WablasService
{
    /**
     * Send WhatsApp message via Wablas API
     *
     * @param string $phone Phone number (any common format)
     * @param string $message Message content
     * @return array Structured response: ['success' => bool, 'status_code' => int|null, 'data' => array|null, 'error' => string|null]
     */
    public static function sendMessage($phone, $message): array
    {
        // Basic validation
        $phoneRaw = (string) $phone;
        $message = (string) $message;

        $host = rtrim(env('WABLAS_HOST', ''), '/');
        $token = env('WABLAS_TOKEN');
        $secret = env('WABLAS_SECRET', '');

        if (empty($host) || empty($token)) {
            $err = 'Wablas host or token not configured';
            Log::error('Wablas config error', ['host' => $host ? 'set' : 'missing', 'token' => $token ? 'set' : 'missing']);
            return ['success' => false, 'status_code' => null, 'data' => null, 'error' => $err];
        }

        $apiUrl = $host . '/api/send-message';

        // Format phone, validate result
        $phone = self::formatPhoneNumber($phoneRaw);
        if (empty($phone) || !preg_match('/^62[0-9]{6,15}$/', $phone)) {
            $err = 'Invalid phone number after normalization';
            Log::warning('Wablas invalid phone', ['original' => $phoneRaw, 'normalized' => $phone]);
            return ['success' => false, 'status_code' => null, 'data' => null, 'error' => $err];
        }

        try {
            // Prepare payload
            $payload = [
                'phone'    => $phone,
                'message'  => $message,
            ];

            // Add secret jika tersedia
            if (!empty($secret)) {
                $payload['secret'] = $secret;
            }

            // Use Authorization header dengan token langsung (bukan Bearer)
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])
                ->timeout(8)          // seconds
                ->retry(2, 200)       // retry 2 times, 200ms backoff
                ->post($apiUrl, $payload);

            $status = $response->status();
            $body = $response->json();

            Log::info('Wablas API Response', [
                'phone' => $phone,
                'status_code' => $status,
                'response' => $body,
            ]);

            if ($response->successful()) {
                return ['success' => true, 'status_code' => $status, 'data' => $body, 'error' => null];
            }

            // Non-2xx
            $errMsg = $body['message'] ?? ($body['error'] ?? 'Wablas returned non-success status');
            return ['success' => false, 'status_code' => $status, 'data' => $body, 'error' => $errMsg];
        } catch (\Throwable $e) {
            Log::error('Wablas API Error', [
                'phone' => $phone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return ['success' => false, 'status_code' => null, 'data' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Normalize phone number to "62..." format.
     * Returns empty string when invalid.
     *
     * @param string $phone
     * @return string
     */
    private static function formatPhoneNumber($phone): string
    {
        $phone = trim((string) $phone);

        if ($phone === '') {
            return '';
        }

        // Remove everything except digits
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 0, replace with 62
        if (strpos($phone, '0') === 0) {
            $phone = '62' . substr($phone, 1);
        }

        // If starts with 8 (user omitted leading 0), assume local and prefix 62
        if (strpos($phone, '62') !== 0 && strpos($phone, '0') !== 0) {
            // e.g. '8123456' -> '628123456'
            $phone = '62' . $phone;
        }

        // Final sanity: only digits, starts with 62
        if (!preg_match('/^62[0-9]{6,15}$/', $phone)) {
            return '';
        }

        return $phone;
    }

    // --- Notification helpers kept but returning standardized response shape ---

    public static function sendCutiApprovalNotification($phone, $name, $status): array
    {
        $status_text = $status === 'disetujui_pimpinan' ? 'Disetujui' : 'Ditolak';
        $emoji = $status === 'disetujui_pimpinan' ? '✅' : '❌';

        $message = "*Notifikasi Pengajuan Cuti*\n\n";
        $message .= "Halo {$name},\n\n";
        $message .= "{$emoji} Pengajuan cuti Anda telah *{$status_text}*\n\n";
        $message .= "Silahkan cek halaman cuti Anda untuk detail lebih lanjut.\n\n";
        $message .= "_Sistem e-Cuti PTUN_";

        return self::sendMessage($phone, $message);
    }

    public static function sendCutiCreatedNotificationToHR($phone, $employee_name, $cuti_type, $duration): array
    {
        $message = "*Notifikasi Pengajuan Cuti Baru*\n\n";
        $message .= "Ada pengajuan cuti masuk dari:\n\n";
        $message .= "*Nama:* {$employee_name}\n";
        $message .= "*Jenis Cuti:* {$cuti_type}\n";
        $message .= "*Durasi:* {$duration} hari\n\n";
        $message .= "Silahkan cek sistem untuk menyetujui atau menolak pengajuan.\n\n";
        $message .= "_Sistem e-Cuti PTUN_";

        return self::sendMessage($phone, $message);
    }

    public static function sendCutiCancellationNotification($phone, $name, $reason = ''): array
    {
        $message = "*Notifikasi Pembatalan Cuti*\n\n";
        $message .= "Halo {$name},\n\n";
        $message .= "⚠️ Pengajuan cuti Anda telah dibatalkan.\n\n";
        if ($reason) {
            $message .= "*Alasan:* {$reason}\n\n";
        }
        $message .= "Silahkan hubungi HR untuk informasi lebih lanjut.\n\n";
        $message .= "_Sistem e-Cuti PTUN_";

        return self::sendMessage($phone, $message);
    }
}
