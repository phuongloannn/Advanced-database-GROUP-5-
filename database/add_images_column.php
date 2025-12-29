<?php
include("../config/dbcon.php");

// Thêm cột images vào bảng reviews
$add_images_column = "ALTER TABLE reviews ADD COLUMN IF NOT EXISTS images TEXT DEFAULT NULL";

if(mysqli_query($con, $add_images_column)) {
    echo "Đã thêm cột images vào bảng reviews thành công!";
} else {
    echo "Lỗi khi thêm cột images: " . mysqli_error($con);
}

// Thêm cột shop_reply vào bảng reviews nếu chưa có
$add_shop_reply_column = "ALTER TABLE reviews ADD COLUMN IF NOT EXISTS shop_reply TEXT DEFAULT NULL";

if(mysqli_query($con, $add_shop_reply_column)) {
    echo "Đã thêm cột shop_reply vào bảng reviews thành công!";
} else {
    echo "Lỗi khi thêm cột shop_reply: " . mysqli_error($con);
}
?> 