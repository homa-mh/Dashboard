<?php
require_once 'app/auth_check.php';
require_once 'app/controllers/ProductController.php';

$ctrl = new ProductController();
$repairProducts = $ctrl->get_products_by_category('user');
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>خرید کاربر</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/units.css">
    <link rel="stylesheet" href="css/index.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800">
<div class="main container mx-auto p-4">

    <?php require_once 'header.php'; ?>

    <div class="flex items-center justify-between mb-6">
        <span class="material-icons" onclick="let sidebar =
            document.querySelector('.menu'); sidebar.classList.toggle('active');
            this.classList.toggle('fa-spin');" style="cursor: pointer;font-size:40px;">menu</span>
        <h1 class="text-2xl font-bold" style="margin:auto;">خرید کاربر</h1>
    </div>
    <p class="text-center mb-6">پس از پرداخت هزینه درخواست شما ثبت خواهد شد.</p>

    <div class="flex justify-center">
    <?php foreach ($repairProducts as $product): ?>
        <div class="bg-white shadow rounded-2xl p-6 flex flex-col items-center text-center w-80">
            <!-- عنوان -->
            <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($product['title']) ?></h2>
            
            <!-- قیمت -->
            <p class="text-gray-600 mb-4"><?= number_format($product['price']/10) ?> تومان (هر کاربر)</p>

            <!-- فرم -->
            <form action="https://hoomplus.ir/hoomPlus/payment/Request.php" method="POST" class="w-full flex flex-col items-center gap-4">
                
                <!-- تعداد -->
                <div class="counter flex items-center gap-3">
                    <button type="button" onclick="changeCount(this, -1, <?= $product['price'] ?>)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded-full text-lg">-</button>
                    <input type="text" name="count" value="1" readonly class="w-12 text-center border rounded-lg py-1">
                    <button type="button" onclick="changeCount(this, 1, <?= $product['price'] ?>)" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded-full text-lg">+</button>
                </div>

                <!-- Hidden Inputs -->
                <input type="hidden" name="amount" value="<?= $product['price'] ?>">
                <input type="hidden" name="info" value="<?= htmlspecialchars($product['id'] . $_SESSION['user_info']['id'] . '_1') ?>">

                <!-- دکمه پرداخت -->
                <button type="submit" class="bg-teal-600 hover:bg-teal-500 text-white py-2 px-6 rounded-lg transition">
                    پرداخت
                </button>
            </form>
        </div>
    <?php endforeach; ?>
</div>


</div>

<script>
function changeCount(btn, delta, basePrice) {
    let form = btn.closest("form");
    let input = form.querySelector("input[name='count']");
    let amountInput = form.querySelector("input[name='amount']");
    let infoInput = form.querySelector("input[name='info']");

    let count = parseInt(input.value) + delta;
    if (count < 1) count = 1;

    input.value = count;
    amountInput.value = basePrice * count;

    let infoParts = infoInput.value.split("_");
    infoInput.value = infoParts[0] + "_" + infoParts[1] + "_" + count;
}
</script>

</body>
</html>
