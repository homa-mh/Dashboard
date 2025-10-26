<?php
session_start();
header('Content-Type: application/json');

$folder = 'garanti/';
if (!file_exists($folder)) mkdir($folder, 0777, true);

try {
    // مرحله ۱
    $customer_name = $_POST['customer_name'] ?? 'نا مشخص';
    $device_qr = $_POST['device_qr'] ?? '---';
    $installer_name = $_POST['installer_name'] ?? '---';

    // مرحله ۲
    $received = isset($_POST['received']) ? '✅ محصول دریافت شد' : '❌ محصول دریافت نشد';
    $users_added = isset($_POST['users_added']) ? '✅ کاربران اضافه شدند' : '❌ کاربران اضافه نشدند';
    $trained = isset($_POST['trained']) ? '✅ آموزش داده شد' : '❌ آموزش داده نشد';

    $phone = $_SESSION["user_info"]["phone"] ?? 'نا مشخص';
    $admin_name = ($_SESSION["user_info"]["first_name"] ?? '') . ' ' . ($_SESSION["user_info"]["last_name"] ?? '');
    $date = date('Y-m-d H:i:s');

    // ذخیره امضا
    $signatureFile = '';
    if (!empty($_POST['signature'])) {
        $img = str_replace(['data:image/png;base64,', ' '], ['', '+'], $_POST['signature']);
        $signatureFileName = 'signature_' . time() . '.png';
        $signatureFile = $folder . $signatureFileName;
        file_put_contents($signatureFile, base64_decode($img));
    }

    // ساخت فایل HTML به عنوان شبه PDF
    $htmlFile = $folder . 'form_' . time() . '.html';
    $htmlContent = "
    <html lang='fa' dir='rtl'>
    <head><meta charset='UTF-8'><title>فرم گارانتی</title></head>
    <body style='font-family:Tahoma; line-height:1.8;'>
        <h2>📋 فرم گارانتی</h2>
        <p><b>مشتری:</b> $customer_name</p>
        <p><b>QR دستگاه:</b> $device_qr</p>
        <p><b>نصاب:</b> $installer_name</p>
        <p><b>$received</b><br><b>$users_added</b><br><b>$trained</b></p>
        <p><b>ثبت‌کننده:</b> $admin_name ($phone)</p>
        <p><b>تاریخ ثبت:</b> $date</p>
        <img src='$signatureFileName' width='200'><br><br>
        <hr>
    </body>
    </html>";
    file_put_contents($htmlFile, $htmlContent);

    // ارسال به بله
    require_once "app/bale/SendMessage.php";
    $bale = new SendMessage();

    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    $htmlUrl = $protocol . $host . rtrim($path, '/') . '/' . $htmlFile;

    $msg = "📋 فرم گارانتی جدید:\n"
         . "👤 مشتری: $customer_name\n"
         . "📱 QR: $device_qr\n"
         . "🔧 نصاب: $installer_name\n"
         . "$received\n$users_added\n$trained\n"
         . "🕓 تاریخ ثبت: $date\n"
         . "📎 مشاهده فرم کامل: $htmlUrl";

    $sent = $bale->send_messagge("6441576316", $msg);

    echo json_encode(['success' => true, 'message' => '✅ فرم با موفقیت ثبت و ارسال شد.']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '❌ خطای داخلی: ' . $e->getMessage()]);
}
