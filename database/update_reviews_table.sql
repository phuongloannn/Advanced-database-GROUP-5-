-- Thêm cột images nếu chưa tồn tại
ALTER TABLE reviews ADD COLUMN IF NOT EXISTS images TEXT DEFAULT NULL;

-- Thêm cột shop_reply nếu chưa tồn tại
ALTER TABLE reviews ADD COLUMN IF NOT EXISTS shop_reply TEXT DEFAULT NULL;

-- Thêm cột helpful_count nếu chưa tồn tại
ALTER TABLE reviews ADD COLUMN IF NOT EXISTS helpful_count INT DEFAULT 0;

-- Tạo bảng review_helpful nếu chưa tồn tại
CREATE TABLE IF NOT EXISTS review_helpful (
    id INT PRIMARY KEY AUTO_INCREMENT,
    review_id INT NOT NULL,
    user_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (review_id) REFERENCES reviews(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_helpful (review_id, user_id)
); 