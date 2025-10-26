<?php

require_once 'app/auth_check.php';
require_once 'app/controllers/ProductController.php';

$ctrl = new ProductController();
$products = $ctrl->get_products_by_category('transfer');


if($_SERVER["REQUEST_METHOD"] == "POST"){
    $name = $_POST['name'] ?? "";
    $phone = $_POST['phone'] ?? "";
    $info  = $_POST['info'];
    $amount = $_POST['amount'];
    $qr = $_POST['qr'] ?? "";
    
    if(!empty($name) && !empty($phone)){
        if(preg_match('/^09\d{9}$/', $phone)){
            $info .= "_" . $phone . $name . "_" . $qr;

            echo '
            <form id="redirectForm" method="POST" action="payment/Request.php">
                <input type="hidden" name="info" value="'.$info.'">
                <input type="hidden" name="amount" value="'.$amount.'">
            </form>
            <script>
                document.getElementById("redirectForm").submit();
            </script>
            ';
            exit;
        } else {
            echo '<p class="text-red-500 text-center my-2">شماره تلفن نامعتبر است.</p>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>انتقال مالکیت</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/units.css">
    <link rel="stylesheet" href="css/index.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex flex-col items-center">

    <?php require_once 'header.php'; ?>

    <!-- هدر با استایل قبلی حفظ شد -->
    <div class="header-container flex items-center justify-between w-full max-w-md mt-6 px-2">
        <span class="material-icons cursor-pointer text-3xl"
            onclick="let sidebar = document.querySelector('.menu'); sidebar.classList.toggle('active'); this.classList.toggle('fa-spin');">
            menu
        </span>
        <h1 class="text-center flex-1 text-xl font-bold">درخواست انتقال مالکیت</h1>
    </div>

    <!-- فرم انتقال مالکیت با Tailwind -->
    <div class="w-full max-w-md mt-6 bg-white rounded-lg shadow p-6">

        <div class="mb-6">
            <p class="text-gray-700 mb-1">هزینه انتقال مالکیت 200 هزار تومان می باشد.</p>
            <p class="text-gray-600 text-sm">پس از پرداخت هزینه، درخواست شما ثبت خواهد شد.</p>
        </div>

        <form method="POST" class="flex flex-col gap-4">
            
            <div>
                <label for="qr" class="block mb-1 text-gray-700 font-medium">کد کیو آر دستگاه :</label>
                <input type="text" name="qr" required 
                    class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <div>
                <label for="name" class="block mb-1 text-gray-700 font-medium">نام مالک جدید :</label>
                <input type="text" name="name" required
                    class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label for="phone" class="block mb-1 text-gray-700 font-medium">شماره تلفن مالک جدید :</label>
                <input type="text" name="phone" required placeholder="مثال: 09123456789"
                    class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            
            
            <input type="hidden" name="amount" value="<?= $products[0]['price'] ?>">
            <input type="hidden" name="info" value="<?= htmlspecialchars($products[0]['id'] . $_SESSION['user_info']['id']) ?>">

            <button type="submit"
                class="w-full bg-teal-600 text-white py-2 rounded hover:bg-blue-700 transition-colors">
                پرداخت
            </button>
        </form>
    </div>

</body>
</html>
