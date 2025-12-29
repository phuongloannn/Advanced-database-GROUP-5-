<?php
include(__DIR__ . "/../config/dbcon.php");

// Create product ratings table
$create_product_ratings = "CREATE TABLE IF NOT EXISTS product_ratings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    product_id INT NOT NULL,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_product_user (product_id, user_id)
)";

// Create website ratings table
$create_website_ratings = "CREATE TABLE IF NOT EXISTS website_ratings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    feedback TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user (user_id)
)";

// Execute queries
if(mysqli_query($conn, $create_product_ratings)) {
    echo "Product ratings table created successfully\n";
} else {
    echo "Error creating product ratings table: " . mysqli_error($conn) . "\n";
}

if(mysqli_query($conn, $create_website_ratings)) {
    echo "Website ratings table created successfully\n";
} else {
    echo "Error creating website ratings table: " . mysqli_error($conn) . "\n";
}

// Add average_rating column to products table if not exists
$add_avg_rating = "ALTER TABLE products ADD COLUMN IF NOT EXISTS average_rating DECIMAL(3,2) DEFAULT 0.00";
if(mysqli_query($conn, $add_avg_rating)) {
    echo "Average rating column added to products table\n";
} else {
    echo "Error adding average rating column: " . mysqli_error($conn) . "\n";
}
?> 