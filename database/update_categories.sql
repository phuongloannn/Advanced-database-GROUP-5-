-- Tắt kiểm tra khóa ngoại tạm thời
SET FOREIGN_KEY_CHECKS=0;

-- Xóa các danh mục cũ
DELETE FROM categories;

-- Reset auto increment
ALTER TABLE categories AUTO_INCREMENT = 1;

-- Thêm các danh mục mới
INSERT INTO categories (name, slug, description, status, image) VALUES
('Xe đạp địa hình', 'xe-dap-dia-hinh', 'Xe đạp địa hình chất lượng cao, phù hợp cho các địa hình phức tạp', 0, 'default.jpg'),
('Xe đạp trẻ em', 'xe-dap-tre-em', 'Xe đạp an toàn và phù hợp cho trẻ em các lứa tuổi', 0, 'default.jpg'),
('Xe đạp touring', 'xe-dap-touring', 'Xe đạp touring chuyên dụng cho các chuyến đi xa', 0, 'default.jpg'),
('Xe đạp đua', 'xe-dap-dua', 'Xe đạp đua chuyên nghiệp cho tốc độ cao', 0, 'default.jpg');

-- Đặt tất cả sản phẩm về danh mục mặc định (xe đạp địa hình - ID 1)
UPDATE products SET category_id = 1;

-- Cập nhật danh mục cho xe đạp trẻ em (ID 2)
UPDATE products SET category_id = 2
WHERE name LIKE '%trẻ em%' 
   OR name LIKE '%kid%' 
   OR name LIKE '%children%' 
   OR description LIKE '%trẻ em%' 
   OR small_description LIKE '%trẻ em%';

-- Cập nhật danh mục cho xe đạp touring (ID 3)
UPDATE products SET category_id = 3
WHERE name LIKE '%touring%' 
   OR name LIKE '%du lịch%' 
   OR name LIKE '%travel%' 
   OR description LIKE '%touring%' 
   OR small_description LIKE '%touring%';

-- Cập nhật danh mục cho xe đạp đua (ID 4)
UPDATE products SET category_id = 4
WHERE name LIKE '%đua%' 
   OR name LIKE '%racing%' 
   OR name LIKE '%speed%' 
   OR description LIKE '%đua%' 
   OR small_description LIKE '%đua%';

-- Bật lại kiểm tra khóa ngoại
SET FOREIGN_KEY_CHECKS=1;

-- Hiển thị thống kê
SELECT c.name, COUNT(p.id) as count 
FROM categories c 
LEFT JOIN products p ON c.id = p.category_id 
GROUP BY c.id, c.name;