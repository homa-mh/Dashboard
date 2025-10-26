<?php
require_once 'app/auth_check.php';
require_once 'app/controllers/ProductController.php';

$ctrl = new ProductController();
$repairProducts = $ctrl->get_products_by_category('sub');
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>خرید اشتراک</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/units.css">
    <link rel="stylesheet" href="css/index.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="main container mx-auto p-4">

    <?php require_once 'header.php'; ?>

    <div class="flex items-center justify-between mb-6">
        <span class="material-icons cursor-pointer text-4xl"
            onclick="let sidebar = document.querySelector('.menu'); sidebar.classList.toggle('active'); this.classList.toggle('fa-spin');">
            menu
        </span>
        <h1 class="text-2xl font-bold text-center flex-1">خرید اشتراک</h1>
    </div>

    <p class="text-center mb-6">در صورتی که شرایط زیر را داشته باشید، می‌توانید اشتراک خود را انتخاب کنید و به مدت یک سال از خدمات پس از فروش بهره‌مند شوید:</p>

    <!-- کارت های اشتراک -->
    <div class="grid md:grid-cols-2 gap-6 mb-8">

        <!-- نقره‌ای -->
        <?php foreach ($repairProducts as $product): ?>
            <?php if (strpos($product['title'], 'نقره') !== false): ?>
                <div class="bg-gradient-to-br from-gray-200 to-gray-400 shadow rounded-2xl p-6 text-center border border-gray-300">
                    <h2 class="text-xl font-bold text-gray-800 mb-4">اشتراک نقره‌ای</h2>
                    <ul class="text-gray-700 space-y-3 mb-6">
                        <li>✔ قدیمی بودن دستگاه</li>
                        <li>✔ تعداد کاربران کمتر از ۲۰ نفر</li>
                    </ul>
                    <div class="text-2xl font-bold text-gray-900 mb-4">
                        <?= number_format($product['price']/10) ?> تومان
                    </div>
                    <form action="https://hoomplus.ir/hoomPlus/payment/Request.php" method="POST">
                        <input type="hidden" name="amount" value="<?= $product['price'] ?>">
                        <input type="hidden" name="info" value="<?= htmlspecialchars($product['id'] . $_SESSION['user_info']['id']) ?>">
                        <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white py-2 px-6 rounded-lg transition">خرید</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- طلایی -->
        <?php foreach ($repairProducts as $product): ?>
            <?php if (strpos($product['title'], 'طلایی') !== false): ?>
                <div class="bg-gradient-to-br from-yellow-400 to-yellow-600 shadow rounded-2xl p-6 text-center text-white">
                    <h2 class="text-xl font-bold mb-4">اشتراک طلایی</h2>
                    <ul class="space-y-3 mb-6">
                        <li>✔ قدیمی بودن دستگاه</li>
                        <li>✔ تعداد کاربران بالای ۲۰ نفر</li>
                    </ul>
                    <div class="text-2xl font-bold mb-4">
                        <?= number_format($product['price']/10) ?> تومان
                    </div>
                    <form action="https://hoomplus.ir/hoomPlus/payment/Request.php" method="POST">
                        <input type="hidden" name="amount" value="<?= $product['price'] ?>">
                        <input type="hidden" name="info" value="<?= htmlspecialchars($product['id'] . $_SESSION['user_info']['id']) ?>">
                        <button type="submit" class="bg-white text-yellow-700 font-bold py-2 px-6 rounded-lg hover:bg-gray-100 transition">خرید</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

    </div>

</div>
</body>


</html>

