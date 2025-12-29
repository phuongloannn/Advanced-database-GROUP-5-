-- Tạo bảng reviews để lưu trữ đánh giá sản phẩm
CREATE TABLE IF NOT EXISTS `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `rating` int(1) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `product_id` (`product_id`),
  KEY `order_id` (`order_id`),
  CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `rating_range` CHECK (`rating` >= 1 AND `rating` <= 5)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm cột rating vào bảng products nếu chưa có
ALTER TABLE `products` 
ADD COLUMN IF NOT EXISTS `rating` DECIMAL(3,2) DEFAULT 0.00;

-- Tạo trigger để tự động cập nhật rating trung bình của sản phẩm
DELIMITER //
CREATE TRIGGER IF NOT EXISTS update_product_rating AFTER INSERT ON reviews
FOR EACH ROW
BEGIN
    UPDATE products 
    SET rating = (
        SELECT AVG(rating) 
        FROM reviews 
        WHERE product_id = NEW.product_id
    )
    WHERE id = NEW.product_id;
END;
//
DELIMITER ;

-- Tạo trigger để cập nhật rating khi xóa đánh giá
DELIMITER //
CREATE TRIGGER IF NOT EXISTS update_product_rating_after_delete AFTER DELETE ON reviews
FOR EACH ROW
BEGIN
    UPDATE products 
    SET rating = COALESCE(
        (SELECT AVG(rating) FROM reviews WHERE product_id = OLD.product_id),
        0
    )
    WHERE id = OLD.product_id;
END;
//
DELIMITER ;

-- Tạo trigger để cập nhật rating khi sửa đánh giá
DELIMITER //
CREATE TRIGGER IF NOT EXISTS update_product_rating_after_update AFTER UPDATE ON reviews
FOR EACH ROW
BEGIN
    IF OLD.rating != NEW.rating THEN
        UPDATE products 
        SET rating = (
            SELECT AVG(rating) 
            FROM reviews 
            WHERE product_id = NEW.product_id
        )
        WHERE id = NEW.product_id;
    END IF;
END;
//
DELIMITER ; 