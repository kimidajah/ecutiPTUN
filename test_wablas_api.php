    <?php
/**
 * Test script untuk mengirim pesan Wablas
 * Dengan curl langsung
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

echo "=== Wablas API Test ===\n";
echo "Target Phone: $phone\n";
echo "Message: $message\n";
echo "---\n";

// Get env vars
$host = isset($env_vars['WABLAS_HOST']) ? rtrim($env_vars['WABLAS_HOST'], '/') : '';
$token = $env_vars['WABLAS_TOKEN'] ?? '';
$secret = $env_vars['WABLAS_SECRET'] ?? '';

echo "Config Wablas:\n";
echo "  HOST: " . ($host ? 'SET' : 'NOT SET') . " ($host)\n";
echo "  TOKEN: " . ($token ? 'SET (length: ' . strlen($token) . ')' : 'NOT SET') . "\n";
echo "  SECRET: " . ($secret ? 'SET' : 'NOT SET') . "\n";
echo "---\n";

if (!$host || !$token) {
    echo "ERROR: WABLAS_HOST atau WABLAS_TOKEN tidak tersedia di .env\n";
    exit(1);
}

$apiUrl = $host . '/api/send-message';

$payload = [
    'phone' => $phone,
    'message' => $message,
];

if (!empty($secret)) {
    $payload['secret'] = $secret;
}

echo "API URL: $apiUrl\n";
echo "Payload: " . json_encode($payload) . "\n";
echo "---\n";

// Curl request
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
    CURLOPT_FOLLOWLOCATION => true,
]);

echo "Mengirim request...\n";
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_errno = curl_errno($ch);
$curl_error = curl_error($ch);

curl_close($ch);

echo "---\n";
echo "HTTP Status Code: $http_code\n";

if ($curl_errno) {
    echo "cURL Error (#$curl_errno): $curl_error\n";
} else {
    echo "cURL: OK\n";
}

echo "---\n";
echo "Response Body:\n";
if ($response) {
    $decoded = json_decode($response, true);
    if (is_array($decoded)) {
        echo json_encode($decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
    } else {
        echo $response . "\n";
    }
} else {
    echo "(empty)\n";
}

// Determine success
$success = ($http_code >= 200 && $http_code < 300) && $curl_errno === 0;
echo "---\n";
echo $success ? "✅ SUCCESS\n" : "❌ FAILED\n";

exit($success ? 0 : 1);
