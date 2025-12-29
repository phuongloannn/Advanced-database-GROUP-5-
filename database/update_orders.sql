-- Tạo bảng orders nếu chưa tồn tại
CREATE TABLE IF NOT EXISTS orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM('Đang xử lý', 'Đang giao hàng', 'Đã hoàn thành', 'Đã hủy') DEFAULT 'Đang xử lý',
    shipping_name VARCHAR(100) NOT NULL,
    shipping_phone VARCHAR(20) NOT NULL,
    shipping_address TEXT NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng order_details nếu chưa tồn tại
CREATE TABLE IF NOT EXISTS order_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Thêm dữ liệu mẫu cho orders
INSERT INTO orders (user_id, total_price, status, shipping_name, shipping_phone, shipping_address, payment_method) VALUES
(1, 1500000, 'Đã hoàn thành', 'Nguyễn Văn A', '0123456789', 'Số 1, Đường ABC, Quận 1, TP.HCM', 'COD'),
(1, 2500000, 'Đang giao hàng', 'Nguyễn Văn A', '0123456789', 'Số 1, Đường ABC, Quận 1, TP.HCM', 'COD'),
(2, 3500000, 'Đã hoàn thành', 'Trần Thị B', '0987654321', 'Số 2, Đường XYZ, Quận 2, TP.HCM', 'Chuyển khoản'),
(3, 1800000, 'Đang xử lý', 'Lê Văn C', '0369852147', 'Số 3, Đường DEF, Quận 3, TP.HCM', 'COD'),
(2, 4200000, 'Đã hoàn thành', 'Trần Thị B', '0987654321', 'Số 2, Đường XYZ, Quận 2, TP.HCM', 'Chuyển khoản'),
(4, 950000, 'Đã hủy', 'Phạm Thị D', '0741852963', 'Số 4, Đường GHI, Quận 4, TP.HCM', 'COD');

-- Thêm dữ liệu mẫu cho order_details (giả sử có sản phẩm với ID từ 1-4)
INSERT INTO order_details (order_id, product_id, quantity, price) VALUES
(1, 1, 2, 750000),
(1, 2, 1, 750000),
(2, 3, 1, 2500000),
(3, 1, 2, 750000),
(3, 2, 2, 1000000),
(3, 4, 1, 1000000),
(4, 2, 3, 600000),
(5, 1, 3, 1400000),
(5, 3, 2, 1400000),
(6, 4, 1, 950000); 