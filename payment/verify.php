<?php
ini_set("display_errors", 1);
error_reporting(E_ALL);

$status    = $_REQUEST['Status']   ?? $_REQUEST['status']   ?? null;
$info      = $_REQUEST['info']     ?? $_REQUEST['Info']     ?? null;
$Authority = $_REQUEST['Authority'] ?? null; 

file_put_contents("callback_log.txt", date("Y-m-d H:i:s") . " => " . print_r($_REQUEST, true) . "\n", FILE_APPEND);

if ($status === null || $info === null || $Authority === null) {
    echo '{"status":"error","message":"No data received"}';
    exit();
}

$pd   = substr($info, 0, 3);
$user_id = substr($info, 3);
$amount = 0;


// دریافت محصول و قیمت محصول
include "../app/Dbh.php";
$db = new Dbh();
$pdo = $db->connect();

try {
    $sql = "SELECT * FROM products WHERE id = ?;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$pd]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result === false) {
        echo '{"status":"error","message":"Product not found"}';
        exit();
    }
    
    $product = $result["title"];
    $amount = $result["price"];
    
    
    // info for owner tranfer
    if($result["category"] == "transfer"){
        $parts = explode('_', $info);
        
        if(isset($parts[1])){
            $new_owner_phone = substr($parts[1], 0, 11);
            $new_owner_name  = substr($parts[1], 11);
        } else {
            $new_owner_phone = "";
            $new_owner_name  = "";
        }
        if(isset($parts[2])){
            $qr_code = substr($parts[2], 0);
        }else{
            $qr_code = "";
        }
    }
    if($result["category"] == "user"){
        $parts = explode('_', $info);
        
        if(isset($parts[1])){
            $user_count = $parts[1];
        } else {
            $user_count = "";
        }
        
    }
    
    // product in sms
    $req = "";
    if($result["category"] == "repair"){
        $req = "تعمیرکار";
    }else if($result["category"] == "install"){
        $req = "نصاب";
    }else if($result["category"] == "update"){
        $req = "آپدیت";
    }else if($result["category"] == "transfer"){
        $req = "انتقال";
    }else if($result["category"] == "sub"){
        $req = "اشتراک";
    }else if($result["category"] == "user"){
        $req = "کاربر";
    }


    
} catch(PDOException $e) {
    echo "error in select user data: ".$e->getMessage();
    exit();
}   


$msg_success = "خطا در پرداخت.";


//   Verify with Zarinpal    //

if (strtoupper($status) == "OK") {
    $MerchantID = "b8e15a68-131c-422a-ad5b-150941835d4e";
    
    $data = [
        "merchant_id" => $MerchantID,
        "authority"   => $Authority,
        "amount"      => $amount,
    ];

    $jsonData = json_encode($data);

    $ch = curl_init("https://api.zarinpal.com/pg/v4/payment/verify.json");
    curl_setopt($ch, CURLOPT_USERAGENT, 'ZarinPal Rest Api v4');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($jsonData)
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);

    if (isset($result["data"]["code"]) && $result["data"]["code"] == 100) {
        // پرداخت موفق
        $refId = $result["data"]["ref_id"];
        $msg_success = " پرداخت ". $product . " با موفقیت انجام شد. <br>" . "کد پیگیری: " . $refId;



    // ثبت در دیتابیس
    
    // افزودن به جدول پرداخت
    try {
        $stmt = $pdo->prepare("INSERT INTO pay (`user_id`, `amount`, `status`, `product`, `ref_id`) VALUES(?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $amount, $status, $product, $refId]);
        $pay_id = $pdo->lastInsertId();
        $msg_success .= "<br>شماره سفارش:" . $pay_id;
    } catch(PDOException $e) {
        echo "error in insert pay data: ".$e->getMessage();
        exit();
    }
    
    // دریافت اطلاعات کاربر از جدول یوزر
    try {
        $sql = "SELECT * FROM user WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result === false) {
            echo '{"status":"error","message":"User not found"}';
            exit();
        }
        $user = $result["first_name"]." ".$result["last_name"];
        $phone = $result["phone"];

    } catch(PDOException $e) {
        echo "error in select user data: ".$e->getMessage();
        exit();
    }
    
    // ارسال پیام به بله
    include "../app/bale/SendMessage.php";
    date_default_timezone_set("Asia/Tehran");
    $DateTime = miladiBeShamsi(date('Y'), date('m'), date('d'), "/") . " " . date('H:i:s');
    
    $msg = "کاربر با نام ".$user." ".$product." را پرداخت کرد.\n".
           "شماره تلفن: ".$phone."\n".
           "کد پیگیری: ".$refId."\n".
           "مبلغ: " . number_format($amount/10) . " تومان\n" ;
          
    if (isset($new_owner_name) && isset($new_owner_phone)){
            $msg .= 
           "کیو آر کد دستگاه: " . $qr_code . "\n" .
           "نام مالک جدید :" . $new_owner_name . "\n".
           "شماره تلفن مالک جدید :" . $new_owner_phone . "\n" ;
    }
    if (isset($user_count)){
        $msg .=
       "تعداد کاربر خریداری شده: " . $user_count . "\n" ;
    }
    
    $msg .= "#buy\n" . $DateTime;
    
    $bale = new SendMessage();
    $bale->send_messagge("6441576316", $msg);
    // ارسال پیامک به کاربر
    $bale->sms_order($phone, $req, $pay_id);
        

    } else {
        file_put_contents("callback_log.txt", date("Y-m-d H:i:s") . " => " . "کاربر پرداخت کرد. خطا در ارسال پیامک/ ثبت سفارش." . "\n", FILE_APPEND);
    }
} else {
    $msg_success = "پرداخت توسط کاربر لغو شد یا ناموفق بود.";
}


// تبدیل تاریخ میلادی به شمسی
function miladiBeShamsi($gy, $gm, $gd, $mod){
    $g_d_m = array(0,31,59,90,120,151,181,212,243,273,304,334);
    if( 1600 < $gy ){
        $jy = 979;
        $gy = (int)$gy - 1600; 
    }else{
        $jy = 0;
        $gy = (int)$gy - 621;
    }
    $gy2 = ($gm > 2)? ($gy + 1): $gy;
    $days = (365 * $gy) + ((int)(($gy2 + 3) / 4)) - ((int)(($gy2 + 99) / 100)) + ((int)(($gy2 + 399) / 400)) - 80 + $gd + $g_d_m[$gm - 1];
    $jy += 33 * ((int)($days / 12053));
    $days %= 12053;
    $jy += 4 * ((int)($days / 1461));
    $days %= 1461;
    $jy += (int)(($days - 1) / 365);
    if($days > 365)
        $days = ($days - 1) % 365;
    if($days < 186){
        $jm = 1 + (int)($days / 31);
        $jd = 1 + ($days % 31);
    }else{
        $jm = 7 + (int)(($days - 186) / 30);
        $jd = 1 + (($days - 186) % 30);
    }
    return $jy . $mod . $jm . $mod . $jd;
}
?>
<!doctype html>
<html lang="fa">
    <head>
        <meta charset="utf-8">
        <title>hoomplus</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0 ">
        <link rel="stylesheet" href="../css/base.css">
        <link rel="stylesheet" href="../css/index.css">
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <style>
            body{
                text-align:center;
            }
            a{
                text-align:center;
                text-decoration:none;
                display:block;
                margin:30px auto;
                width:70%;
                color:white;
                background-color:green;
                cursor:pointer;
                border-radius:15px;
                border:1px solid gray;
                padding:10px;
            }
        </style>
    </head>
    <body>
        <div class="main">
            <p class="text-light fs-3 my-3">
                <?php echo $msg_success;?>
            </p>
            <a type="button" href="../index.php">
                بازگشت به صفحه اصلی 
            </a>
        </div>
    </body>
</html>
