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