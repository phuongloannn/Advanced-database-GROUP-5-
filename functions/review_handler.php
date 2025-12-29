<?php
session_start();
include('../config/dbcon.php');

header('Content-Type: application/json');

function sendResponse($status, $message, $data = null) {
    echo json_encode([
        'status' => $status,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['auth_user'])) {
    sendResponse('error', 'Vui lòng đăng nhập để đánh giá');
}

// Kiểm tra method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendResponse('error', 'Phương thức không hợp lệ');
}

try {
    // Lấy và validate dữ liệu
    $user_id = $_SESSION['auth_user']['id'];
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;
    $comment = isset($_POST['comment']) ? trim($_POST['comment']) : '';

    // Validate dữ liệu
    if ($product_id <= 0) {
        sendResponse('error', 'Sản phẩm không hợp lệ');
    }

    if ($rating < 1 || $rating > 5) {
        sendResponse('error', 'Đánh giá phải từ 1 đến 5 sao');
    }

    if (empty($comment)) {
        sendResponse('error', 'Vui lòng nhập nhận xét của bạn');
    }

    // Escape các giá trị
    $product_id = mysqli_real_escape_string($con, $product_id);
    $comment = mysqli_real_escape_string($con, $comment);

    // Kiểm tra sản phẩm tồn tại
    $check_product = mysqli_query($con, "SELECT id FROM products WHERE id = '$product_id'");
    if (mysqli_num_rows($check_product) == 0) {
        sendResponse('error', 'Sản phẩm không tồn tại');
    }

    // Bắt đầu transaction
    mysqli_begin_transaction($con);

    try {
        // Kiểm tra đánh giá đã tồn tại
        $check_review = mysqli_query($con, "SELECT id FROM reviews WHERE user_id = '$user_id' AND product_id = '$product_id'");
        
        if (mysqli_num_rows($check_review) > 0) {
            // Cập nhật đánh giá cũ
            $update_query = "UPDATE reviews 
                           SET rating = '$rating', 
                               comment = '$comment', 
                               created_at = CURRENT_TIMESTAMP 
                           WHERE user_id = '$user_id' 
                           AND product_id = '$product_id'";
            
            if (!mysqli_query($con, $update_query)) {
                throw new Exception("Không thể cập nhật đánh giá");
            }
            $message = "Đã cập nhật đánh giá của bạn";
        } else {
            // Thêm đánh giá mới
            $insert_query = "INSERT INTO reviews (user_id, product_id, rating, comment) 
                           VALUES ('$user_id', '$product_id', '$rating', '$comment')";
            
            if (!mysqli_query($con, $insert_query)) {
                throw new Exception("Không thể thêm đánh giá mới");
            }
            $message = "Cảm ơn bạn đã đánh giá sản phẩm";
        }

        // Cập nhật rating trung bình
        $update_avg = "UPDATE products p 
                      SET rating = (
                          SELECT ROUND(AVG(rating), 2)
                          FROM reviews 
                          WHERE product_id = '$product_id'
                      )
                      WHERE id = '$product_id'";
        
        if (!mysqli_query($con, $update_avg)) {
            throw new Exception("Không thể cập nhật điểm trung bình");
        }

        // Lấy danh sách đánh giá mới
        $reviews_query = "SELECT r.*, u.name as user_name 
                         FROM reviews r 
                         JOIN users u ON r.user_id = u.id 
                         WHERE r.product_id = '$product_id' 
                         ORDER BY r.created_at DESC";
        $reviews_result = mysqli_query($con, $reviews_query);
        
        if (!$reviews_result) {
            throw new Exception("Không thể lấy danh sách đánh giá");
        }

        $reviews = [];
        while ($row = mysqli_fetch_assoc($reviews_result)) {
            $reviews[] = [
                'user_name' => htmlspecialchars($row['user_name']),
                'rating' => (int)$row['rating'],
                'comment' => htmlspecialchars($row['comment']),
                'created_at' => date('d/m/Y', strtotime($row['created_at']))
            ];
        }

        // Lấy rating trung bình mới
        $avg_query = "SELECT rating FROM products WHERE id = '$product_id'";
        $avg_result = mysqli_query($con, $avg_query);
        
        if (!$avg_result) {
            throw new Exception("Không thể lấy điểm trung bình");
        }

        $avg_rating = number_format(mysqli_fetch_assoc($avg_result)['rating'], 1);

        // Commit transaction
        mysqli_commit($con);

        // Trả về kết quả
        sendResponse('success', $message, [
            'reviews' => $reviews,
            'average_rating' => $avg_rating
        ]);

    } catch (Exception $e) {
        mysqli_rollback($con);
        throw $e;
    }

} catch (Exception $e) {
    sendResponse('error', $e->getMessage());
}
?> 