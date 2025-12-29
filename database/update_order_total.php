<?php
include('../config/dbcon.php');

// First, check if total_amount exists and drop it
$check_column = "SHOW COLUMNS FROM `orders` LIKE 'total_amount'";
$result = mysqli_query($con, $check_column);
if(mysqli_num_rows($result) > 0) {
    $drop_column = "ALTER TABLE `orders` DROP COLUMN `total_amount`";
    mysqli_query($con, $drop_column);
}

// Then add total_price column
$alter_table = "ALTER TABLE `orders` 
                ADD COLUMN IF NOT EXISTS `total_price` decimal(10,2) NOT NULL DEFAULT 0.00";

if(mysqli_query($con, $alter_table)) {
    echo "Đã thêm cột total_price vào bảng orders thành công!";
} else {
    echo "Lỗi khi thêm cột: " . mysqli_error($con);
}
?> 