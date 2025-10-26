<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['export_data'])) {
    // گرفتن داده و تبدیل از JSON به آرایه PHP
    $data = json_decode($_POST['export_data'], true);

    // ارسال هدرهای لازم برای خروجی اکسل (CSV با BOM UTF-8)
    header('Content-Type: text/csv; charset=UTF-8');
    header('Content-Disposition: attachment; filename="export.csv"');
    echo "\xEF\xBB\xBF"; // BOM برای UTF-8

    $output = fopen("php://output", "w");

    // --- اطلاعات کلّی دستگاه ---
    $groupName = $data['action_groups'][0]['action_group_name'] ?? 'بدون نام';
    $model = $data['model_name'] ?? 'نامشخص';
    $qr = $data['unique_qr_code'] ?? 'نامشخص';

    fputcsv($output, ['نام دستگاه :', $groupName]);
    fputcsv($output, ['مدل دستگاه:', $model]);
    fputcsv($output, ['کد QR:', $qr]);
    fputcsv($output, []); // خط خالی برای فاصله

    // --- تیتر جدول کاربران ---
    fputcsv($output, ['نام کاربر', 'شماره تلفن']);

    // --- لیست کاربران ---
    if (!empty($data['action_groups'][0]['access_list']['users'])) {
        foreach ($data['action_groups'][0]['access_list']['users'] as $user) {
            $name = $user['user_info']['name'] ?? 'نامشخص';
            $phone = $user['user_info']['phone_number'] ?? 'نامشخص';

            // جلوگیری از فرمت علمی در اکسل
            if (is_numeric($phone)) {
                $phone = "'" . $phone;
            }

            fputcsv($output, [$name, $phone]);
        }
    } else {
        fputcsv($output, ['هیچ کاربری یافت نشد', '']);
    }

    fclose($output);
    exit;
} else {
    echo "داده‌ای برای خروجی وجود ندارد.";
}
?>
