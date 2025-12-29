<?php
include('../config/dbcon.php');

// Kiểm tra xem cột created_at đã tồn tại chưa
$checkColumn = "SELECT COLUMN_NAME 
               FROM INFORMATION_SCHEMA.COLUMNS 
               WHERE TABLE_SCHEMA = 'fashion_shop_group5' 
               AND TABLE_NAME = 'users' 
               AND COLUMN_NAME = 'created_at'";

$result = mysqli_query($con, $checkColumn);

if (mysqli_num_rows($result) == 0) {
    // Thêm cột created_at nếu chưa tồn tại
    $addColumn = "ALTER TABLE users ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP";
    if (mysqli_query($con, $addColumn)) {
        echo "Đã thêm cột created_at thành công\n";
    } else {
        echo "Lỗi khi thêm cột: " . mysqli_error($con) . "\n";
    }
}

// Cập nhật created_at cho các user hiện tại nếu là NULL
$updateUsers = "UPDATE users SET created_at = CURRENT_TIMESTAMP WHERE created_at IS NULL";
if (mysqli_query($con, $updateUsers)) {
    echo "Đã cập nhật created_at cho các user thành công\n";
} else {
    echo "Lỗi khi cập nhật users: " . mysqli_error($con) . "\n";
}

mysqli_close($con);
?> 