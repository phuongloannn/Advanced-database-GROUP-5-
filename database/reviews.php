<?php
include('../config/dbcon.php');

// Tạo bảng reviews nếu chưa tồn tại
$create_reviews = "CREATE TABLE IF NOT EXISTS reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
)";

if(mysqli_query($con, $create_reviews)) {
    echo "Tạo bảng reviews thành công!";
} else {
    echo "Lỗi: " . mysqli_error($con);
}

// Thêm cột rating vào bảng products nếu chưa có
$add_rating = "ALTER TABLE products ADD COLUMN IF NOT EXISTS rating DECIMAL(3,2) DEFAULT 0";
mysqli_query($con, $add_rating);
?> 