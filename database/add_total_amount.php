<?php
include('../config/dbcon.php');

// Add total_amount column to orders table if not exists
$alter_table = "ALTER TABLE `orders` 
                ADD COLUMN IF NOT EXISTS `total_amount` decimal(10,2) NOT NULL DEFAULT 0.00";

if(mysqli_query($con, $alter_table)) {
    echo "Đã thêm cột total_amount vào bảng orders thành công!";
} else {
    echo "Lỗi khi thêm cột: " . mysqli_error($con);
}
?> 