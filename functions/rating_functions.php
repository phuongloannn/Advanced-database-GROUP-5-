<?php
include('config/dbcon.php');

// Add or update product rating
function rateProduct($product_id, $user_id, $rating, $comment = '') {
    global $con;
    
    $comment = mysqli_real_escape_string($con, $comment);
    
    $query = "INSERT INTO product_ratings (product_id, user_id, rating, comment) 
              VALUES ('$product_id', '$user_id', '$rating', '$comment')
              ON DUPLICATE KEY UPDATE rating = '$rating', comment = '$comment'";
              
    if(mysqli_query($con, $query)) {
        // Update average rating in products table
        updateProductAverageRating($product_id);
        return true;
    }
    return false;
}

// Update product's average rating
function updateProductAverageRating($product_id) {
    global $con;
    
    $query = "UPDATE products p 
              SET average_rating = (
                  SELECT AVG(rating) 
                  FROM product_ratings 
                  WHERE product_id = '$product_id'
              )
              WHERE id = '$product_id'";
              
    return mysqli_query($con, $query);
}

// Get product rating by user
function getProductRatingByUser($product_id, $user_id) {
    global $con;
    
    $query = "SELECT rating, comment 
              FROM product_ratings 
              WHERE product_id = '$product_id' 
              AND user_id = '$user_id'";
              
    $result = mysqli_query($con, $query);
    return mysqli_fetch_assoc($result);
}

// Get all ratings for a product
function getProductRatings($product_id) {
    global $con;
    
    $query = "SELECT pr.*, u.name as user_name, u.avatar 
              FROM product_ratings pr 
              JOIN users u ON pr.user_id = u.id 
              WHERE pr.product_id = ? 
              ORDER BY pr.created_at DESC";
              
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $ratings = array();
    while($row = mysqli_fetch_assoc($result)) {
        $ratings[] = $row;
    }
    
    return $ratings;
}

// Add or update website rating
function rateWebsite($user_id, $rating, $feedback = '') {
    global $con;
    
    $feedback = mysqli_real_escape_string($con, $feedback);
    
    $query = "INSERT INTO website_ratings (user_id, rating, feedback) 
              VALUES ('$user_id', '$rating', '$feedback')
              ON DUPLICATE KEY UPDATE rating = '$rating', feedback = '$feedback'";
              
    return mysqli_query($con, $query);
}

// Get website rating by user
function getWebsiteRatingByUser($user_id) {
    global $con;
    
    $query = "SELECT rating, feedback 
              FROM website_ratings 
              WHERE user_id = '$user_id'";
              
    $result = mysqli_query($con, $query);
    return mysqli_fetch_assoc($result);
}

// Get website average rating
function getWebsiteAverageRating() {
    global $con;
    
    $query = "SELECT AVG(rating) as average_rating, COUNT(*) as total_ratings 
              FROM website_ratings";
              
    $result = mysqli_query($con, $query);
    return mysqli_fetch_assoc($result);
}

// Lấy đánh giá trung bình của một sản phẩm
function getAverageRating($product_id) {
    global $con;
    
    $query = "SELECT AVG(rating) as average_rating, COUNT(*) as total_ratings 
              FROM product_ratings 
              WHERE product_id = ?";
              
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    return array(
        'average' => round($row['average_rating'], 1),
        'total' => $row['total_ratings']
    );
}

// Kiểm tra xem người dùng đã đánh giá sản phẩm chưa
function hasUserRated($user_id, $product_id) {
    global $con;
    
    $query = "SELECT id FROM product_ratings WHERE user_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    
    return mysqli_stmt_num_rows($stmt) > 0;
}

// Lấy đánh giá của người dùng cho một sản phẩm
function getUserRating($user_id, $product_id) {
    global $con;
    
    $query = "SELECT rating, comment FROM product_ratings WHERE user_id = ? AND product_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    return mysqli_fetch_assoc($result);
}
?> 