<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WablasService
{
    /**
     * Send WhatsApp message via Wablas API
     * 
     * @param string $phone Phone number (format: 6281234567890)
     * @param string $message Message content
     * @return array Response from API
     */
    public static function sendMessage($phone, $message)
    {
        try {
            $apiUrl = env('WABLAS_HOST') . '/api/send-message';
            $token = env('WABLAS_TOKEN');

            // Format phone number if needed
            $phone = self::formatPhoneNumber($phone);

            $response = Http::withHeaders([
                'Authorization' => $token,
                'Content-Type' => 'application/json'
            ])->post($apiUrl, [
                'phone' => $phone,
                'message' => $message,
                'secret' => false,
                'priority' => false,
            ]);

            $result = $response->json();

            // Log untuk debugging
            Log::info('Wablas API Response', [
                'phone' => $phone,
                'status_code' => $response->status(),
                'response' => $result
            ]);

            return $result;
        } catch (\Exception $e) {
            Log::error('Wablas API Error', [
                'phone' => $phone,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Format phone number to international format
     * 
     * @param string $phone Phone number
     * @return string Formatted phone number
     */
    private static function formatPhoneNumber($phone)
    {
        // Remove non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // If starts with 0, replace with 62
        if (strpos($phone, '0') === 0) {
            $phone = '62' . substr($phone, 1);
        }

        // If doesn't start with 62, add it
        if (strpos($phone, '62') !== 0) {
            $phone = '62' . $phone;
        }

        return $phone;
    }

    /**
     * Send cuti approval notification
     * 
     * @param string $phone Phone number
     * @param string $name User name
     * @param string $status Status (disetujui/ditolak)
     * @return array Response
     */
    public static function sendCutiApprovalNotification($phone, $name, $status)
    {
        $status_text = $status === 'disetujui_pimpinan' ? 'Disetujui' : 'Ditolak';
        $emoji = $status === 'disetujui_pimpinan' ? '✅' : '❌';

        $message = "*Notifikasi Pengajuan Cuti*\n\n";
        $message .= "Halo $name,\n\n";
        $message .= "$emoji Pengajuan cuti Anda telah *$status_text*\n\n";
        $message .= "Silahkan cek halaman cuti Anda untuk detail lebih lanjut.\n\n";
        $message .= "_Sistem e-Cuti PTUN_";

        return self::sendMessage($phone, $message);
    }

    /**
     * Send cuti creation notification to HR
     * 
     * @param string $phone HR phone number
     * @param string $employee_name Employee name
     * @param string $cuti_type Type of leave
     * @param int $duration Duration in days
     * @return array Response
     */
    public static function sendCutiCreatedNotificationToHR($phone, $employee_name, $cuti_type, $duration)
    {
        $message = "*Notifikasi Pengajuan Cuti Baru*\n\n";
        $message .= "Ada pengajuan cuti masuk dari:\n\n";
        $message .= "*Nama:* $employee_name\n";
        $message .= "*Jenis Cuti:* $cuti_type\n";
        $message .= "*Durasi:* $duration hari\n\n";
        $message .= "Silahkan cek sistem untuk menyetujui atau menolak pengajuan.\n\n";
        $message .= "_Sistem e-Cuti PTUN_";

        return self::sendMessage($phone, $message);
    }

    /**
     * Send cuti cancellation notification
     * 
     * @param string $phone Phone number
     * @param string $name User name
     * @param string $reason Cancellation reason
     * @return array Response
     */
    public static function sendCutiCancellationNotification($phone, $name, $reason = '')
    {
        $message = "*Notifikasi Pembatalan Cuti*\n\n";
        $message .= "Halo $name,\n\n";
        $message .= "⚠️ Pengajuan cuti Anda telah dibatalkan.\n\n";
        if ($reason) {
            $message .= "*Alasan:* $reason\n\n";
        }
        $message .= "Silahkan hubungi HR untuk informasi lebih lanjut.\n\n";
        $message .= "_Sistem e-Cuti PTUN_";

        return self::sendMessage($phone, $message);
    }
}

