-- Kiểm tra xem cột created_at đã tồn tại chưa
SET @dbname = 'fashion_shop_group5';
SET @tablename = 'users';
SET @columnname = 'created_at';
SET @preparedStatement = (SELECT IF(
  (
    SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
    WHERE
      TABLE_SCHEMA = @dbname
      AND TABLE_NAME = @tablename
      AND COLUMN_NAME = @columnname
  ) > 0,
  "SELECT 'Column already exists'",
  CONCAT("ALTER TABLE ", @tablename, " ADD COLUMN ", @columnname, " TIMESTAMP DEFAULT CURRENT_TIMESTAMP")
));

PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Cập nhật created_at cho các user hiện tại nếu là NULL
UPDATE users SET created_at = CURRENT_TIMESTAMP WHERE created_at IS NULL; 