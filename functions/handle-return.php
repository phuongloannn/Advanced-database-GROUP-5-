<?php
session_start();
include('../config/dbcon.php');
include('myfunctions.php');

if(!isset($_SESSION['auth_user'])) {
    redirect("../login.php", "Vui lòng đăng nhập để thực hiện chức năng này");
}

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $orderId = mysqli_real_escape_string($con, $_POST['order_id']);
    $userId = $_SESSION['auth_user']['id'];
    $reason = mysqli_real_escape_string($con, $_POST['reason']);
    $requestType = mysqli_real_escape_string($con, $_POST['request_type']);

    // Kiểm tra đơn hàng
    $check_query = "SELECT * FROM orders WHERE id = ? AND user_id = ? AND status = 'completed'";
    $stmt = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($stmt, "ii", $orderId, $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if(mysqli_num_rows($result) > 0) {
        // Kiểm tra thời gian đổi trả (7 ngày kể từ ngày nhận hàng)
        $order = mysqli_fetch_assoc($result);
        $delivery_date = new DateTime($order['delivery_date']);
        $current_date = new DateTime();
        $interval = $current_date->diff($delivery_date);
        
        if($interval->days <= 7) {
            // Tạo yêu cầu trả/đổi hàng
            $insert_query = "INSERT INTO return_requests (order_id, user_id, reason, request_type) VALUES (?, ?, ?, ?)";
            $stmt = mysqli_prepare($con, $insert_query);
            mysqli_stmt_bind_param($stmt, "iiss", $orderId, $userId, $reason, $requestType);
            
            if(mysqli_stmt_execute($stmt)) {
                $return_id = mysqli_insert_id($con);
                
                // Xử lý upload ảnh
                $upload_dir = '../uploads/returns/';
                if (!file_exists($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                $upload_status = true;
                $uploaded_files = [];

                if(isset($_FILES['images'])) {
                    foreach($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                        if($_FILES['images']['error'][$key] == 0) {
                            $filename = $_FILES['images']['name'][$key];
                            $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                            
                            // Kiểm tra định dạng file
                            if(!in_array($filetype, ['jpg', 'jpeg', 'png', 'gif'])) {
                                $upload_status = false;
                                break;
                            }

                            // Kiểm tra kích thước file (5MB)
                            if($_FILES['images']['size'][$key] > 5 * 1024 * 1024) {
                                $upload_status = false;
                                break;
                            }

                            $newname = uniqid() . '_' . time() . '.' . $filetype;
                            $upload_path = $upload_dir . $newname;

                            if(move_uploaded_file($tmp_name, $upload_path)) {
                                $uploaded_files[] = $newname;
                            } else {
                                $upload_status = false;
                                break;
                            }
                        }
                    }
                }

                if($upload_status && !empty($uploaded_files)) {
                    // Lưu thông tin ảnh vào database
                    $image_query = "INSERT INTO return_images (return_id, image_path) VALUES (?, ?)";
                    $stmt = mysqli_prepare($con, $image_query);
                    
                    foreach($uploaded_files as $image) {
                        mysqli_stmt_bind_param($stmt, "is", $return_id, $image);
                        mysqli_stmt_execute($stmt);
                    }

                    // Cập nhật trạng thái đơn hàng
                    $update_order = "UPDATE orders SET status = 'returned' WHERE id = ?";
                    $stmt = mysqli_prepare($con, $update_order);
                    mysqli_stmt_bind_param($stmt, "i", $orderId);
                    mysqli_stmt_execute($stmt);

                    redirect("../my-orders.php", "Yêu cầu trả/đổi hàng đã được gửi thành công");
                } else {
                    // Xóa yêu cầu trả hàng nếu upload ảnh thất bại
                    $delete_query = "DELETE FROM return_requests WHERE id = ?";
                    $stmt = mysqli_prepare($con, $delete_query);
                    mysqli_stmt_bind_param($stmt, "i", $return_id);
                    mysqli_stmt_execute($stmt);

                    redirect("../my-orders.php", "Có lỗi xảy ra khi tải ảnh lên. Vui lòng thử lại");
                }
            } else {
                redirect("../my-orders.php", "Có lỗi xảy ra, vui lòng thử lại");
            }
        } else {
            redirect("../my-orders.php", "Đã quá thời hạn đổi trả (7 ngày kể từ ngày nhận hàng)");
        }
    } else {
        redirect("../my-orders.php", "Không tìm thấy đơn hàng hoặc đơn hàng không hợp lệ");
    }
} 