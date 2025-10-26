<?php

ini_set('session.use_strict_mode', 1);
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid', 0);
ini_set('session.gc_maxlifetime', 86400); 

session_set_cookie_params([
    'lifetime' => 86400, 
    'domain' => $_SERVER['HTTP_HOST'],
    'path' => '/',
    'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
    'httponly' => true
]);

session_start();

if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if(!isset($_SESSION['last_generation'])){
    regenerate_id_session();
}else{
    $interval = 60 * 40;
    if(time() - $_SESSION['last_generation'] >= $interval){
        regenerate_id_session();
    }
}

function regenerate_id_session(){
    session_regenerate_id(false);
    $_SESSION['last_generation'] = time();
}
