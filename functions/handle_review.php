<?php
session_start();
include('../config/dbcon.php');
include('userfunctions.php');

header('Content-Type: application/json');

if(!isset($_SESSION['auth_user'])) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập']);
    exit;
}

$user_id = $_SESSION['auth_user']['id'];

if($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch($action) {
        case 'toggle_helpful':
            $review_id = $_POST['review_id'] ?? 0;
            
            if(!$review_id) {
                echo json_encode(['success' => false, 'message' => 'Thiếu thông tin đánh giá']);
                exit;
            }
            
            // Kiểm tra xem đã đánh dấu hữu ích chưa
            $is_helpful = hasMarkedHelpful($review_id, $user_id);
            
            if($is_helpful) {
                // Nếu đã đánh dấu thì xóa
                updateHelpfulCount($review_id, $user_id, 'remove');
                echo json_encode(['success' => true, 'added' => false]);
            } else {
                // Nếu chưa đánh dấu thì thêm
                updateHelpfulCount($review_id, $user_id, 'add');
                echo json_encode(['success' => true, 'added' => true]);
            }
            break;
            
        default:
            // Xử lý thêm đánh giá mới
            $product_id = $_POST['product_id'] ?? 0;
            $rating = $_POST['rating'] ?? 0;
            $comment = $_POST['comment'] ?? '';
            
            if(!$product_id || !$rating || !$comment) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin']);
                exit;
            }
            
            // Kiểm tra xem người dùng đã mua sản phẩm chưa
            $has_purchased = checkUserPurchased($user_id, $product_id);
            if(!$has_purchased) {
                echo json_encode(['success' => false, 'message' => 'Bạn cần mua sản phẩm để đánh giá']);
                exit;
            }
            
            // Kiểm tra xem đã đánh giá chưa
            $has_reviewed = checkUserReviewed($user_id, $product_id);
            if($has_reviewed) {
                echo json_encode(['success' => false, 'message' => 'Bạn đã đánh giá sản phẩm này']);
                exit;
            }
            
            // Xử lý upload ảnh
            $images = [];
            if(!empty($_FILES['review_images'])) {
                $file_count = count($_FILES['review_images']['name']);
                
                if($file_count > 5) {
                    echo json_encode(['success' => false, 'message' => 'Chỉ được upload tối đa 5 ảnh']);
                    exit;
                }
                
                for($i = 0; $i < $file_count; $i++) {
                    if($_FILES['review_images']['error'][$i] === UPLOAD_ERR_OK) {
                        $tmp_name = $_FILES['review_images']['tmp_name'][$i];
                        $name = $_FILES['review_images']['name'][$i];
                        $extension = pathinfo($name, PATHINFO_EXTENSION);
                        
                        // Kiểm tra định dạng file
                        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                        if(!in_array(strtolower($extension), $allowed)) {
                            echo json_encode(['success' => false, 'message' => 'Chỉ chấp nhận file ảnh (jpg, jpeg, png, gif)']);
                            exit;
                        }
                        
                        // Tạo tên file mới
                        $new_name = uniqid() . '.' . $extension;
                        $upload_path = '../uploads/reviews/' . $new_name;
                        
                        if(move_uploaded_file($tmp_name, $upload_path)) {
                            $images[] = $new_name;
                        }
                    }
                }
            }
            
            // Thêm đánh giá vào database
            $images_str = !empty($images) ? implode(',', $images) : null;
            $query = "INSERT INTO reviews (product_id, user_id, rating, comment, images, created_at) 
                     VALUES (?, ?, ?, ?, ?, NOW())";
            
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "iiiss", $product_id, $user_id, $rating, $comment, $images_str);
            
            if(mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Có lỗi xảy ra khi thêm đánh giá']);
            }
            break;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Phương thức không được hỗ trợ']);
}

// Hàm kiểm tra người dùng đã mua sản phẩm chưa
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