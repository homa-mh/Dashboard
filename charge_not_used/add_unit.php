<?php
require_once 'app/session_config.php';

if ($_SERVER['REQUEST_METHOD'] != "POST" || 
    !isset($_POST['csrf_token']) || 
    $_POST['csrf_token'] != $_SESSION['csrf_token']) {

    header("Location: unit.php?success=دسترسی غیر مجاز");
    exit;
}


require_once 'app/controllers/UnitController.php';

$controller = new UnitController();

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'افزودن واحد':
        $controller->store();
        header("Location: charge.php?success=با موفقیت اضافه شد.");
        break;

    case 'ویرایش واحد':
        $controller->update();
        header("Location: charge.php?success=با موفقیت ویرایش شد.");
        break;

    case 'حذف واحد':
        $controller->delete();
        header("Location: charge.php?success=با موفقیت حذف شد.");
        break;

    default:
        header("Location: charge.php?success=عملیات نامشخص");
        break;
}

exit;

