<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



require_once 'app/session_config.php';


if ($_SERVER['REQUEST_METHOD'] != "POST" || 
    !isset($_POST['csrf_token']) || 
    $_POST['csrf_token'] != $_SESSION['csrf_token']) {

    header("Location: factor.php?success=دسترسی غیر مجاز");
    exit;
}

require_once 'app/controllers/FactorController.php';

$user_id = $_SESSION['user_info']['id'];

$controller = new FactorController();

if ($_POST['addFactor'] === 'add') {
    $controller->add($_POST['addFactor'] , $_POST, $user_id);
} 
elseif ($_POST['addFactor'] === 'update' && isset($_POST['id'])) {
    $controller->update($_POST['addFactor'] , $_POST['id'], $user_id);
}
elseif ($_POST['addFactor'] === 'delete' && isset($_POST['id'])) {
    $controller->delete($_POST['id'], $user_id);
}
