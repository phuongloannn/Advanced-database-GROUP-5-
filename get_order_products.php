<?php
session_start();
include('config/dbcon.php');

if (!isset($_SESSION['auth_user']) || !isset($_GET['order_id'])) {
    http_response_code(403);
    exit();
}

$user_id = $_SESSION['auth_user']['id'];
$order_id = mysqli_real_escape_string($con, $_GET['order_id']);

// Kiểm tra đơn hàng có thuộc về user không
$check_order = "SELECT * FROM orders WHERE id = '$order_id' AND user_id = '$user_id'";
$check_order_run = mysqli_query($con, $check_order);

if (mysqli_num_rows($check_order_run) > 0) {
    // Lấy danh sách sản phẩm trong đơn hàng chưa được đánh giá
    $products_query = "SELECT p.id, p.name, p.image 
                      FROM order_detail od 
                      JOIN products p ON od.product_id = p.id 
                      WHERE od.order_id = '$order_id'
                      AND NOT EXISTS (
                          SELECT 1 FROM reviews r 
                          WHERE r.order_id = od.order_id 
                          AND r.product_id = od.product_id
                          AND r.user_id = '$user_id'
                      )";
    
    $products_result = mysqli_query($con, $products_query);
    $products = array();
    
    while ($row = mysqli_fetch_assoc($products_result)) {
        $products[] = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'image' => $row['image']
        );
    }
    
    header('Content-Type: application/json');
    echo json_encode($products);
} else {
    http_response_code(403);
    exit();
}
?> 