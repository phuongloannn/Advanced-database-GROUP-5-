-- Cập nhật bảng orders thêm trường trạng thái
ALTER TABLE orders ADD COLUMN status VARCHAR(50) DEFAULT 'pending' COMMENT 'pending,preparing,confirmed,completed,cancelled,returned';
ALTER TABLE orders ADD COLUMN delivery_date DATETIME DEFAULT NULL;
ALTER TABLE orders ADD COLUMN confirmed_date DATETIME DEFAULT NULL;

-- Tạo bảng cho yêu cầu trả/đổi hàng
CREATE TABLE return_requests (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    reason TEXT NOT NULL,
    request_type ENUM('return', 'exchange') NOT NULL,
    status VARCHAR(50) DEFAULT 'pending' COMMENT 'pending,approved,rejected,completed',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Tạo bảng cho hình ảnh chứng minh trả/đổi hàng
CREATE TABLE return_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    return_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (return_id) REFERENCES return_requests(id)
);

-- Cập nhật bảng reviews thêm trường xác nhận đã mua hàng
ALTER TABLE reviews ADD COLUMN is_verified_purchase BOOLEAN DEFAULT FALSE; 