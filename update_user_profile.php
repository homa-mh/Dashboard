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

require_once 'app/controllers/ProfileController.php';

$user_id = $_SESSION['user_info']['id'];

$controller = new ProfileController();

if ($_POST['submitUser'] === 'update') {
    $controller->update( $_POST, $user_id);
} 

