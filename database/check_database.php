<?php
$host = 'localhost';
$user = 'root';
$pass = '';

// Tạo kết nối không có database
$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("Kết nối thất bại: " . $conn->connect_error);
}

// Tạo database nếu chưa tồn tại
$sql = "CREATE DATABASE IF NOT EXISTS fashion_shop CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if ($conn->query($sql)) {
    echo "Database fashion_shop_group5 đã sẵn sàng.<br>";
} else {
    echo "Lỗi khi tạo database: " . $conn->error . "<br>";
}

// Chọn database
$conn->select_db('fashion_shop_group5');

// Kiểm tra bảng users
$check_table = $conn->query("SHOW TABLES LIKE 'users'");
if ($check_table->num_rows > 0) {
    echo "Bảng users đã tồn tại.<br>";
    
    // Kiểm tra dữ liệu trong bảng
    $result = $conn->query("SELECT * FROM users");
    echo "Số lượng user trong bảng: " . $result->num_rows . "<br>";
} else {
    echo "Bảng users chưa tồn tại.<br>";
}

$conn->close(); 