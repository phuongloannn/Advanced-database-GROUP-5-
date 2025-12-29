<?php
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
    
    // Đọc nội dung file SQL
    $sql = file_get_contents('fashion_shop_group5.sql');
    
    // Thực thi từng câu lệnh SQL
    if ($conn->multi_query($sql)) {
        do {
            // Lưu trữ kết quả đầu tiên
            if ($result = $conn->store_result()) {
                $result->free();
            }
            // Chuyển sang kết quả tiếp theo
        } while ($conn->more_results() && $conn->next_result());
        
        echo "Đã import database thành công!<br>";
        echo "Bạn có thể đăng nhập với tài khoản:<br>";
        echo "Email: admin@qfashion.com<br>";
        echo "Mật khẩu: password<br>";
    } else {
        throw new Exception("Lỗi khi import database: " . $conn->error);
    }
    
} catch (Exception $e) {
    die("Lỗi: " . $e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
} 