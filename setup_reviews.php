<?php
include('config/dbcon.php');

// Tạo bảng reviews
$create_reviews_table = "
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `user_id` int(11) NOT NULL,
    `product_id` int(11) NOT NULL,
    `order_id` int(11) NOT NULL,
    `rating` int(1) NOT NULL,
    `comment` text NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `user_id` (`user_id`),
    KEY `product_id` (`product_id`),
    KEY `order_id` (`order_id`),
    CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
    CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
    CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

// Thêm cột rating vào bảng products
$add_rating_column = "
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `rating` DECIMAL(3,2) DEFAULT 0.00;
";

if (mysqli_query($con, $create_reviews_table)) {
    echo "Tạo bảng reviews thành công<br>";
} else {
    echo "Lỗi khi tạo bảng reviews: " . mysqli_error($con) . "<br>";
}

if (mysqli_query($con, $add_rating_column)) {
    echo "Thêm cột rating vào bảng products thành công<br>";
} else {
    echo "Lỗi khi thêm cột rating: " . mysqli_error($con) . "<br>";
}
?> 