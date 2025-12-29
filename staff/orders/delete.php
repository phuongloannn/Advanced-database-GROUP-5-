<?php

session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_as']) || $_SESSION['role_as'] != 2) {
    header('Location: ../../auth/login.php');
    exit();
}
require_once '../../includes/db.php';

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($order_id > 0) {
    // Xóa chi tiết đơn hàng trước
    $conn->query("DELETE FROM order_detail WHERE order_id = $order_id");
    // Xóa đơn hàng
    $conn->query("DELETE FROM orders WHERE id = $order_id");
}
header('Location: index.php?msg=deleted');
exit();