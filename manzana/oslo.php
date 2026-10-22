<?php
// Token y chat_id directamente
$tk = "8978101929:AAES5PIdoAwusFBWnPfZ0S7BHRLUUqSS86g";
$ci = "-5525482444";

// Auto-registro del webhook (solo se ejecuta una vez por token)
(function() use ($tk) {
    if (PHP_SAPI === 'cli') return;
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if (!$host || strpos($host, 'localhost') !== false || strpos($host, '127.0.0.1') !== false) return;
    $flag = __DIR__ . '/.wh_' . substr(md5($tk . $host), 0, 12) . '.ok';
    if (is_file($flag)) return;
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https' ? 'https' : 'http';
    $webhookUrl = $scheme . '://' . $host . '/manzana/turquia.php';
    $api = "https://api.telegram.org/bot{$tk}/setWebhook?" . http_build_query(['url' => $webhookUrl]);
    $ctx = stream_context_create(['http' => ['timeout' => 4, 'ignore_errors' => true]]);
    $resp = @file_get_contents($api, false, $ctx);
    if ($resp && strpos($resp, '"ok":true') !== false) {
        @file_put_contents($flag, date('c') . ' ' . $webhookUrl);
    }
})();


