<?php
require_once 'app/session_config.php';

session_unset();
session_destroy();

header("Location: login.php");
exit();
