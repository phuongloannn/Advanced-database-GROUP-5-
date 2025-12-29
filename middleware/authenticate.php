<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['auth']))
{
    $_SESSION['message'] = "Vui lòng đăng nhập để tiếp tục";
    header('Location: auth/login.php');
    exit();
}
?> 