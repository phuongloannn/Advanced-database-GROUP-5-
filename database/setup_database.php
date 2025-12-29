<?php
include("../config/dbcon.php");

// Đọc nội dung file SQL
$sql = file_get_contents('fashion_shop.sql');

// Tách các câu lệnh SQL
$queries = explode(';', $sql);

// Thực thi từng câu lệnh
foreach($queries as $query) {
    $query = trim($query);
    if(!empty($query)) {
        if(mysqli_query($con, $query)) {
            echo "Thực thi thành công: " . substr($query, 0, 50) . "...<br>";
        } else {
            echo "Lỗi khi thực thi: " . substr($query, 0, 50) . "...<br>";
            echo "Chi tiết lỗi: " . mysqli_error($con) . "<br>";
        }
    }
}

// Kiểm tra các bảng và cột đã được tạo
$check_tables = [
    'reviews' => [
        'id' => 'INT',
        'user_id' => 'INT',
        'product_id' => 'INT',
        'order_id' => 'INT',
        'rating' => 'INT',
        'comment' => 'TEXT',
        'created_at' => 'TIMESTAMP'
    ],
    'products' => [
        'rating' => 'FLOAT'
    ],
    'orders' => [
        'status' => "ENUM('pending','processing','completed','cancelled')"
    ],
    'order_detail' => [
        'status' => "ENUM('1','2','3','4')"
    ]
];

foreach($check_tables as $table => $columns) {
    $result = mysqli_query($con, "SHOW TABLES LIKE '$table'");
    if(mysqli_num_rows($result) > 0) {
        echo "<br>Bảng '$table' tồn tại.<br>";
        
        // Kiểm tra các cột
        $cols = mysqli_query($con, "SHOW COLUMNS FROM $table");
        $existing_columns = [];
        while($col = mysqli_fetch_assoc($cols)) {
            $existing_columns[$col['Field']] = $col['Type'];
        }
        
        foreach($columns as $column => $type) {
            if(isset($existing_columns[$column])) {
                echo "- Cột '$column' tồn tại với kiểu: " . $existing_columns[$column] . "<br>";
            } else {
                echo "- Cột '$column' không tồn tại!<br>";
            }
        }
    } else {
        echo "<br>Bảng '$table' không tồn tại!<br>";
    }
}

// Kiểm tra trigger
$check_trigger = mysqli_query($con, "SHOW TRIGGERS LIKE 'update_product_rating_after_review'");
if(mysqli_num_rows($check_trigger) > 0) {
    echo "<br>Trigger 'update_product_rating_after_review' đã được tạo.<br>";
} else {
    echo "<br>Trigger 'update_product_rating_after_review' chưa được tạo!<br>";
}

echo "<br>Hoàn tất quá trình cài đặt database!";

$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Tạo kết nối MySQL không có database
    $conn = new mysqli($host, $user, $pass);
    
    if ($conn->connect_error) {
        throw new Exception("Kết nối thất bại: " . $conn->connect_error);
    }
    
    echo "Kết nối MySQL thành công.<br>";
    
    // Tạo database nếu chưa tồn tại
    $sql = "CREATE DATABASE IF NOT EXISTS fashion_shop_group5 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
    if ($conn->query($sql)) {
        echo "Database fashion_shop đã được tạo hoặc đã tồn tại.<br>";
    } else {
        throw new Exception("Lỗi khi tạo database: " . $conn->error);
    }
    
    // Chọn database
    $conn->select_db('fashion_shop');
    
    // Xóa bảng users nếu tồn tại
    $sql = "DROP TABLE IF EXISTS users";
    if ($conn->query($sql)) {
        echo "Đã xóa bảng users cũ (nếu có).<br>";
    }
    
    // Tạo bảng users
    $sql = "CREATE TABLE users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        role_as TINYINT NOT NULL DEFAULT 0 COMMENT '0=user, 1=admin, 2=staff',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    if ($conn->query($sql)) {
        echo "Bảng users đã được tạo thành công.<br>";
        
        // Thêm tài khoản admin
        $admin_password = password_hash('password', PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, password, role_as) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $name = "Admin";
        $email = "admin@qfashion.com";
        $role = 1;
        
        $stmt->bind_param("sssi", $name, $email, $admin_password, $role);
        
        if ($stmt->execute()) {
            echo "Tài khoản admin đã được tạo:<br>";
            echo "Email: admin@qfashion.com<br>";
            echo "Mật khẩu: password<br>";
        } else {
            throw new Exception("Lỗi khi thêm tài khoản admin: " . $stmt->error);
        }
        
        // Kiểm tra dữ liệu
        $result = $conn->query("SELECT * FROM users");
        echo "Số lượng user trong bảng: " . $result->num_rows . "<br>";
        
    } else {
        throw new Exception("Lỗi khi tạo bảng users: " . $conn->error);
    }
    
} catch (Exception $e) {
    die("Lỗi: " . $e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 