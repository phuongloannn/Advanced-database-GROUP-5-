<?php

session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_as']) || ($_SESSION['role_as'] != 1 && $_SESSION['role_as'] != 2)) {
    header('Location: ../../auth/login.php');
    exit();
}

require_once '../../includes/db.php';

// Lấy ID sản phẩm
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit();
}

try {
    $conn->begin_transaction();

    // Lấy thông tin sản phẩm để xóa ảnh
    $image_query = "SELECT image, image1, image2, image3 FROM products WHERE id = ?";
    $image_stmt = $conn->prepare($image_query);
    $image_stmt->bind_param("i", $id);
    $image_stmt->execute();
    $image_result = $image_stmt->get_result();
    $product_data = $image_result->fetch_assoc();

    // Cập nhật trạng thái các đơn hàng có sản phẩm này thành đã hủy (status = 4)
    $update_orders = "UPDATE orders o 
                     INNER JOIN order_detail od ON o.id = od.order_id 
                     SET o.status = 4 
                     WHERE od.product_id = ? AND o.status = 0";
    $update_stmt = $conn->prepare($update_orders);
    $update_stmt->bind_param("i", $id);
    $update_stmt->execute();

    // Xóa chi tiết đơn hàng liên quan đến sản phẩm
    $delete_order_details = "DELETE FROM order_detail WHERE product_id = ?";
    $delete_od_stmt = $conn->prepare($delete_order_details);
    $delete_od_stmt->bind_param("i", $id);
    $delete_od_stmt->execute();

    // Xóa sản phẩm
    $delete_product = "DELETE FROM products WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_product);
    $delete_stmt->bind_param("i", $id);
    $delete_stmt->execute();

    // Xóa các file ảnh
    $image_fields = ['image', 'image1', 'image2', 'image3'];
    foreach ($image_fields as $field) {
        if (!empty($product_data[$field])) {
            $file_path = "../../anh_xedap/" . $product_data[$field];
            if (file_exists($file_path)) {
                unlink($file_path);
            }
        }
    }

    $conn->commit();
    header('Location: index.php?msg=delete_success');
    exit();

} catch (Exception $e) {
    $conn->rollback();
    header('Location: index.php?msg=delete_error');
    exit();
}
