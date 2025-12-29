-- Tắt kiểm tra khóa ngoại
SET FOREIGN_KEY_CHECKS=0;

-- Đặt category_id về NULL cho tất cả sản phẩm
UPDATE products SET category_id = NULL WHERE status = '0';

-- Xóa dữ liệu từ bảng categories nhưng giữ lại cấu trúc bảng
DELETE FROM categories;

-- Đặt lại auto_increment
ALTER TABLE categories AUTO_INCREMENT = 22;

-- Thêm lại các danh mục với ID cố định
INSERT INTO categories (id, name, slug, description, status) VALUES
(22, 'Xe đạp địa hình', 'xe-dap-dia-hinh', 'Xe đạp chuyên dụng cho địa hình gồ ghề, đường núi, đường mòn', 0),
(23, 'Xe đạp trẻ em', 'xe-dap-tre-em', 'Xe đạp thiết kế đặc biệt phù hợp cho trẻ em các độ tuổi', 0),
(24, 'Xe đạp thể thao', 'xe-dap-the-thao', 'Xe đạp phù hợp cho tập luyện thể thao và đi lại hàng ngày', 0),
(25, 'Xe đạp đua', 'xe-dap-dua', 'Xe đạp chuyên dụng cho đua xe và tốc độ cao', 0);

-- 1. Phân loại xe đạp trẻ em (ưu tiên cao nhất)
UPDATE products 
SET category_id = 23 
WHERE status = '0' 
AND (
    name LIKE '%trẻ em%' 
    OR name LIKE '%trẻ%' 
    OR name LIKE '%kid%' 
    OR name LIKE '%children%'
    OR (selling_price <= 5000000 AND name LIKE '%nhỏ%')
);

-- 2. Phân loại xe đạp đua (ưu tiên thứ hai)
UPDATE products 
SET category_id = 25 
WHERE status = '0' 
AND category_id IS NULL
AND (
    name LIKE '%đua%' 
    OR name LIKE '%racing%' 
    OR name LIKE '%speed%' 
    OR name LIKE '%road bike%'
);

-- 3. Phân loại xe đạp địa hình
UPDATE products 
SET category_id = 22 
WHERE status = '0' 
AND category_id IS NULL
AND (
    name LIKE '%địa hình%' 
    OR name LIKE '%MTB%' 
    OR name LIKE '%mountain%' 
    OR name LIKE '%leo núi%'
);

-- 4. Phân loại xe đạp thể thao (còn lại)
UPDATE products 
SET category_id = 24 
WHERE status = '0' 
AND category_id IS NULL;

-- Bật lại kiểm tra khóa ngoại
SET FOREIGN_KEY_CHECKS=1;

-- Hiển thị kết quả phân loại
SELECT 
    c.name AS 'Danh mục',
    COUNT(p.id) AS 'Số lượng',
    GROUP_CONCAT(p.name SEPARATOR ', ') AS 'Danh sách sản phẩm'
FROM categories c
LEFT JOIN products p ON c.id = p.category_id AND p.status = '0'
GROUP BY c.id, c.name
ORDER BY c.id; 