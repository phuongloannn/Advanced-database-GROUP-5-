<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('functions/userfunctions.php');

// Check if user is logged in
if (!isset($_SESSION['auth_user']['id']) || empty($_SESSION['auth_user']['id'])) {
    $_SESSION['error'] = 'Vui lòng đăng nhập để đánh giá';
    header("Location: product-detail.php?id=" . $_POST['product_id']);
    exit;
}

// Check required POST data
if (!isset($_POST['product_id']) || !isset($_POST['rating']) || !isset($_POST['comment'])) {
    $_SESSION['error'] = 'Thiếu thông tin đánh giá';
    header("Location: product-detail.php?id=" . $_POST['product_id']);
    exit;
}

$product_id = $_POST['product_id'];
$user_id = $_SESSION['auth_user']['id'];
$rating = $_POST['rating'];
$comment = $_POST['comment'];

// Validate rating
if (!is_numeric($rating) || $rating < 1 || $rating > 5) {
    $_SESSION['error'] = 'Đánh giá không hợp lệ';
    header("Location: product-detail.php?id=" . $product_id);
    exit;
}

// Check if user has purchased the product
if (!hasUserPurchased($product_id, $user_id)) {
    $_SESSION['error'] = 'Bạn cần mua hàng trước khi đánh giá sản phẩm';
    header("Location: product-detail.php?id=" . $product_id);
    exit;
}

// Check if user has already reviewed
if (hasUserReviewed($product_id, $user_id)) {
    $_SESSION['error'] = 'Bạn đã đánh giá sản phẩm này rồi';
    header("Location: product-detail.php?id=" . $product_id);
    exit;
}

// Submit review
if (submitReview($product_id, $user_id, $rating, $comment)) {
    $_SESSION['success'] = 'Cảm ơn bạn đã đánh giá sản phẩm!';
} else {
    $_SESSION['error'] = 'Có lỗi xảy ra khi gửi đánh giá';
}

header("Location: product-detail.php?id=" . $product_id);
exit;
?>