-- Tạo bảng tỉnh/thành phố
CREATE TABLE IF NOT EXISTS provinces (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(20)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng quận/huyện
CREATE TABLE IF NOT EXISTS districts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    province_id INT,
    code VARCHAR(20),
    CONSTRAINT fk_district_province FOREIGN KEY (province_id) REFERENCES provinces(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tạo bảng phường/xã
CREATE TABLE IF NOT EXISTS wards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    district_id INT,
    code VARCHAR(20),
    CONSTRAINT fk_ward_district FOREIGN KEY (district_id) REFERENCES districts(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Thêm cột vào bảng users để lưu thông tin địa chỉ chi tiết
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS street_address TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS province_id INT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS district_id INT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS ward_id INT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS avatar VARCHAR(255) DEFAULT NULL;

-- Thêm foreign key cho bảng users
ALTER TABLE users
ADD CONSTRAINT fk_user_province 
FOREIGN KEY (province_id) REFERENCES provinces(id);

ALTER TABLE users
ADD CONSTRAINT fk_user_district 
FOREIGN KEY (district_id) REFERENCES districts(id);

ALTER TABLE users
ADD CONSTRAINT fk_user_ward 
FOREIGN KEY (ward_id) REFERENCES wards(id);

-- Thêm dữ liệu mẫu cho bảng provinces
INSERT INTO provinces (name, code) VALUES 
('Thành phố Hà Nội', '01'),
('Thành phố Hồ Chí Minh', '79'),
('Thành phố Hải Phòng', '31'),
('Thành phố Đà Nẵng', '48'),
('Thành phố Cần Thơ', '92');

-- Thêm dữ liệu mẫu cho Hà Nội
INSERT INTO districts (name, province_id, code) VALUES
('Quận Ba Đình', 1, '001'),
('Quận Hoàn Kiếm', 1, '002'),
('Quận Tây Hồ', 1, '003'),
('Quận Long Biên', 1, '004'),
('Quận Cầu Giấy', 1, '005');

-- Thêm dữ liệu mẫu cho TP.HCM
INSERT INTO districts (name, province_id, code) VALUES
('Quận 1', 2, '760'),
('Quận 3', 2, '770'),
('Quận 4', 2, '773'),
('Quận 5', 2, '774'),
('Quận 7', 2, '778');

-- Thêm dữ liệu mẫu cho phường của Quận Ba Đình
INSERT INTO wards (name, district_id, code) VALUES
('Phường Phúc Xá', 1, '00001'),
('Phường Trúc Bạch', 1, '00004'),
('Phường Vĩnh Phúc', 1, '00006'),
('Phường Cống Vị', 1, '00007'),
('Phường Liễu Giai', 1, '00008');

-- Thêm dữ liệu mẫu cho phường của Quận 1
INSERT INTO wards (name, district_id, code) VALUES
('Phường Bến Nghé', 6, '26740'),
('Phường Bến Thành', 6, '26743'),
('Phường Cô Giang', 6, '26755'),
('Phường Cầu Ông Lãnh', 6, '26752'),
('Phường Nguyễn Thái Bình', 6, '26746'); 