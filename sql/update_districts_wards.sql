-- Xóa dữ liệu cũ trong bảng wards và districts
DELETE FROM wards;
DELETE FROM districts;

-- Thêm quận/huyện cho Hà Nội (id = 64)
INSERT INTO districts (name, province_id, code) VALUES
('Quận Ba Đình', 64, '001'),
('Quận Hoàn Kiếm', 64, '002'),
('Quận Tây Hồ', 64, '003'),
('Quận Long Biên', 64, '004'),
('Quận Cầu Giấy', 64, '005'),
('Quận Đống Đa', 64, '006'),
('Quận Hai Bà Trưng', 64, '007'),
('Quận Hoàng Mai', 64, '008'),
('Quận Thanh Xuân', 64, '009'),
('Quận Nam Từ Liêm', 64, '010'),
('Quận Bắc Từ Liêm', 64, '011'),
('Quận Hà Đông', 64, '012'),
('Thị xã Sơn Tây', 64, '013'),
('Huyện Ba Vì', 64, '014'),
('Huyện Chương Mỹ', 64, '015'),
('Huyện Đan Phượng', 64, '016'),
('Huyện Đông Anh', 64, '017'),
('Huyện Gia Lâm', 64, '018'),
('Huyện Hoài Đức', 64, '019'),
('Huyện Mê Linh', 64, '020'),
('Huyện Mỹ Đức', 64, '021'),
('Huyện Phú Xuyên', 64, '022'),
('Huyện Phúc Thọ', 64, '023'),
('Huyện Quốc Oai', 64, '024'),
('Huyện Sóc Sơn', 64, '025'),
('Huyện Thạch Thất', 64, '026'),
('Huyện Thanh Oai', 64, '027'),
('Huyện Thanh Trì', 64, '028'),
('Huyện Thường Tín', 64, '029'),
('Huyện Ứng Hòa', 64, '030');

-- Thêm quận/huyện cho TP.HCM (id = 65)
INSERT INTO districts (name, province_id, code) VALUES
('Quận 1', 65, '760'),
('Quận 12', 65, '761'),
('Quận Thủ Đức', 65, '762'),
('Quận 9', 65, '763'),
('Quận Gò Vấp', 65, '764'),
('Quận Bình Thạnh', 65, '765'),
('Quận Tân Bình', 65, '766'),
('Quận Tân Phú', 65, '767'),
('Quận Phú Nhuận', 65, '768'),
('Quận 2', 65, '769'),
('Quận 3', 65, '770'),
('Quận 10', 65, '771'),
('Quận 11', 65, '772'),
('Quận 4', 65, '773'),
('Quận 5', 65, '774'),
('Quận 6', 65, '775'),
('Quận 8', 65, '776'),
('Quận Bình Tân', 65, '777'),
('Quận 7', 65, '778'),
('Huyện Củ Chi', 65, '783'),
('Huyện Hóc Môn', 65, '784'),
('Huyện Bình Chánh', 65, '785'),
('Huyện Nhà Bè', 65, '786'),
('Huyện Cần Giờ', 65, '787');

-- Thêm quận/huyện cho Đà Nẵng (id = 67)
INSERT INTO districts (name, province_id, code) VALUES
('Quận Hải Châu', 67, '490'),
('Quận Thanh Khê', 67, '491'),
('Quận Sơn Trà', 67, '492'),
('Quận Ngũ Hành Sơn', 67, '493'),
('Quận Liên Chiểu', 67, '494'),
('Quận Cẩm Lệ', 67, '495'),
('Huyện Hòa Vang', 67, '497'),
('Huyện Hoàng Sa', 67, '498');

-- Sau khi thêm districts, chúng ta sẽ lấy ID của các quận để thêm phường/xã
-- Giả sử Quận 1 có id là 1, Quận 3 có id là 11, Quận Hoàn Kiếm có id là 2, Quận Cầu Giấy có id là 5, Quận Thanh Khê có id là 2

-- Thêm phường cho Quận 1, TP.HCM
INSERT INTO wards (name, district_id, code) VALUES
('Phường Tân Định', (SELECT id FROM districts WHERE name = 'Quận 1' AND province_id = 65), '26734'),
('Phường Đa Kao', (SELECT id FROM districts WHERE name = 'Quận 1' AND province_id = 65), '26737'),
('Phường Bến Nghé', (SELECT id FROM districts WHERE name = 'Quận 1' AND province_id = 65), '26740'),
('Phường Bến Thành', (SELECT id FROM districts WHERE name = 'Quận 1' AND province_id = 65), '26743'),
('Phường Nguyễn Thái Bình', (SELECT id FROM districts WHERE name = 'Quận 1' AND province_id = 65), '26746'),
('Phường Phạm Ngũ Lão', (SELECT id FROM districts WHERE name = 'Quận 1' AND province_id = 65), '26749'),
('Phường Cầu Ông Lãnh', (SELECT id FROM districts WHERE name = 'Quận 1' AND province_id = 65), '26752'),
('Phường Cô Giang', (SELECT id FROM districts WHERE name = 'Quận 1' AND province_id = 65), '26755'),
('Phường Nguyễn Cư Trinh', (SELECT id FROM districts WHERE name = 'Quận 1' AND province_id = 65), '26758'),
('Phường Cầu Kho', (SELECT id FROM districts WHERE name = 'Quận 1' AND province_id = 65), '26761');

-- Thêm phường cho Quận 3, TP.HCM
INSERT INTO wards (name, district_id, code) VALUES
('Phường 1', (SELECT id FROM districts WHERE name = 'Quận 3' AND province_id = 65), '27100'),
('Phường 2', (SELECT id FROM districts WHERE name = 'Quận 3' AND province_id = 65), '27103'),
('Phường 3', (SELECT id FROM districts WHERE name = 'Quận 3' AND province_id = 65), '27106'),
('Phường 4', (SELECT id FROM districts WHERE name = 'Quận 3' AND province_id = 65), '27109'),
('Phường 5', (SELECT id FROM districts WHERE name = 'Quận 3' AND province_id = 65), '27112'),
('Phường 6', (SELECT id FROM districts WHERE name = 'Quận 3' AND province_id = 65), '27115'),
('Phường 7', (SELECT id FROM districts WHERE name = 'Quận 3' AND province_id = 65), '27118'),
('Phường 8', (SELECT id FROM districts WHERE name = 'Quận 3' AND province_id = 65), '27121'),
('Phường 9', (SELECT id FROM districts WHERE name = 'Quận 3' AND province_id = 65), '27124'),
('Phường 10', (SELECT id FROM districts WHERE name = 'Quận 3' AND province_id = 65), '27127'),
('Phường 11', (SELECT id FROM districts WHERE name = 'Quận 3' AND province_id = 65), '27130'),
('Phường 12', (SELECT id FROM districts WHERE name = 'Quận 3' AND province_id = 65), '27133'),
('Phường 13', (SELECT id FROM districts WHERE name = 'Quận 3' AND province_id = 65), '27136'),
('Phường 14', (SELECT id FROM districts WHERE name = 'Quận 3' AND province_id = 65), '27139');

-- Thêm phường cho Quận Hoàn Kiếm, Hà Nội
INSERT INTO wards (name, district_id, code) VALUES
('Phường Phúc Tân', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00037'),
('Phường Đồng Xuân', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00040'),
('Phường Hàng Mã', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00043'),
('Phường Hàng Buồm', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00046'),
('Phường Hàng Đào', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00049'),
('Phường Hàng Bồ', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00052'),
('Phường Cửa Đông', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00055'),
('Phường Lý Thái Tổ', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00058'),
('Phường Hàng Bạc', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00061'),
('Phường Hàng Gai', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00064'),
('Phường Chương Dương', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00067'),
('Phường Hàng Trống', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00070'),
('Phường Cửa Nam', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00073'),
('Phường Hàng Bông', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00076'),
('Phường Tràng Tiền', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00079'),
('Phường Trần Hưng Đạo', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00082'),
('Phường Phan Chu Trinh', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00085'),
('Phường Hàng Bài', (SELECT id FROM districts WHERE name = 'Quận Hoàn Kiếm' AND province_id = 64), '00088');

-- Thêm phường cho Quận Cầu Giấy, Hà Nội
INSERT INTO wards (name, district_id, code) VALUES
('Phường Nghĩa Đô', (SELECT id FROM districts WHERE name = 'Quận Cầu Giấy' AND province_id = 64), '00145'),
('Phường Nghĩa Tân', (SELECT id FROM districts WHERE name = 'Quận Cầu Giấy' AND province_id = 64), '00148'),
('Phường Mai Dịch', (SELECT id FROM districts WHERE name = 'Quận Cầu Giấy' AND province_id = 64), '00151'),
('Phường Dịch Vọng', (SELECT id FROM districts WHERE name = 'Quận Cầu Giấy' AND province_id = 64), '00154'),
('Phường Dịch Vọng Hậu', (SELECT id FROM districts WHERE name = 'Quận Cầu Giấy' AND province_id = 64), '00157'),
('Phường Quan Hoa', (SELECT id FROM districts WHERE name = 'Quận Cầu Giấy' AND province_id = 64), '00160'),
('Phường Yên Hoà', (SELECT id FROM districts WHERE name = 'Quận Cầu Giấy' AND province_id = 64), '00163'),
('Phường Trung Hoà', (SELECT id FROM districts WHERE name = 'Quận Cầu Giấy' AND province_id = 64), '00166');

-- Thêm phường cho Quận Thanh Khê, Đà Nẵng
INSERT INTO wards (name, district_id, code) VALUES
('Phường Tam Thuận', (SELECT id FROM districts WHERE name = 'Quận Thanh Khê' AND province_id = 67), '20227'),
('Phường Thanh Khê Tây', (SELECT id FROM districts WHERE name = 'Quận Thanh Khê' AND province_id = 67), '20230'),
('Phường Thanh Khê Đông', (SELECT id FROM districts WHERE name = 'Quận Thanh Khê' AND province_id = 67), '20233'),
('Phường Xuân Hà', (SELECT id FROM districts WHERE name = 'Quận Thanh Khê' AND province_id = 67), '20236'),
('Phường Tân Chính', (SELECT id FROM districts WHERE name = 'Quận Thanh Khê' AND province_id = 67), '20239'),
('Phường Chính Gián', (SELECT id FROM districts WHERE name = 'Quận Thanh Khê' AND province_id = 67), '20242'),
('Phường Vĩnh Trung', (SELECT id FROM districts WHERE name = 'Quận Thanh Khê' AND province_id = 67), '20245'),
('Phường Thạc Gián', (SELECT id FROM districts WHERE name = 'Quận Thanh Khê' AND province_id = 67), '20248'),
('Phường An Khê', (SELECT id FROM districts WHERE name = 'Quận Thanh Khê' AND province_id = 67), '20251'),
('Phường Hoà Khê', (SELECT id FROM districts WHERE name = 'Quận Thanh Khê' AND province_id = 67), '20254'); 