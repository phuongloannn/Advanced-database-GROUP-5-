-- Thêm các cột mới vào bảng orders
ALTER TABLE orders
ADD COLUMN total_price decimal(10,2) NOT NULL DEFAULT 0.00,
ADD COLUMN shipping_name varchar(255) NOT NULL,
ADD COLUMN shipping_phone varchar(20) NOT NULL,
ADD COLUMN shipping_address text NOT NULL;

-- Tạo bảng order_items
CREATE TABLE order_items (
    id int(11) NOT NULL AUTO_INCREMENT,
    order_id int(11) NOT NULL,
    product_id int(11) NOT NULL,
    quantity int(11) NOT NULL,
    price decimal(10,2) NOT NULL,
    created_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm các chỉ mục cho bảng orders nếu chưa có
ALTER TABLE orders
ADD INDEX idx_user_id (user_id),
ADD INDEX idx_status (status),
ADD INDEX idx_created_at (created_at); 