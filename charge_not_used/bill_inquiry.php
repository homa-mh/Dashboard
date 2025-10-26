<?php
require_once 'app/session_config.php';

if ($_SERVER['REQUEST_METHOD'] != "POST" || 
    !isset($_POST['csrf_token']) || 
    $_POST['csrf_token'] != $_SESSION['csrf_token']) {

    header("Location: bill.php?error=دسترسی غیر مجاز");
    exit;
}


require_once 'app/controllers/BillController.php';

$controller = new BillController();
$controller->store();

