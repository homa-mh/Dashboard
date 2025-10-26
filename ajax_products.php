<?php
ob_start(); // شروع بافر خروجی
error_reporting(0); // خاموش کردن هشدارها
header("Content-Type: application/json; charset=utf-8");

require_once 'app/controllers/ProductController.php';
$response = ["success" => false];
$ctrl = new ProductController();

try {
    $action = $_POST['action'] ?? '';

    if ($action === "add") {
        $title = trim($_POST['title'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $category = trim($_POST['category'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $imagePath = '';

        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/product_images/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

            $filename = time() . '_' . basename($_FILES['image']['name']);
            $targetFile = $uploadDir . $filename;

            if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                throw new Exception("خطا در آپلود تصویر");
            }
            $imagePath = 'product_images/' . $filename;
        }

        $ctrl->add_product($title, $price, $category, $description, $imagePath);
        $response["success"] = true;

    } elseif ($action === "update") {
        $ctrl->update_product((int)($_POST['id'] ?? 0), $_POST['title'], $_POST['price'], $_POST['description']);
        $response["success"] = true;

    } elseif ($action === "delete") {
        $ctrl->delete_product((int)($_POST['id'] ?? 0));
        $response["success"] = true;

    } else {
        $response["error"] = "Action نامعتبر است.";
    }

} catch (Throwable $e) {
    $response["error"] = $e->getMessage();
}

// پاک کردن هر خروجی اضافی قبل از ارسال JSON
ob_clean();
echo json_encode($response);
exit;
