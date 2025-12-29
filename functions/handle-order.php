<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include('../config/dbcon.php');
include('myfunctions.php');

header('Content-Type: application/json');

if(!isset($_SESSION['auth_user'])) {
    echo json_encode([
        'status' => 401,
        'message' => 'Vui lòng đăng nhập để thực hiện chức năng này'
    ]);
    exit();
}

// Xử lý hủy đơn hàng
if(isset($_POST['order_id']) && isset($_POST['cancel_reason'])) {
    $order_id = mysqli_real_escape_string($con, $_POST['order_id']);
    $cancel_reason = mysqli_real_escape_string($con, $_POST['cancel_reason']);
    $userId = $_SESSION['auth_user']['id'];

    // Kiểm tra đơn hàng tồn tại và quyền hủy
    $check_order = "SELECT * FROM orders WHERE id = '$order_id'";
    if (!isset($_SESSION['auth_user']['role_as']) || $_SESSION['auth_user']['role_as'] != 1) {
        $check_order .= " AND user_id = '$userId'";
    }
    $order_result = mysqli_query($con, $check_order);

    if (mysqli_num_rows($order_result) > 0) {
        $order = mysqli_fetch_assoc($order_result);
        
        // Kiểm tra trạng thái đơn hàng
        if ($order['status'] == 3) {
            echo json_encode([
                'status' => 400,
                'message' => 'Không thể hủy đơn hàng đã hoàn thành'
            ]);
            exit();
        }

        // Bắt đầu transaction
        mysqli_begin_transaction($con);
        try {
            // Cập nhật trạng thái đơn hàng thành "Đã hủy"
            $update_order = "UPDATE orders SET status='4', cancel_reason='$cancel_reason' WHERE id='$order_id'";
            mysqli_query($con, $update_order);

            // Lấy danh sách sản phẩm trong đơn hàng
            $get_items = "SELECT product_id, quantity FROM order_detail WHERE order_id='$order_id'";
            $items_result = mysqli_query($con, $get_items);

            // Cập nhật lại số lượng trong kho
            while($item = mysqli_fetch_assoc($items_result)) {
                $product_id = $item['product_id'];
                $qty = $item['quantity'];
                
                $update_stock = "UPDATE products SET qty = qty + $qty WHERE id='$product_id'";
                mysqli_query($con, $update_stock);
            }

            // Commit transaction
            mysqli_commit($con);
            
            echo json_encode([
                'status' => 200,
                'message' => 'Đơn hàng đã được hủy thành công'
            ]);
            exit();

        } catch(Exception $e) {
            // Rollback nếu có lỗi
            mysqli_rollback($con);
            echo json_encode([
                'status' => 500,
                'message' => 'Có lỗi xảy ra khi hủy đơn hàng'
            ]);
            exit();
        }
    } else {
        echo json_encode([
            'status' => 404,
            'message' => 'Không tìm thấy đơn hàng hoặc bạn không có quyền hủy đơn hàng này'
        ]);
        exit();
    }
}

// Xử lý cập nhật trạng thái đơn hàng (cho admin)
if(isset($_POST['update_order_btn'])) {
    $order_id = mysqli_real_escape_string($con, $_POST['order_id']);
    $status = mysqli_real_escape_string($con, $_POST['order_status']);

    // Kiểm tra quyền admin
    if(isset($_SESSION['auth_user']['role_as']) && $_SESSION['auth_user']['role_as'] == 1) {
        $update_query = "UPDATE orders SET status='$status' WHERE id='$order_id'";
        $update_query_run = mysqli_query($con, $update_query);

        if($update_query_run) {
            echo json_encode([
                'status' => 200,
                'message' => 'Cập nhật trạng thái đơn hàng thành công'
            ]);
        } else {
            echo json_encode([
                'status' => 500,
                'message' => 'Có lỗi xảy ra khi cập nhật trạng thái'
            ]);
        }
        exit();
    } else {
        echo json_encode([
            'status' => 403,
            'message' => 'Bạn không có quyền thực hiện thao tác này'
        ]);
        exit();
    }
}

// Nếu không có action nào được xử lý
echo json_encode([
    'status' => 400,
    'message' => 'Yêu cầu không hợp lệ'
]);
?> 