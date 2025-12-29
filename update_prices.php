<?php
require_once 'includes/db.php';

// Update all product prices
$sql = "UPDATE products SET original_price = original_price * 10000, selling_price = selling_price * 10000";
if ($conn->query($sql)) {
    echo "Đã cập nhật giá thành công!";
} else {
    echo "Lỗi khi cập nhật giá: " . $conn->error;
}

// Update order details prices
$sql = "UPDATE order_detail SET selling_price = selling_price * 10000";
if ($conn->query($sql)) {
    echo "\nĐã cập nhật giá đơn hàng thành công!";
} else {
    echo "\nLỗi khi cập nhật giá đơn hàng: " . $conn->error;
}

$conn->close();
?> 