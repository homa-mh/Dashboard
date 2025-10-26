<?php
require_once 'app/auth_check.php';

if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token'] || $_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    die('دسترسی غیرمجاز: CSRF Token نامعتبر است!');
}
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

// گرفتن داده‌ها
$months = isset($_POST['months']) ? $_POST['months'] : [];
$total = $_POST['total'] ?? null;
$fixed_charge = $_POST['fixed_charge'] ?? null;

$errors = [];

if (empty($months) || !is_array($months)) {
    $errors[] = "حداقل یک ماه باید انتخاب شود.";
} if(!is_numeric($fixed_charge)){
        $errors[] = "شارژ ثابت باید به صورت عددی وارد شود.";

}

// گرفتن شارژ واحدها
$unit_charges = [];
foreach ($_POST as $key => $value) {
    if (preg_match('/^unit(\d+)_charge$/', $key, $matches)) {
        $unit_num = $matches[1];
        $charge = trim($value);
        if ($charge === "" || !is_numeric($charge) || $charge < 0) {
            $errors[] = "مقدار شارژ وارد شده برای واحد {$unit_num} نامعتبر است.";
        }
        $unit_charges[$unit_num] = floatval($charge);
    }
}

// فاکتورها
$factors = [];
foreach ($_POST as $key => $value) {
    if (preg_match('/^factortitle(\d+)$/', $key, $matches)) {
        $index = $matches[1];
        $factors[] = [
            'title' => $_POST["factortitle$index"],
            'description' => $_POST["factordescription$index"] ?? "-",
            'amount' => $_POST["factoramount$index"]
        ];
    }
}

// قبض ها
$bills = [];
foreach ($_POST as $key => $value) {
    if (preg_match('/^billtitle(\d+)$/', $key, $matches)) {
        $index = $matches[1];
        $bills[] = [
            'title' => $_POST["billtitle$index"],
            'amount' => $_POST["billamount$index"]
        ];
    }
}
// بررسی خطاها
if (!empty($errors)) {
    $error_str = urlencode(implode(' | ', $errors));
    header("Location: index.php?error=$error_str");
    die();
}

// ارسال به کنترلر
require_once 'app/controllers/ChargeCtrl.php';
$ctrl = new ChargeCtrl();

$result = $ctrl->send_charge_full($months, $unit_charges, (float)($total), $_SESSION['user_info']['channel_chat_id'], (float)($fixed_charge), $factors + $bills);

if ($result) {
    $ctrl->update_factors($_SESSION['user_info']['id']);
    header("Location: index.php?success=شارژ ارسال شد.");
    die();
} else {
    header("Location: index.php?success=خطا در ارسال شارژ.");
    die();
}
