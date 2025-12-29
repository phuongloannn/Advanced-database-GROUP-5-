<?php
include("../config/dbcon.php");

// Tạo bảng reviews
$create_reviews_table = "CREATE TABLE IF NOT EXISTS reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    order_id INT NOT NULL,
    rating INT NOT NULL,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    FOREIGN KEY (order_id) REFERENCES orders(id)
)";

if(mysqli_query($con, $create_reviews_table)) {
    echo "Bảng reviews đã được tạo thành công!";
} else {
    echo "Lỗi khi tạo bảng reviews: " . mysqli_error($con);
}

// Thêm cột rating vào bảng products nếu chưa có
$add_rating_column = "ALTER TABLE products ADD COLUMN IF NOT EXISTS rating FLOAT DEFAULT 0";
if(mysqli_query($con, $add_rating_column)) {
    echo "Đã thêm cột rating vào bảng products!";
} else {
    echo "Lỗi khi thêm cột rating: " . mysqli_error($con);
}
?> 