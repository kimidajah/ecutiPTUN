<?php
/**
 * Test script untuk mengirim pesan Wablas dengan berbagai variasi format secret
 */

// Load .env manually
$env_file = __DIR__ . '/.env';
$env_vars = [];

if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') === false || strpos($line, '#') === 0) continue;
        [$key, $value] = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value, '\'" ');
        $env_vars[$key] = $value;
    }
}

$phone = $argv[1] ?? '6283844452722';
$message = $argv[2] ?? 'Test message dari sistem e-Cuti PTUN';

// Normalize phone
function normalizePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strpos($phone, '0') === 0) {
        $phone = '62' . substr($phone, 1);
    }
    if (strpos($phone, '62') !== 0) {
        $phone = '62' . $phone;
    }
    return $phone;
}

$phone = normalizePhone($phone);

echo "=== Wablas API Test (Multiple Variations) ===\n";

$host = isset($env_vars['WABLAS_HOST']) ? rtrim($env_vars['WABLAS_HOST'], '/') : '';
$token = $env_vars['WABLAS_TOKEN'] ?? '';
$secret = $env_vars['WABLAS_SECRET'] ?? '';

if (!$host || !$token) {
    echo "ERROR: Missing config\n";
    exit(1);
}

echo "Phone: $phone\n";
echo "Message: $message\n";
echo "---\n";

$apiUrl = $host . '/api/send-message';

// Try 1: Secret in payload
echo "\n[Test 1] Secret in payload:\n";
$payload = [
    'phone' => $phone,
    'message' => $message,
    'secret' => $secret,
];

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
    CURLOPT_TIMEOUT => 10,
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $http_code\n";
if ($response) {
    $decoded = json_decode($response, true);
    echo "Response: " . ($decoded['message'] ?? $response) . "\n";
}

// Try 2: Secret as query parameter
echo "\n[Test 2] Secret as query parameter:\n";
$payload = [
    'phone' => $phone,
    'message' => $message,
];

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl . '?secret=' . urlencode($secret),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        'Authorization: ' . $token,
        'Content-Type: application/json',
    ],
    CURLOPT_TIMEOUT => 10,
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $http_code\n";
if ($response) {
    $decoded = json_decode($response, true);
    echo "Response: " . ($decoded['message'] ?? $response) . "\n";
}

// Try 3: Secret as header
echo "\n[Test 3] Secret as header (X-Secret-Key):\n";
$payload = [
    'phone' => $phone,
    'message' => $message,
];

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $apiUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($payload),
    CURLOPT_HTTPHEADER => [
        'Authorization: ' . $token,
        'X-Secret-Key: ' . $secret,
        'Content-Type: application/json',
    ],
    CURLOPT_TIMEOUT => 10,
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $http_code\n";
if ($response) {
    $decoded = json_decode($response, true);
    echo "Response: " . ($decoded['message'] ?? $response) . "\n";
}

// Try 4: Without secret at all (token should be enough)
echo "\n[Test 4] Without secret (just token):\n";
$payload = [
    'phone' => $phone,
    'message' => $message,
];

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
    CURLOPT_TIMEOUT => 10,
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $http_code\n";
if ($response) {
    $decoded = json_decode($response, true);
    echo "Response: " . ($decoded['message'] ?? $response) . "\n";
}

echo "\n---\n";
echo "Note: Error 403 usually means IP not whitelisted or authentication issue.\n";
echo "      Check Wablas dashboard for IP whitelist settings.\n";
