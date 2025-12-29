<?php

session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_as']) || $_SESSION['role_as'] != 1) {
    header('Location: index.php?msg=permission_denied');
    exit();
}

require_once '../../includes/db.php';

// Lấy ID người dùng
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit();
}

// Không cho phép xóa chính mình
if ($id == $_SESSION['user_id']) {
    header('Location: index.php?msg=cannot_delete_self');
    exit();
}

// Xóa các order_detail liên quan trước
$stmt = $conn->prepare("DELETE FROM order_detail WHERE user_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

// Xóa người dùng
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header('Location: index.php?msg=delete_success');
exit();
