<?php
include('config/dbcon.php');

$add_trending_column = "ALTER TABLE `products` 
                       ADD COLUMN IF NOT EXISTS `trending` TINYINT(1) DEFAULT 0;";

if (mysqli_query($con, $add_trending_column)) {
    echo "Thêm cột trending vào bảng products thành công<br>";
} else {
    echo "Lỗi khi thêm cột trending: " . mysqli_error($con) . "<br>";
}
?> 