<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_as']) || $_SESSION['role_as'] != 1) {
    header('Location: ../../auth/login.php');
    exit();
}

require_once '../../includes/db.php';

// Lấy ID đơn hàng
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit();
}

// Xóa chi tiết đơn hàng trước (nếu có)
$stmt = $conn->prepare("DELETE FROM order_detail WHERE order_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Xóa đơn hàng
$stmt = $conn->prepare("DELETE FROM orders WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header('Location: index.php?msg=delete_success');
exit();
