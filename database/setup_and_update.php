<?php
// Kết nối đến MySQL (không chọn database)
$con = mysqli_connect("localhost", "root", "");

if (!$con) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}

try {
    echo "<h2>Bắt đầu thiết lập database...</h2>";
    
    // 1. Thực thi file setup_database.sql
    echo "<h3>Bước 1: Tạo database và dữ liệu mẫu</h3>";
    $setup_sql = file_get_contents('setup_database.sql');
    $queries = array_filter(array_map('trim', explode(';', $setup_sql)));
    
    foreach($queries as $query) {
        if(empty($query)) continue;
        
        if(mysqli_query($con, $query)) {
            echo "Thực thi thành công: " . substr($query, 0, 50) . "...<br>";
        } else {
            throw new Exception("Lỗi khi thực thi câu lệnh: " . $query . "\nLỗi: " . mysqli_error($con));
        }
    }
    
    // 2. Thực thi file update_categories.sql
    echo "<h3>Bước 2: Phân loại sản phẩm</h3>";
    $update_sql = file_get_contents('update_categories.sql');
    $queries = array_filter(array_map('trim', explode(';', $update_sql)));
    
    foreach($queries as $query) {
        if(empty($query)) continue;
        
        if(mysqli_query($con, $query)) {
            if(stripos($query, 'SELECT') === 0) {
                $result = mysqli_query($con, $query);
                echo "<h4>Thống kê số lượng sản phẩm theo danh mục:</h4>";
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
    
    echo "<h3>Hoàn tất! Database đã được thiết lập và sản phẩm đã được phân loại.</h3>";

} catch(Exception $e) {
    // Đảm bảo bật lại foreign key checks trong trường hợp có lỗi
    mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");
    echo "<div style='color: red; font-weight: bold;'>Lỗi: " . $e->getMessage() . "</div>";
}

// Đóng kết nối
mysqli_close($con);
?> 