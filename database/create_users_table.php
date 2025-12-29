<?php
require_once '../includes/db.php';

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

echo "Kết nối database thành công.<br>";

// Xóa bảng cũ nếu tồn tại
$conn->query("DROP TABLE IF EXISTS users");
echo "Đã xóa bảng users cũ nếu tồn tại.<br>";

// SQL để tạo bảng users
$create_table_sql = "
CREATE TABLE `users` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `name` varchar(255) NOT NULL,
    `email` varchar(255) NOT NULL,
    `password` varchar(255) NOT NULL,
    `role_as` tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=user, 1=admin, 2=staff',
    `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
";

// Thực thi câu lệnh tạo bảng
if ($conn->query($create_table_sql)) {
    echo "Tạo bảng users thành công!<br>";
    
    // SQL để thêm tài khoản admin
    $insert_admin_sql = "
    INSERT INTO `users` (`name`, `email`, `password`, `role_as`) 
    VALUES ('Admin', 'admin@qfashion.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1)
    ";
    
    // Thêm tài khoản admin
    if ($conn->query($insert_admin_sql)) {
        echo "Thêm tài khoản admin thành công!<br>";
        echo "Email: admin@qfashion.com<br>";
        echo "Mật khẩu: password<br>";
        
        // Kiểm tra dữ liệu đã thêm
        $result = $conn->query("SELECT * FROM users");
        echo "Số lượng user trong bảng: " . $result->num_rows . "<br>";
    } else {
        echo "Lỗi khi thêm tài khoản admin: " . $conn->error . "<br>";
    }
} else {
    echo "Lỗi khi tạo bảng users: " . $conn->error . "<br>";
}

$conn->close(); 