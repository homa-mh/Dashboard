<?php
session_start();
// $apiUrl = "http://185.235.197.220/info-api/device-details/by-phone/" . $_SESSION['user_info']['phone'];
$apiUrl = "http://185.235.197.220/info-api/device-details/by-phone/" . "09366774986";

$ch = curl_init($apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
$response = curl_exec($ch);
curl_close($ch);
header('Content-Type: application/json; charset=utf-8');
echo $response;
