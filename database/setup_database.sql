-- Tạo database nếu chưa tồn tại
CREATE DATABASE IF NOT EXISTS fashion_shop_group5;
USE fashion_shop_group5;

-- Tạo bảng categories
CREATE TABLE IF NOT EXISTS categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT,
    status TINYINT DEFAULT 0,
    image VARCHAR(255) DEFAULT 'default.jpg',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Tạo bảng products
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    small_description TEXT,
    description TEXT,
    original_price DECIMAL(10,2) NOT NULL,
    selling_price DECIMAL(10,2) NOT NULL,
    image VARCHAR(255),
    qty INT NOT NULL,
    status TINYINT DEFAULT 0,
    trending TINYINT DEFAULT 0,
    meta_title VARCHAR(255),
    meta_keywords TEXT,
    meta_description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- Thêm dữ liệu mẫu cho sản phẩm
INSERT INTO products (name, slug, small_description, description, original_price, selling_price, image, qty, status, category_id) VALUES
-- Xe đạp địa hình
('Xe đạp địa hình MTB Pro', 'xe-dap-dia-hinh-mtb-pro', 'Xe đạp địa hình chuyên nghiệp', 'Xe đạp địa hình cao cấp, phù hợp địa hình núi', 15000000, 12000000, 'mtb1.jpg', 10, 1, 1),
('Mountain Bike X-Trail', 'mountain-bike-x-trail', 'Xe đạp leo núi đa năng', 'Xe đạp địa hình đa năng, thích hợp mọi địa hình', 12000000, 10000000, 'mtb2.jpg', 15, 1, 1),

-- Xe đạp trẻ em
('Xe đạp trẻ em Star Kids', 'xe-dap-tre-em-star', 'Xe đạp cho bé từ 6-10 tuổi', 'Xe đạp trẻ em an toàn, nhiều màu sắc', 3000000, 2500000, 'kids1.jpg', 20, 1, 1),
('Children Bike Rainbow', 'children-bike-rainbow', 'Xe đạp kid size nhỏ gọn', 'Xe đạp trẻ em thiết kế thân thiện', 2500000, 2000000, 'kids2.jpg', 25, 1, 1),

-- Xe đạp touring
('Xe đạp touring Travel Pro', 'xe-dap-touring-travel-pro', 'Xe đạp du lịch cao cấp', 'Xe đạp touring chuyên dụng cho phượt', 20000000, 18000000, 'tour1.jpg', 8, 1, 1),
('Touring Bike Adventure', 'touring-bike-adventure', 'Xe đạp touring đường trường', 'Xe đạp touring cho chuyến đi xa', 18000000, 15000000, 'tour2.jpg', 12, 1, 1),

-- Xe đạp đua
('Xe đạp đua Speed Master', 'xe-dap-dua-speed-master', 'Xe đạp đua chuyên nghiệp', 'Xe đạp đua tốc độ cao', 25000000, 22000000, 'racing1.jpg', 5, 1, 1),
('Racing Bike Lightning', 'racing-bike-lightning', 'Xe đạp racing nhẹ nhàng', 'Xe đạp đua siêu nhẹ cho tốc độ', 22000000, 20000000, 'racing2.jpg', 7, 1, 1); 