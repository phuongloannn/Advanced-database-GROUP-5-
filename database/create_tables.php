<?php
require_once '../includes/db.php';

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

    echo "Tạo bảng thành công!";
} else {
    echo "Lỗi khi tạo bảng: " . $conn->error;
}

$conn->close(); 