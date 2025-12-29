<?php
include('../config/dbcon.php');

// Xóa bảng cũ nếu tồn tại
$drop_table = "DROP TABLE IF EXISTS reviews";
mysqli_query($con, $drop_table);

// Tạo bảng reviews mới
$create_table = "CREATE TABLE reviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    product_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if(mysqli_query($con, $create_table)) {
    echo "Tạo bảng reviews thành công!<br>";
} else {
    echo "Lỗi khi tạo bảng reviews: " . mysqli_error($con) . "<br>";
}

// Thêm cột rating vào bảng products nếu chưa có
$check_column = "SHOW COLUMNS FROM products LIKE 'rating'";
$result = mysqli_query($con, $check_column);

if(mysqli_num_rows($result) == 0) {
    $add_column = "ALTER TABLE products ADD COLUMN rating DECIMAL(3,2) DEFAULT 0";
    if(mysqli_query($con, $add_column)) {
        echo "Thêm cột rating vào bảng products thành công!<br>";
    } else {
        echo "Lỗi khi thêm cột rating: " . mysqli_error($con) . "<br>";
    }
} else {
    echo "Cột rating đã tồn tại trong bảng products<br>";
}

// Cập nhật rating cho tất cả sản phẩm
$update_ratings = "UPDATE products p 
                   SET rating = COALESCE(
                       (SELECT AVG(rating) 
                        FROM reviews 
                        WHERE product_id = p.id
                       ), 0)";
mysqli_query($con, $update_ratings);
echo "Đã cập nhật rating cho tất cả sản phẩm<br>";

echo "Hoàn tất thiết lập!";
?> 