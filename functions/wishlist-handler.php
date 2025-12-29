<?php
session_start();
include('../config/dbcon.php');

if (!isset($_SESSION['auth'])) {
    echo json_encode([
        'status' => 401,
        'message' => 'Vui lòng đăng nhập để thực hiện chức năng này'
    ]);
    exit;
}

if (isset($_POST['prod_id']) && isset($_POST['action'])) {
    $prod_id = mysqli_real_escape_string($con, $_POST['prod_id']);
    $user_id = $_SESSION['auth_user']['user_id'];
    $action = $_POST['action'];

    // Check if product exists
    $product_query = "SELECT * FROM products WHERE id = ?";
    $stmt = mysqli_prepare($con, $product_query);
    mysqli_stmt_bind_param($stmt, "i", $prod_id);
    mysqli_stmt_execute($stmt);
    $product_result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($product_result) > 0) {
        if ($action === 'add') {
            // Check if already in wishlist
            $check_query = "SELECT * FROM wishlist WHERE user_id = ? AND prod_id = ?";
            $stmt = mysqli_prepare($con, $check_query);
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $prod_id);
            mysqli_stmt_execute($stmt);
            $check_result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($check_result) > 0) {
                echo json_encode([
                    'status' => 409,
                    'message' => 'Sản phẩm đã có trong danh sách yêu thích'
                ]);
            } else {
                $insert_query = "INSERT INTO wishlist (user_id, prod_id) VALUES (?, ?)";
                $stmt = mysqli_prepare($con, $insert_query);
                mysqli_stmt_bind_param($stmt, "ii", $user_id, $prod_id);
                
                if (mysqli_stmt_execute($stmt)) {
                    echo json_encode([
                        'status' => 200,
                        'message' => 'Đã thêm vào danh sách yêu thích'
                    ]);
                } else {
                    echo json_encode([
                        'status' => 500,
                        'message' => 'Đã xảy ra lỗi, vui lòng thử lại'
                    ]);
                }
            }
        } elseif ($action === 'remove') {
            $delete_query = "DELETE FROM wishlist WHERE user_id = ? AND prod_id = ?";
            $stmt = mysqli_prepare($con, $delete_query);
            mysqli_stmt_bind_param($stmt, "ii", $user_id, $prod_id);
            
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode([
                    'status' => 200,
                    'message' => 'Đã xóa khỏi danh sách yêu thích'
                ]);
            } else {
                echo json_encode([
                    'status' => 500,
                    'message' => 'Đã xảy ra lỗi, vui lòng thử lại'
                ]);
            }
        }
    } else {
        echo json_encode([
            'status' => 404,
            'message' => 'Sản phẩm không tồn tại'
        ]);
    }
} else {
    echo json_encode([
        'status' => 400,
        'message' => 'Yêu cầu không hợp lệ'
    ]);
}
?>