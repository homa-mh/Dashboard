<?php
require_once 'app/auth_check.php';
require_once 'app/controllers/ProductController.php';

$ctrl = new ProductController();
$repairProducts = $ctrl->get_products_by_category('install');
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>درخواست نصاب</title>
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
        <span class="material-icons"  onclick="let sidebar =
            document.querySelector('.menu'); sidebar.classList.toggle('active');
            this.classList.toggle('fa-spin');" style="cursor: pointer;font-size:40px;">menu</span>
        <h1 class="text-2xl font-bold" style="margin:auto;">درخواست نصاب</h1>
    </div>
    <p class="text-center mb-4">پس از پرداخت هزینه درخواست شما ثبت خواهد شد.</p>
    <p class="text-center mb-6">توجه داشته باشید که هزینه ایاب ذهاب با خود مشتری می باشد.</p>

    <div class="overflow-x-auto">
        <table class="min-w-full bg-white rounded shadow">
            <!--<thead class="bg-teal-600 text-white">-->
            <!--    <tr>-->
            <!--        <th class="py-2 px-4 text-center">نام سرویس</th>-->
            <!--        <th class="py-2 px-4 text-center">قیمت</th>-->
            <!--        <th class="py-2 px-4 text-center">عملیات</th>-->
            <!--    </tr>-->
            <!--</thead>-->
            <tbody>
                <?php foreach ($repairProducts as $product): ?>
                    <tr class="border-b text-center">
                        <td class="py-2 px-4"><?= htmlspecialchars($product['title']) ?></td>
                        <td class="py-2 px-4"><?= number_format($product['price']/10) ?> تومان</td>
                        <td class="py-2 px-4">
                            <form action="https://hoomplus.ir/hoomPlus/payment/Request.php" method="POST">
                                <input type="hidden" name="amount" value="<?= $product['price'] ?>">
                                <input type="hidden" name="info" value="<?= htmlspecialchars($product['id'] . $_SESSION['user_info']['id']) ?>">
                                <button type="submit" class="bg-teal-600 hover:bg-teal-500 text-white py-2 px-4 rounded transition">پرداخت</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

</div>
</body>
</html>
