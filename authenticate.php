<?php
session_start();

if (!isset($_SESSION['auth_user'])) {
    $_SESSION['message'] = "Vui lòng đăng nhập để tiếp tục";
    header("Location: login.php");
    exit();
}
?> 