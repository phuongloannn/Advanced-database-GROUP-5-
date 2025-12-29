-- Thêm dữ liệu mẫu cho bảng provinces
INSERT INTO provinces (name, code) VALUES 
('Hà Nội', 'HN'),
('TP Hồ Chí Minh', 'HCM'),
('Đà Nẵng', 'DN'),
('Hải Phòng', 'HP'),
('Cần Thơ', 'CT');

-- Thêm dữ liệu mẫu cho bảng districts của Hà Nội
INSERT INTO districts (name, province_id, code) VALUES 
('Quận Ba Đình', 1, 'BD'),
('Quận Hoàn Kiếm', 1, 'HK'),
('Quận Tây Hồ', 1, 'TH'),
('Quận Long Biên', 1, 'LB'),
('Quận Cầu Giấy', 1, 'CG');

-- Thêm dữ liệu mẫu cho bảng districts của TP HCM
INSERT INTO districts (name, province_id, code) VALUES 
('Quận 1', 2, 'Q1'),
('Quận 3', 2, 'Q3'),
('Quận 4', 2, 'Q4'),
('Quận 5', 2, 'Q5'),
('Quận 7', 2, 'Q7');

-- Thêm dữ liệu mẫu cho bảng wards của Quận Ba Đình
INSERT INTO wards (name, district_id, code) VALUES 
('Phường Phúc Xá', 1, 'PX'),
('Phường Trúc Bạch', 1, 'TB'),
('Phường Vĩnh Phúc', 1, 'VP'),
('Phường Cống Vị', 1, 'CV'),
('Phường Liễu Giai', 1, 'LG');

-- Thêm dữ liệu mẫu cho bảng wards của Quận 1
INSERT INTO wards (name, district_id, code) VALUES 
('Phường Bến Nghé', 6, 'BN'),
('Phường Bến Thành', 6, 'BT'),
('Phường Cô Giang', 6, 'CG'),
('Phường Cầu Ông Lãnh', 6, 'COL'),
('Phường Nguyễn Thái Bình', 6, 'NTB'); 