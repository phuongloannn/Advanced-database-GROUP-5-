-- Thêm cột tracking_no vào bảng orders
ALTER TABLE orders ADD COLUMN tracking_no VARCHAR(50) AFTER id;

-- Cập nhật tracking_no cho các đơn hàng hiện có
UPDATE orders SET tracking_no = CONCAT('ORD', LPAD(id, 6, '0')); 