<?php

if($_SERVER['REQUEST_METHOD'] !== "POST"){
    header("Location: login.php");
    die();
}

require_once 'app/controllers/LoginController.php';

$user_name = trim($_POST['username']) ?? null;
$pwd = $_POST['password'] ?? null;
$action = $_POST['action'];

$controller = new LoginController();



if($action == "send_code"){
    $send_code = $controller->send_code($user_name);
}
if($action == "login"){
    $code_check = $controller->code_check($user_name, $pwd);
    if($code_check){
        $_SESSION['user_id'] = $user_name;
        unset($_SESSION['pre_value']);
        $controller->set_sessions($user_name);
        header("Location: index.php");
        die();
    }
    // unset($_SESSION['pre_value']);
    // $success = $controller->set_sessions($user_name);

    // if ($success) {
    //     $_SESSION['user_id'] = $user_name;
    //     header("Location: index.php");
    //     die();
    // } 
    // else {
    //     header("Location: login.php");
    //     die();
    // }
    
}

header("Location: login.php");
die();

