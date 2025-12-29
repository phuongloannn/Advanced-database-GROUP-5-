<?php
include("../config/dbcon.php");

// Đọc nội dung file SQL
$sql = file_get_contents('update_reviews_table.sql');

// Tách các câu lệnh SQL
$queries = array_filter(array_map('trim', explode(';', $sql)));

// Thực thi từng câu lệnh
$success = true;
foreach ($queries as $query) {
    if (!empty($query)) {
        if (!mysqli_query($con, $query)) {
            echo "Lỗi khi thực thi câu lệnh: " . $query . "<br>";
            echo "Chi tiết lỗi: " . mysqli_error($con) . "<br><br>";
            $success = false;
        }
    }
}

if ($success) {
    echo "Đã cập nhật cấu trúc bảng reviews thành công!";
} else {
    echo "Có lỗi xảy ra trong quá trình cập nhật cấu trúc bảng.";
}
?> 