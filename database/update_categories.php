<?php
include('../config/dbcon.php');

try {
    // Tắt tạm thời foreign key checks
    mysqli_query($con, "SET FOREIGN_KEY_CHECKS=0");

    // Tạo các danh mục mới
    $categories = [
        ['Xe đạp địa hình', 'xe-dap-dia-hinh', 'Xe đạp địa hình chất lượng cao, phù hợp cho các địa hình phức tạp'],
        ['Xe đạp trẻ em', 'xe-dap-tre-em', 'Xe đạp an toàn và phù hợp cho trẻ em các lứa tuổi'],
        ['Xe đạp touring', 'xe-dap-touring', 'Xe đạp touring chuyên dụng cho các chuyến đi xa'],
        ['Xe đạp đua', 'xe-dap-dua', 'Xe đạp đua chuyên nghiệp cho tốc độ cao']
    ];

    // Xóa các danh mục cũ
    if(mysqli_query($con, "DELETE FROM categories")) {
        echo "Đã xóa các danh mục cũ thành công<br>";
    } else {
        throw new Exception("Lỗi khi xóa danh mục cũ: " . mysqli_error($con));
    }

    // Reset auto increment
    if(!mysqli_query($con, "ALTER TABLE categories AUTO_INCREMENT = 1")) {
        throw new Exception("Lỗi khi reset auto increment: " . mysqli_error($con));
    }

    // Thêm các danh mục mới
    $category_ids = [];
    $insert_category = "INSERT INTO categories (name, slug, description, status, image) VALUES (?, ?, ?, 0, 'default.jpg')";
    $stmt = mysqli_prepare($con, $insert_category);

    if(!$stmt) {
        throw new Exception("Lỗi khi chuẩn bị câu lệnh: " . mysqli_error($con));
    }

    foreach($categories as $category) {
        if(!mysqli_stmt_bind_param($stmt, "sss", $category[0], $category[1], $category[2])) {
            throw new Exception("Lỗi khi bind param: " . mysqli_stmt_error($stmt));
        }
        
        if(!mysqli_stmt_execute($stmt)) {
            throw new Exception("Lỗi khi thêm danh mục {$category[0]}: " . mysqli_stmt_error($stmt));
        }
        
        $category_ids[$category[1]] = mysqli_insert_id($con);
        echo "Đã thêm danh mục {$category[0]} thành công (ID: {$category_ids[$category[1]]})<br>";
    }

    mysqli_stmt_close($stmt);

    // Đặt tất cả sản phẩm về danh mục mặc định trước
    $default_category_id = $category_ids['xe-dap-dia-hinh'];
    if(!mysqli_query($con, "UPDATE products SET category_id = $default_category_id")) {
        throw new Exception("Lỗi khi cập nhật danh mục mặc định: " . mysqli_error($con));
    }

    // Cập nhật danh mục cho từng loại sản phẩm
    $updates = [
        'xe-dap-dia-hinh' => "name LIKE '%địa hình%' OR name LIKE '%MTB%' OR name LIKE '%mountain%' OR description LIKE '%địa hình%' OR small_description LIKE '%địa hình%'",
        'xe-dap-tre-em' => "name LIKE '%trẻ em%' OR name LIKE '%kid%' OR name LIKE '%children%' OR description LIKE '%trẻ em%' OR small_description LIKE '%trẻ em%'",
        'xe-dap-touring' => "name LIKE '%touring%' OR name LIKE '%du lịch%' OR name LIKE '%travel%' OR description LIKE '%touring%' OR small_description LIKE '%touring%'",
        'xe-dap-dua' => "name LIKE '%đua%' OR name LIKE '%racing%' OR name LIKE '%speed%' OR description LIKE '%đua%' OR small_description LIKE '%đua%'"
    ];

    foreach($updates as $slug => $condition) {
        $category_id = $category_ids[$slug];
        $query = "UPDATE products SET category_id = $category_id WHERE $condition";
        if(!mysqli_query($con, $query)) {
            throw new Exception("Lỗi khi cập nhật sản phẩm cho danh mục $slug: " . mysqli_error($con));
        }
        echo "Đã cập nhật sản phẩm cho danh mục $slug thành công<br>";
    }

    // Bật lại foreign key checks
    mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");

    // Hiển thị thống kê
    echo "<h3>Thống kê số lượng sản phẩm theo danh mục:</h3>";
    $stats = mysqli_query($con, "SELECT c.name, COUNT(p.id) as count 
                               FROM categories c 
                               LEFT JOIN products p ON c.id = p.category_id 
                               GROUP BY c.id, c.name");

    if($stats) {
        while($row = mysqli_fetch_assoc($stats)) {
            echo "{$row['name']}: {$row['count']} sản phẩm<br>";
        }
    } else {
        throw new Exception("Lỗi khi lấy thống kê: " . mysqli_error($con));
    }

} catch(Exception $e) {
    // Bật lại foreign key checks trong trường hợp có lỗi
    mysqli_query($con, "SET FOREIGN_KEY_CHECKS=1");
    echo "<div style='color: red; font-weight: bold;'>Lỗi: " . $e->getMessage() . "</div>";
}
?> 