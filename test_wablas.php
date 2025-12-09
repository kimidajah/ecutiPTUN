<?php
/**
 * Test script untuk mengirim pesan Wablas
 * 
 * Usage:
 *   php test_wablas.php <phone> <message>
 * 
 * Contoh:
 *   php test_wablas.php 628123456789 "Test message"
 */

require 'app/Helpers/WablasService.php';

// Load .env
$env_path = __DIR__ . '/.env';
if (file_exists($env_path)) {
    $lines = file($env_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') === false || strpos($line, '#') === 0) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, '\'" ');
        if (!getenv($key)) {
            putenv("$key=$value");
        }
    }
}

$phone = $argv[1] ?? '6283844452722';  // Default ke HR Hilman
$message = $argv[2] ?? 'Test message dari sistem e-Cuti PTUN';

echo "Mengirim pesan ke: $phone\n";
echo "Pesan: $message\n";
echo "---\n";

// Gunakan reflection untuk test (karena namespace issue)
require 'vendor/autoload.php';

// Manually init
echo "Env WABLAS_HOST: " . env('WABLAS_HOST') . "\n";
echo "Env WABLAS_TOKEN: " . (env('WABLAS_TOKEN') ? 'SET' : 'NOT SET') . "\n";
echo "Env WABLAS_SECRET: " . (env('WABLAS_SECRET') ? 'SET' : 'NOT SET') . "\n";

// Test dengan direct HTTP call
$host = rtrim(env('WABLAS_HOST', ''), '/');
$token = env('WABLAS_TOKEN');
$secret = env('WABLAS_SECRET', '');

$apiUrl = $host . '/api/send-message';

echo "API URL: $apiUrl\n";
echo "---\n";

$payload = [
    'phone' => $phone,
    'message' => $message,
];

if (!empty($secret)) {
    $payload['secret'] = $secret;
}

echo "Payload: " . json_encode($payload, JSON_PRETTY_PRINT) . "\n";
echo "---\n";

// Test dengan curl
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        'Authorization: ' . $token,
        'Content-Type: application/json',
    ],
    CURLOPT_TIMEOUT => 8,
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_error = curl_error($ch);
curl_close($ch);

echo "HTTP Status: $http_code\n";
echo "cURL Error: " . ($curl_error ? $curl_error : 'None') . "\n";
echo "Response:\n" . ($response ? json_encode(json_decode($response), JSON_PRETTY_PRINT) : '(empty)') . "\n";
