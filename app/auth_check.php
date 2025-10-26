<?php
require_once 'session_config.php';

if (!isset($_SESSION['user_info'])) {
    header("Location: login.php");
    exit();
}
