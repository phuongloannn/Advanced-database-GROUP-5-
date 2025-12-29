<?php

require '../config/dbcon.php';

$sql = "CREATE TABLE IF NOT EXISTS `carts` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `prod_id` int(11) NOT NULL,
    `prod_qty` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `prod_id` (`prod_id`),
    CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `cart_ibfk_2` FOREIGN KEY (`prod_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";

if (mysqli_query($con, $sql)) {
    echo "Bảng carts đã được tạo thành công!";
} else {
    echo "Lỗi khi tạo bảng carts: " . mysqli_error($con);
}

mysqli_close($con);
?> 