<?php
// توکن ربات و آیدی کانال رو تنظیم کن
$token = '463578540:RvnPoZlFe9Nt1EXFpFgaVIkoFdcTNlLOgpOREFKt';   
$chat_id = '1745122024';    // آیدی گروه یا کانال بله

// دریافت داده‌های POST از فرم ارسال شده
$data = $_POST;

if (!$data || count($data) == 0) {
    echo json_encode(["status" => "error", "message" => "No data received"]);
    exit();
}

// شروع گزارش
$report_text = "💰 *گزارش شارژ ماهانه ساختمان* 💰\n\n";

// پردازش واحدها
foreach ($data as $key => $amount) {
    if ($key === "buildingName") continue; // فیلتر نام ساختمان

    // استخراج شماره واحد از نام کلید (مثلاً charge_1 -> 1)
    $unit_number = str_replace("charge_", "", $key);
    $payment_link = "http://localhost/plat/pay.php?unit=$unit_number&amount=$amount";  // لینک اختصاصی هر واحد

    $report_text .= "🏠 *واحد $unit_number* 🏡\n";
    $report_text .= "💵 مبلغ: *$amount تومان* 💰\n";
    $report_text .= "🔗  [پرداخت آنلاین]($payment_link) \n\n";  // لینک کلیک‌پذیر
}

// ارسال پیام به بله
$url = "https://tapi.bale.ai/bot$token/sendMessage";
$data_to_send = [
    'chat_id' => $chat_id,
    'text' => $report_text,
    'parse_mode' => 'Markdown' // استفاده از HTML Mode
];

$options = [
    'http' => [
        'header'  => "Content-Type: application/json\r\n",
        'method'  => 'POST',
        'content' => json_encode($data_to_send)
    ]
];

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) {
    echo json_encode(["status" => "error", "message" => "Failed to send message"]);
} else {
    echo json_encode(["status" => "success", "message" => "Report sent"]);
}
?>
