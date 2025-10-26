<?php
// فایل proxy.php

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// آدرس API اصلی
$url = "http://185.235.197.220/info-api/usage_logs?unique_qr_code=";
$url .= urlencode($_GET['qr']);
$url .= "&token=rW8QoV5yP2aL3xZ4hT7jK9bN6mD1sF0g";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo json_encode(["error" => curl_error($ch)]);
} else {
    echo $response;
}

curl_close($ch);
