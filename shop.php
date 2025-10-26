<?php 
require_once 'app/auth_check.php';
require_once 'app/controllers/ProductController.php';

$ctrl = new ProductController();
$repairProducts = $ctrl->get_products_by_category('user_feedback');

// اطلاعات کاربر از سشن
$user_name  = ($_SESSION['user_info']['first_name'] ?? '') . ' ' . ($_SESSION['user_info']['last_name'] ?? '');
$user_phone = $_SESSION['user_info']['phone'] ?? 'نامشخص';
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>محصولات کاربران</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/base.css">
    <link rel="stylesheet" href="css/units.css">
    <link rel="stylesheet" href="css/index.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script></head>
<body class="bg-gray-100 ">
<div class="main container mx-auto p-4">

    <?php require_once 'header.php'; ?>

    <div class="flex items-center justify-between mb-6">
        <span class="material-icons"  onclick="let sidebar =
            document.querySelector('.menu'); sidebar.classList.toggle('active');
            this.classList.toggle('fa-spin');" style="cursor: pointer;font-size:40px;">menu</span>
        <h1 class="text-2xl font-bold" style="margin:auto;">لیست محصولات</h1>
    </div>
    <div class="max-w-6xl mx-auto py-8 px-4">

        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3 justify-items-center">
            <?php foreach ($repairProducts as $product): ?>
                <div class="bg-white rounded-2xl shadow-md overflow-hidden hover:shadow-xl transition duration-300 w-72">
                    
                    <!-- تصویر محصول مربع و وسط‌چین -->
                    <?php if (!empty($product['image'])): ?>
                        <div class="w-40 h-40 mx-auto mt-4">
                            <img src="<?= htmlspecialchars($product['image']) ?>" alt="img" class="w-full h-full object-cover rounded-lg">
                        </div>
                    <?php else: ?>
                        <div class="w-40 h-40 bg-gray-200 flex items-center justify-center text-gray-400 rounded-lg mx-auto mt-4">
                            بدون تصویر
                        </div>
                    <?php endif; ?>
        
                    <!-- محتوای کارت -->
                    <div class="p-4 flex flex-col justify-between h-48 text-center">
                        <div>
                            <h2 class="text-lg font-semibold mb-2"><?= htmlspecialchars($product['title']) ?></h2>
                            <p class="text-sm text-gray-600 mb-4"><?= htmlspecialchars($product['description']) ?></p>
                        </div>
                        <button 
                            onclick="sendRequest('<?= htmlspecialchars($product['title']) ?>')" 
                            class="w-full bg-green-700 hover:bg-green-600 text-white font-semibold py-2 px-4 rounded-xl transition">
                            ارسال درخواست
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    
    </div>
</div>
<script>
function sendRequest(productName) {
    fetch('shop_send_request.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ product: productName })
    })
    .then(res => res.json())
    .then(data => {
        alert(data.message);
    })
    .catch(err => {
        alert('❌ خطا در ارسال درخواست.');
        console.error(err);
    });
}
</script>

</body>
</html>
