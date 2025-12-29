<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('functions/userfunctions.php');

header('Content-Type: application/json');

if (!isset($_GET['product_id'])) {
    echo json_encode([
        'error' => true,
        'message' => 'Product ID is required'
    ]);
    exit;
}

$product_id = $_GET['product_id'];

// Lấy điểm trung bình từ hàm đã có
$avg_rating = getAverageRating($product_id);

// Đếm tổng số đánh giá
$reviews = getProductReviews($product_id);
$total_reviews = mysqli_num_rows($reviews);

// Trả về kết quả dạng JSON
echo json_encode([
    'success' => true,
    'avg_rating' => $avg_rating,
    'total_reviews' => $total_reviews
]);
?>