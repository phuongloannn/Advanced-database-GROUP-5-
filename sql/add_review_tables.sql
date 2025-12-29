-- Tạo bảng reviews nếu chưa tồn tại
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `product_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `rating` int(1) NOT NULL,
    `comment` text NOT NULL,
    `images` text DEFAULT NULL,
    `shop_reply` text DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp(),
    PRIMARY KEY (`id`),
    KEY `product_id` (`product_id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `reviews_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
    CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng review_helpful
CREATE TABLE IF NOT EXISTS `review_helpful` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `review_id` int(11) NOT NULL,
    `user_id` int(11) NOT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`),
    UNIQUE KEY `review_user_unique` (`review_id`, `user_id`),
    KEY `review_id` (`review_id`),
    KEY `user_id` (`user_id`),
    CONSTRAINT `review_helpful_ibfk_1` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE,
    CONSTRAINT `review_helpful_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci; 