<?php
require_once 'app/Dbh.php';

$db = new Dbh();
$conn = $db->connect();

// گرفتن شماره تلفن از فرم (اگه پر شده باشه)
$phoneFilter = $_GET['phone'] ?? '';

if (!empty($phoneFilter)) {
    $stmt = $conn->prepare("SELECT * FROM login_logs WHERE phone LIKE ? ORDER BY time DESC");
    $stmt->execute(["%$phoneFilter%"]);
} else {
    $stmt = $conn->prepare("SELECT * FROM login_logs ORDER BY time DESC");
    $stmt->execute();
}

$visits = $stmt->fetchAll(PDO::FETCH_ASSOC);
$totalVisits = count($visits);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بازدیدهای سایت</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <div class="max-w-5xl mx-auto p-4">
        <!-- سربرگ -->
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl md:text-2xl font-bold text-gray-700">لیست بازدیدهای سایت</h1>
            <a href="charge.php" class="text-gray-600 hover:text-gray-900">
                <span class="material-symbols-outlined">keyboard_backspace</span>
            </a>
        </div>

        <!-- فرم فیلتر -->
        <form method="get" class="flex flex-col sm:flex-row gap-2 sm:gap-4 mb-4">
            <input 
                type="text" 
                name="phone" 
                placeholder="جستجو بر اساس تلفن..." 
                value="<?= htmlspecialchars($phoneFilter) ?>"
                class="flex-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-teal-400"
            >
            <button type="submit" 
                class="px-4 py-2 bg-teal-500 text-white rounded-lg hover:bg-teal-600">
                فیلتر
            </button>
            <a href="visits.php" 
                class="px-4 py-2 bg-gray-400 text-white rounded-lg hover:bg-gray-500 text-center">
                نمایش همه
            </a>
        </form>

        <!-- تعداد بازدید -->
        <div class="mb-3 font-medium text-gray-700">
            تعداد بازدیدها: <?= $totalVisits ?>
        </div>

        <!-- جدول -->
        <div class="overflow-x-auto bg-white shadow-md rounded-lg">
            <table class="min-w-full text-sm text-center">
                <thead class="bg-teal-500 text-white sticky top-0">
                    <tr>
                        <th class="py-3 px-4">ردیف</th>
                        <th class="py-3 px-4">تلفن همراه</th>
                        <th class="py-3 px-4">زمان بازدید</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($totalVisits > 0): ?>
                        <?php foreach ($visits as $index => $row): ?>
                            <tr class="border-b hover:bg-gray-100">
                                <td class="py-2 px-4"><?= $index + 1 ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($row['phone']) ?></td>
                                <td class="py-2 px-4"><?= htmlspecialchars($row['time']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="py-3 text-gray-500">هیچ رکوردی یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- آیکون‌ها -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
</body>
</html>
