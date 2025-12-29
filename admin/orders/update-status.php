<?php
session_start();
require_once '../../config/dbcon.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['auth_user']) || !isset($_SESSION['auth_user']['role_as']) || $_SESSION['auth_user']['role_as'] != 1) {
    echo json_encode(['status' => 'error', 'message' => 'Không có quyền truy cập']);
    exit;
}

// Kiểm tra method và dữ liệu
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Phương thức không hợp lệ']);
    exit;
}

$order_id = isset($_POST['order_id']) ? intval($_POST['order_id']) : 0;
$new_status = isset($_POST['status']) ? intval($_POST['status']) : -1;

// Validate dữ liệu - status phải là số từ 0-4
if ($order_id <= 0 || !in_array($new_status, [0, 1, 2, 3, 4])) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Trạng thái đơn hàng không hợp lệ. Status: ' . $new_status . ', Order ID: ' . $order_id
    ]);
    exit;
}

try {
    // Cập nhật trạng thái và thời gian cập nhật
    $query = "UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?";
    $stmt = mysqli_prepare($con, $query);
    
    if (!$stmt) {
        throw new Exception("Lỗi chuẩn bị query: " . mysqli_error($con));
    }
    
    mysqli_stmt_bind_param($stmt, 'ii', $new_status, $order_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Lấy số lượng đơn hàng theo trạng thái (số)
        $count_query = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
        $count_result = mysqli_query($con, $count_query);
        
        // ĐÃ SỬA: mapping đúng thứ tự
        $status_counts = [
            0 => 0, // Chờ xử lý
            1 => 0, // Đang xử lý
            2 => 0, // Đang giao
            3 => 0, // Hoàn thành
            4 => 0  // Đã hủy
        ];
        
        if ($count_result) {
            while ($row = mysqli_fetch_assoc($count_result)) {
                $status_counts[$row['status']] = $row['count'];
            }
        }
        
        // ĐÃ SỬA: hàm lấy màu sắc cho status mới
        function getStatusBgColor($status) {
            switch($status) {
                case 0: return '#fff3cd'; // Chờ xử lý - vàng nhạt
                case 1: return '#cff4fc'; // Đang xử lý - xanh nhạt
                case 2: return '#cfe2ff'; // Đang giao - xanh dương nhạt
                case 3: return '#d1e7dd'; // Hoàn thành - xanh lá nhạt
                case 4: return '#f8d7da'; // Đã hủy - đỏ nhạt
                default: return '#ffffff';
            }
        }
        
        function getStatusColor($status) {
            switch($status) {
                case 0: return '#856404'; // Chờ xử lý - vàng đậm
                case 1: return '#055160'; // Đang xử lý - xanh đậm
                case 2: return '#084298'; // Đang giao - xanh dương đậm
                case 3: return '#0f5132'; // Hoàn thành - xanh lá đậm
                case 4: return '#721c24'; // Đã hủy - đỏ đậm
                default: return '#000000';
            }
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Cập nhật trạng thái thành công',
            'status_counts' => $status_counts,
            'bg_color' => getStatusBgColor($new_status),
            'text_color' => getStatusColor($new_status)
        ]);
    } else {
        throw new Exception("Không thể cập nhật trạng thái đơn hàng: " . mysqli_error($con));
    }
    
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
    ]);
}
?>