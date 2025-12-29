<?php
include('../config/dbcon.php');

try {
    // Đọc nội dung file SQL
    $sql = file_get_contents('update_categories.sql');
    
    // Tách các câu lệnh SQL
    $queries = array_filter(array_map('trim', explode(';', $sql)));
    
    // Thực thi từng câu lệnh
    foreach($queries as $query) {
        if(empty($query)) continue;
        
        if(mysqli_query($con, $query)) {
            // Nếu là câu lệnh SELECT, hiển thị kết quả
            if(stripos($query, 'SELECT') === 0) {
                $result = mysqli_query($con, $query);
                echo "<h3>Thống kê số lượng sản phẩm theo danh mục:</h3>";
                while($row = mysqli_fetch_assoc($result)) {
                    echo "{$row['name']}: {$row['count']} sản phẩm<br>";
                }
            } else {
                echo "Thực thi thành công: " . substr($query, 0, 50) . "...<br>";
            }
        } else {
            throw new Exception("Lỗi khi thực thi câu lệnh: " . $query . "\nLỗi: " . mysqli_error($con));
        }
    }
    
    echo "<br>Đã cập nhật danh mục thành công!";

} catch(Exception $e) {
    // Đảm bảo bật lại foreign key checks trong trường hợp có lỗi
    mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");
    echo "<div style='color: red; font-weight: bold;'>Lỗi: " . $e->getMessage() . "</div>";
}
?> 