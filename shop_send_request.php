<?php
require_once 'app/session_config.php';
require_once 'app/bale/SendMessage.php';

// اطلاعات کاربر از سشن
$user_name  = ($_SESSION['user_info']['first_name'] ?? '') . ' ' . ($_SESSION['user_info']['last_name'] ?? '');
$user_phone = $_SESSION['user_info']['phone'] ?? 'نامشخص';

// دریافت ورودی از fetch()
$data = json_decode(file_get_contents('php://input'), true);
$product = $data['product'] ?? 'نامشخص';

// متن پیام
$message = "📩 درخواست جدید از کاربر:\n"
          . "👤 نام: {$user_name}\n"
          . "📞 شماره تماس: {$user_phone}\n"
          . "📦 محصول: {$product}";

// شناسه کانال (میتونی از config بگیری)
$channel_chat_id = "6441576316";

// ارسال پیام
$bale = new SendMessage();
$bale->send_messagge($channel_chat_id, $message);

echo json_encode([
    'status' => 'success',
    'message' => '✅ درخواست شما با موفقیت ارسال شد. منتظر تماس پشتیبان باشید.'
]);
