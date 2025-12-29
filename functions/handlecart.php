<?php
session_start();
include('../config/dbcon.php');

// Đảm bảo response là JSON
header('Content-Type: application/json');

function sendJsonResponse($status, $message, $data = null) {
    $response = [
        'status' => $status,
        'message' => $message
    ];
    if ($data !== null) {
        $response['data'] = $data;
    }
    echo json_encode($response);
    exit();
}

// Kiểm tra kết nối database
if (!$con) {
    sendJsonResponse(500, "Lỗi kết nối database: " . mysqli_connect_error());
}

// Kiểm tra session
if(!isset($_SESSION['auth'])) {
    sendJsonResponse(401, "Vui lòng đăng nhập để thực hiện thao tác này");
}

if(isset($_POST['scope'])) {
    $scope = $_POST['scope'];
    switch($scope) {
        case "add":
            if(!isset($_POST['prod_id']) || !isset($_POST['prod_qty'])) {
                sendJsonResponse(400, "Thiếu thông tin sản phẩm");
            }

            $prod_id = mysqli_real_escape_string($con, $_POST['prod_id']);
            $prod_qty = (int)$_POST['prod_qty'];

            if($prod_qty <= 0) {
                sendJsonResponse(400, "Số lượng không hợp lệ");
            }

            // Kiểm tra sản phẩm tồn tại và còn hàng
            $query = "SELECT * FROM products WHERE id=? AND status='0'";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "s", $prod_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if($result && mysqli_num_rows($result) > 0) {
                $product = mysqli_fetch_array($result);
                
                if($product['qty'] >= $prod_qty) {
                    if(!isset($_SESSION['cart'])) {
                        $_SESSION['cart'] = array();
                    }

                    // Kiểm tra nếu sản phẩm đã có trong giỏ hàng
                    if(isset($_SESSION['cart'][$prod_id])) {
                        $newQty = $_SESSION['cart'][$prod_id]['quantity'] + $prod_qty;
                        if($product['qty'] >= $newQty) {
                            $_SESSION['cart'][$prod_id]['quantity'] = $newQty;
                            sendJsonResponse(201, "Đã cập nhật số lượng sản phẩm trong giỏ hàng");
                        } else {
                            sendJsonResponse(400, "Chỉ còn {$product['qty']} sản phẩm trong kho");
                        }
                    } else {
                        $_SESSION['cart'][$prod_id] = [
                            'id' => $prod_id,
                            'name' => $product['name'],
                            'price' => $product['selling_price'],
                            'image' => $product['image'],
                            'quantity' => $prod_qty
                        ];
                        sendJsonResponse(201, "Đã thêm sản phẩm vào giỏ hàng");
                    }
                } else {
                    sendJsonResponse(400, "Chỉ còn {$product['qty']} sản phẩm trong kho");
                }
            } else {
                sendJsonResponse(404, "Sản phẩm không tồn tại hoặc đã hết hàng");
            }
            break;

        case "update":
            if(!isset($_POST['prod_id']) || !isset($_POST['prod_qty'])) {
                sendJsonResponse(400, "Thiếu thông tin cập nhật");
            }

            $prod_id = mysqli_real_escape_string($con, $_POST['prod_id']);
            $prod_qty = (int)$_POST['prod_qty'];

            if($prod_qty <= 0) {
                sendJsonResponse(400, "Số lượng không hợp lệ");
            }

            // Kiểm tra sản phẩm trong giỏ hàng
            if(!isset($_SESSION['cart'][$prod_id])) {
                sendJsonResponse(404, "Sản phẩm không có trong giỏ hàng");
            }

            // Kiểm tra số lượng trong kho
            $query = "SELECT qty FROM products WHERE id=? AND status='0'";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "s", $prod_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if($result && mysqli_num_rows($result) > 0) {
                $product = mysqli_fetch_array($result);
                if($product['qty'] >= $prod_qty) {
                    $_SESSION['cart'][$prod_id]['quantity'] = $prod_qty;
                    sendJsonResponse(200, "Đã cập nhật số lượng");
                } else {
                    sendJsonResponse(400, "Chỉ còn {$product['qty']} sản phẩm trong kho");
                }
            } else {
                sendJsonResponse(404, "Sản phẩm không tồn tại hoặc đã hết hàng");
            }
            break;

        case "delete":
            if(!isset($_POST['prod_id'])) {
                if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 400, 'message' => "Thiếu thông tin sản phẩm"]);
                } else {
                    $_SESSION['message'] = "Thiếu thông tin sản phẩm";
                    header('Location: ../cart.php');
                }
                exit();
            }
            
            $prod_id = mysqli_real_escape_string($con, $_POST['prod_id']);
            
            if(isset($_SESSION['cart'][$prod_id])) {
                $product_name = $_SESSION['cart'][$prod_id]['name'];
                unset($_SESSION['cart'][$prod_id]);
                
                if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 200, 'message' => "Đã xóa sản phẩm '$product_name' khỏi giỏ hàng"]);
                } else {
                    $_SESSION['message'] = "Đã xóa sản phẩm '$product_name' khỏi giỏ hàng";
                    header('Location: ../cart.php');
                }
            } else {
                if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    header('Content-Type: application/json');
                    echo json_encode(['status' => 404, 'message' => "Sản phẩm không có trong giỏ hàng"]);
                } else {
                    $_SESSION['message'] = "Sản phẩm không có trong giỏ hàng";
                    header('Location: ../cart.php');
                }
            }
            exit();
            break;

        default:
            if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['status' => 400, 'message' => "Yêu cầu không hợp lệ"]);
            } else {
                $_SESSION['message'] = "Yêu cầu không hợp lệ";
                header('Location: ../cart.php');
            }
            exit();
            break;
    }
} else {
    if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode(['status' => 400, 'message' => "Yêu cầu không hợp lệ"]);
    } else {
        $_SESSION['message'] = "Yêu cầu không hợp lệ";
        header('Location: ../cart.php');
    }
    exit();
}
?> 