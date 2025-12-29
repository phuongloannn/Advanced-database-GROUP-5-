<?php
session_start();
include('../config/dbcon.php');
include('userfunctions.php');

header('Content-Type: application/json');

if (!isset($_SESSION['auth_user'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$user_id = $_SESSION['auth_user']['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // Enable debug logging
    error_log("Debug: Action = $action");
    error_log("Debug: User ID = $user_id");
    error_log("Debug: Product ID = " . ($_POST['product_id'] ?? 'Not set'));
    error_log("Debug: POST data = " . print_r($_POST, true));
    
    switch ($action) {
        case 'toggle_helpful':
            $review_id = $_POST['review_id'] ?? 0;
            
            if (!$review_id) {
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đánh giá']);
                exit;
            }
            
            // Kiểm tra xem đã đánh dấu hữu ích chưa
            $is_helpful = hasMarkedHelpful($review_id, $user_id);
            
            if ($is_helpful) {
                // Nếu đã đánh dấu thì xóa
                updateHelpfulCount($review_id, $user_id, 'remove');
                echo json_encode(['success' => true, 'added' => false]);
            } else {
                // Nếu chưa đánh dấu thì thêm
                updateHelpfulCount($review_id, $user_id, 'add');
                echo json_encode(['success' => true, 'added' => true]);
            }
            break;
            
        case 'submit_review':
            // Xử lý thêm đánh giá mới
            $product_id = $_POST['product_id'] ?? 0;
            $rating = $_POST['rating'] ?? 0;
            $comment = $_POST['comment'] ?? '';
            
            error_log("Debug: Processing review - Product ID: $product_id, Rating: $rating");
            
            if (!$product_id || !$rating || !$comment) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
                exit;
            }
            
            // Kiểm tra xem người dùng đã mua sản phẩm và đơn hàng đã hoàn thành chưa
            $check_order = "SELECT o.id as order_id 
                          FROM orders o 
                          JOIN order_detail od ON o.id = od.order_id 
                          WHERE o.user_id = ? 
                          AND od.product_id = ? 
                          AND o.status = 3 
                          LIMIT 1";
            
            $stmt = mysqli_prepare($con, $check_order);
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) == 0) {
                echo json_encode(['success' => false, 'message' => 'Bạn cần mua hàng và hoàn thành đơn hàng trước khi đánh giá sản phẩm']);
                exit;
            }
            
            $order = mysqli_fetch_assoc($result);
            $order_id = $order['order_id'];
            
            // Kiểm tra xem đã đánh giá chưa
            $check_review = "SELECT 1 FROM reviews 
                           WHERE user_id = ? AND product_id = ? 
                           LIMIT 1";
            $stmt = mysqli_prepare($con, $check_review);
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
            mysqli_stmt_execute($stmt);
            
            if (mysqli_stmt_get_result($stmt)->num_rows > 0) {
                echo json_encode(['success' => false, 'message' => 'Bạn đã đánh giá sản phẩm này']);
                exit;
            }
            
            // Xử lý upload ảnh
            $images = [];
            if (!empty($_FILES['images']['name'][0])) {
                $upload_dir = '../uploads/reviews/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                
                $file_count = count($_FILES['images']['name']);
                if ($file_count > 5) {
                    echo json_encode(['success' => false, 'message' => 'Chỉ được upload tối đa 5 ảnh']);
                    exit;
                }
                
                for ($i = 0; $i < $file_count; $i++) {
                    if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['images']['tmp_name'][$i];
                        $name = $_FILES['images']['name'][$i];
                        $extension = pathinfo($name, PATHINFO_EXTENSION);
                        
                        // Kiểm tra định dạng file
                        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                        if (!in_array(strtolower($extension), $allowed)) {
                            echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận file ảnh (jpg, jpeg, png, gif)']);
                            exit;
                        }
                        
                        // Tạo tên file mới
                        $new_name = uniqid() . '.' . $extension;
                        $upload_path = $upload_dir . $new_name;
                        
                        if (move_uploaded_file($tmp_name, $upload_path)) {
                            $images[] = $new_name;
                        }
                    }
                }
            }
            
            // Thêm đánh giá vào database
            $images_str = !empty($images) ? implode(',', $images) : null;
            $query = "INSERT INTO reviews (product_id, user_id, order_id, rating, comment, images, created_at) 
                     VALUES (?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "iiisss", $product_id, $user_id, $order_id, $rating, $comment, $images_str);
            
            if (mysqli_stmt_execute($stmt)) {
                // Cập nhật trạng thái đã đánh giá trong order_detail
                $update_order = "UPDATE order_detail 
                               SET rate = ? 
                               WHERE order_id = ? AND product_id = ?";
                $stmt = mysqli_prepare($con, $update_order);
                mysqli_stmt_bind_param($stmt, "iii", $rating, $order_id, $product_id);
                mysqli_stmt_execute($stmt);
                
                // Cập nhật rating trung bình của sản phẩm
                $update_product = "UPDATE products p 
                                 SET rating = (
                                     SELECT AVG(rating) 
                                     FROM reviews 
                                     WHERE product_id = ?
                                 )
                                 WHERE id = ?";
                $stmt = mysqli_prepare($con, $update_product);
                mysqli_stmt_bind_param($stmt, "ii", $product_id, $product_id);
                mysqli_stmt_execute($stmt);
                
                echo json_encode(['success' => true, 'message' => 'Cảm ơn bạn đã đánh giá sản phẩm']);
            } else {
                error_log("Error adding review: " . mysqli_error($con));
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi thêm đánh giá']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ']);
            break;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}

// Hàm kiểm tra người dùng đã mua sản phẩm và đơn hàng đã hoàn thành chưa
function checkUserPurchased($user_id, $product_id) {
    global $con;
    $query = "SELECT 1 FROM orders o 
              JOIN order_items oi ON o.id = oi.order_id 
              WHERE o.user_id = ? 
              AND oi.product_id = ? 
              AND o.status = 'completed' 
              LIMIT 1";
              
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result) > 0;
}

// Hàm kiểm tra người dùng đã đánh giá sản phẩm chưa
function checkUserReviewed($user_id, $product_id) {
    global $con;
    $query = "SELECT 1 FROM reviews WHERE user_id = ? AND product_id = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result) > 0;
}
?>