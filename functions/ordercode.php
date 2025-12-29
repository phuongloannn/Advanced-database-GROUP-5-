<?php
session_start();
include("../config/dbcon.php");
include("../functions/myfunctions.php");

// Xử lý xóa sản phẩm khỏi giỏ hàng (session)
if (isset($_GET['deleteID'])) {
    $product_id = intval($_GET['deleteID']);
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header("Location: ../cart.php");
    exit();
}

// Xử lý cập nhật số lượng sản phẩm trong giỏ hàng (session)
if (isset($_POST['update_product']) && isset($_POST['product_id']) && isset($_POST['quantity'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = intval($_POST['quantity']);
    if ($quantity > 0 && isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
    }
    header("Location: ../cart.php");
    exit();
}

// ...các xử lý khác...

if (isset($_POST['order'])) {
    $user_id    = $_SESSION['auth_user']['id'];
    $product_id = $_POST['product_id'];
    $quantity   = $_POST['quantity'];

    $product = getByID("products", $product_id);
    if (mysqli_num_rows($product) > 0) {
        $product = mysqli_fetch_array($product);
        $slug    = $product['slug'];
        if ($quantity != "" && $quantity <= $product['qty']) {
            $selling_price  = $product['selling_price'];
            $insert_query   = "INSERT INTO order_detail (`user_id`, `product_id`, `selling_price`, `quantity`) 
                               VALUES ('$user_id', '$product_id', '$selling_price', '$quantity')";
            $insert_query_run = mysqli_query($con, $insert_query);
            if ($insert_query_run) {
                $_SESSION['message'] = "Thêm vào giỏ hàng thành công";
                header("Location: ../product-detail.php?slug=$slug");
                exit;
            }
        } else {
            $_SESSION['message'] = "Số lượng sản phẩm không phù hợp";
            header("Location: ../product-detail.php?slug=$slug");
            exit;
        }
    } else {
        $_SESSION['message'] = "Đã xảy ra lỗi không đáng có";
        header("Location: ../products.php");
        exit;
    }
} else if (isset($_GET['deleteID'])) {
    $user_id    = $_SESSION['auth_user']['id'];
    $order_id   = $_GET['deleteID'];
    $query = "DELETE FROM `order_detail` 
              WHERE `id` = '$order_id' AND `user_id` = '$user_id'";
    mysqli_query($con, $query);
    $_SESSION['message'] = "Xóa sản phẩm thành công";
    header("Location: ../cart.php");
    exit;
} else if (isset($_POST['update_product'])) {
    $user_id    = $_SESSION['auth_user']['id'];
    $product_id = $_POST['product_id'];
    $quantity   = $_POST['quantity'];

    // Lấy số lượng sản phẩm hiện có trong kho
    $query          = "SELECT `qty` FROM `products` WHERE `id` = '$product_id'";
    $total_quantity = mysqli_fetch_array(mysqli_query($con, $query))['qty'];

    // Kiểm tra số lượng có đủ không
    if ($total_quantity >= $quantity) {
        $query = "UPDATE `order_detail` SET `quantity` = $quantity 
                  WHERE `product_id` = '$product_id' AND `user_id` = '$user_id' AND `status` = '1'";
        mysqli_query($con, $query);
        $_SESSION['message'] = "Cập nhập sản phẩm thành công";
    } else {
        $_SESSION['message'] = "Cập nhập số lượng sản phẩm quá lớn";
    }

    header("Location: ../cart.php");
    exit;
} else if (isset($_POST['buy_product'])) {
    if (!isset($_SESSION['auth_user'])) {
        $_SESSION['message'] = "Vui lòng đăng nhập để đặt hàng";
        header("Location: ../cart.php");
        exit();
    }

    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart']) || empty($_SESSION['cart'])) {
        $_SESSION['message'] = "Giỏ hàng của bạn đang trống";
        header("Location: ../cart.php");
        exit();
    }

    $user_id = $_SESSION['auth_user']['id'];
    $payment_method = $_POST['payment_method'];
    
    // Tính tổng giá trị đơn hàng
    $total_price = 0;
    foreach ($_SESSION['cart'] as $product_id => $item) {
        if (isset($item['price']) && isset($item['quantity'])) {
            $total_price += floatval($item['price']) * intval($item['quantity']);
        }
    }

    // Tạo đơn hàng mới
    $insert_order = "INSERT INTO orders (user_id, payment_method, total_price, status) 
                     VALUES ('$user_id', '$payment_method', $total_price, 'preparing')";
    $insert_order_run = mysqli_query($con, $insert_order);

    if ($insert_order_run) {
        $order_id = mysqli_insert_id($con);

        // Chuyển các sản phẩm từ giỏ hàng vào chi tiết đơn hàng
        foreach ($_SESSION['cart'] as $product_id => $item) {
            if (isset($item['price']) && isset($item['quantity'])) {
                $quantity = intval($item['quantity']);
                $price = floatval($item['price']);
                
                $insert_items = "INSERT INTO order_detail (order_id, product_id, quantity, selling_price) 
                                VALUES ('$order_id', '$product_id', '$quantity', '$price')";
                mysqli_query($con, $insert_items);

                // Cập nhật số lượng sản phẩm trong kho
                mysqli_query($con, "UPDATE products SET qty = qty - $quantity WHERE id = '$product_id'");
            }
        }

        // Xóa giỏ hàng sau khi đặt hàng thành công
        unset($_SESSION['cart']);

        if ($payment_method == 'cod') {
            // Thanh toán khi nhận hàng
            $_SESSION['message'] = "Đặt hàng thành công! Bạn có thể xem chi tiết đơn hàng trong trang cá nhân.";
            header("Location: ../user.php");
            exit();
        } 
        else if ($payment_method == 'vnpay') {
            include('../vnpay_php/config.php');
            
            $vnp_TxnRef = $order_id;
            $vnp_OrderInfo = 'Thanh toan don hang #' . $order_id;
            $vnp_OrderType = 'billpayment';
            $vnp_Amount = $total_price * 100;
            $vnp_Locale = 'vn';
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
            
            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
                "vnp_ExpireDate" => date('YmdHis', strtotime('+15 minutes'))
            );

            ksort($inputData);
            $query = "";
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($hashdata != "") {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

            header("Location: $vnp_Url");
            exit();
        }
    } else {
        $_SESSION['message'] = "Đã xảy ra lỗi khi đặt hàng. Vui lòng thử lại!";
        header("Location: ../cart.php");
        exit();
    }
} else if (isset($_POST['rate'])) {
    $user_id    = $_SESSION['auth_user']['id'];
    $id         = $_POST['id'];
    $rate       = $_POST['rating'];
    $comment    = $_POST['comment'];

    $query = "UPDATE `order_detail` SET `rate` = '$rate', `comment` = '$comment'
              WHERE `id` = '$id' AND `user_id` = '$user_id' AND `status` = '4'";
    mysqli_query($con, $query);

    $_SESSION['message'] = "Đánh giá sản phẩm thành công";
    header("Location: ../cart-status.php");
    exit;
}

// Xử lý thanh toán đơn hàng
if (isset($_POST['process_payment'])) {
    $order_id = intval($_POST['order_id']);
    $payment_method = $_POST['payment_method'];
    $user_id = $_SESSION['auth_user']['id'];

    // Kiểm tra đơn hàng tồn tại và thuộc về user
    $check_order = "SELECT * FROM orders WHERE id='$order_id' AND user_id='$user_id' AND status=0";
    $check_order_run = mysqli_query($con, $check_order);

    if (mysqli_num_rows($check_order_run) > 0) {
        $order = mysqli_fetch_assoc($check_order_run);

        // Cập nhật phương thức thanh toán
        $update_payment = "UPDATE orders SET payment_method='$payment_method' WHERE id='$order_id'";
        mysqli_query($con, $update_payment);

        if ($payment_method == 'cod') {
            $_SESSION['message'] = "Đã cập nhật phương thức thanh toán khi nhận hàng";
            header("Location: ../user.php");
            exit();
        } else if ($payment_method == 'vnpay') {
            include('../vnpay_php/config.php');

            $amount = intval($_POST['amount']);
            
            $vnp_TxnRef = $order_id;
            $vnp_OrderInfo = 'Thanh toán đơn hàng #' . $order_id;
            $vnp_OrderType = 'billpayment';
            $vnp_Amount = $amount * 100;
            $vnp_Locale = 'vn';
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];

            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef,
                "vnp_ExpireDate" => date('YmdHis', strtotime('+15 minutes'))
            );

            ksort($inputData);
            $query = "";
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($hashdata != "") {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;

            header("Location: $vnp_Url");
            exit();
        }
    } else {
        $_SESSION['message'] = "Không tìm thấy đơn hàng hoặc đơn hàng không hợp lệ";
        header("Location: ../user.php");
        exit();
    }
}

// Xử lý hủy đơn hàng
if(isset($_POST['cancel_order']) && isset($_POST['order_id'])) {
    $order_id = mysqli_real_escape_string($con, $_POST['order_id']);
    $user_id = $_SESSION['auth_user']['id'];
    $cancel_reason = mysqli_real_escape_string($con, $_POST['cancel_reason']);

    // Kiểm tra đơn hàng có thuộc về user không
    $check_order = "SELECT * FROM orders WHERE id='$order_id' AND user_id='$user_id' AND status=0";
    $check_order_run = mysqli_query($con, $check_order);

    if(mysqli_num_rows($check_order_run) > 0) {
        // Cập nhật trạng thái đơn hàng thành đã hủy
        $update_order = "UPDATE orders SET status='cancelled', cancel_reason='$cancel_reason' WHERE id='$order_id'";
        $update_order_run = mysqli_query($con, $update_order);
        
        if($update_order_run) {
            // Hoàn lại số lượng sản phẩm vào kho
            $get_items = "SELECT product_id, quantity FROM order_detail WHERE order_id='$order_id'";
            $items_run = mysqli_query($con, $get_items);
            while($item = mysqli_fetch_assoc($items_run)) {
                $product_id = $item['product_id'];
                $quantity = $item['quantity'];
                mysqli_query($con, "UPDATE products SET qty = qty + $quantity WHERE id='$product_id'");
            }
            
            $_SESSION['message'] = "Đơn hàng đã được hủy thành công";
        } else {
            $_SESSION['message'] = "Có lỗi xảy ra khi hủy đơn hàng";
        }
    } else {
        $_SESSION['message'] = "Không tìm thấy đơn hàng hoặc bạn không có quyền hủy đơn hàng này";
    }
    
    header("Location: ../user.php");
    exit();
}

// Xử lý xóa đơn hàng
if(isset($_POST['delete_order'])) {
    $order_id = mysqli_real_escape_string($con, $_POST['order_id']);
    $user_id = $_SESSION['auth_user']['id'];

    // Kiểm tra đơn hàng có thuộc về user không
    $check_order = "SELECT * FROM orders WHERE id='$order_id' AND user_id='$user_id'";
    $check_order_run = mysqli_query($con, $check_order);

    if(mysqli_num_rows($check_order_run) > 0) {
        // Xóa chi tiết đơn hàng
        $delete_details = "DELETE FROM order_detail WHERE order_id='$order_id'";
        mysqli_query($con, $delete_details);

        // Xóa đơn hàng
        $delete_order = "DELETE FROM orders WHERE id='$order_id'";
        $delete_order_run = mysqli_query($con, $delete_order);
        
        if($delete_order_run) {
            $_SESSION['message'] = "Đã xóa đơn hàng thành công";
        } else {
            $_SESSION['message'] = "Có lỗi xảy ra khi xóa đơn hàng";
        }
    } else {
        $_SESSION['message'] = "Không tìm thấy đơn hàng hoặc bạn không có quyền xóa đơn hàng này";
    }
    
    header("Location: ../user.php");
    exit();
}

// Xử lý đánh giá sản phẩm
if (isset($_POST['submit_review'])) {
    header('Content-Type: application/json');
    
    if (!isset($_SESSION['auth_user'])) {
        echo json_encode([
            'success' => false,
            'message' => "Vui lòng đăng nhập để đánh giá sản phẩm"
        ]);
        exit();
    }

    $user_id = mysqli_real_escape_string($con, $_POST['user_id']);
    $order_id = mysqli_real_escape_string($con, $_POST['order_id']);
    $product_id = mysqli_real_escape_string($con, $_POST['product_id']);
    $rating = mysqli_real_escape_string($con, $_POST['rating']);
    $comment = mysqli_real_escape_string($con, $_POST['comment']);

    // Kiểm tra dữ liệu đầu vào
    if (empty($rating) || empty($comment) || empty($product_id) || empty($order_id) || empty($user_id)) {
        echo json_encode([
            'success' => false,
            'message' => "Vui lòng điền đầy đủ thông tin"
        ]);
        exit();
    }

    // Kiểm tra đơn hàng có thuộc về user và đã hoàn thành
    $check_order = "SELECT * FROM orders o 
                    JOIN order_detail od ON o.id = od.order_id 
                    WHERE o.id = '$order_id' 
                    AND o.user_id = '$user_id' 
                    AND od.product_id = '$product_id' 
                    AND o.status = 'completed'";
    $check_order_run = mysqli_query($con, $check_order);

    if (mysqli_num_rows($check_order_run) == 0) {
        echo json_encode([
            'success' => false,
            'message' => "Đơn hàng không hợp lệ hoặc chưa hoàn thành"
        ]);
        exit();
    }

    // Kiểm tra xem đã đánh giá sản phẩm này trong đơn hàng chưa
    $check_review = "SELECT * FROM reviews 
                    WHERE user_id = '$user_id' 
                    AND product_id = '$product_id' 
                    AND order_id = '$order_id'";
    $check_review_run = mysqli_query($con, $check_review);

    if (mysqli_num_rows($check_review_run) > 0) {
        echo json_encode([
            'success' => false,
            'message' => "Bạn đã đánh giá sản phẩm này trong đơn hàng rồi"
        ]);
        exit();
    }

    // Thêm đánh giá mới
    $insert_review = "INSERT INTO reviews (user_id, product_id, order_id, rating, comment, created_at) 
                      VALUES ('$user_id', '$product_id', '$order_id', '$rating', '$comment', NOW())";
    $insert_review_run = mysqli_query($con, $insert_review);

    if ($insert_review_run) {
        // Cập nhật điểm đánh giá trung bình của sản phẩm
        $update_avg = "UPDATE products p 
                       SET p.rating = (
                           SELECT AVG(r.rating) 
                           FROM reviews r 
                           WHERE r.product_id = p.id
                       )
                       WHERE p.id = '$product_id'";
        mysqli_query($con, $update_avg);

        // Lấy lại số đánh giá và điểm trung bình
        $rating_query = "SELECT COUNT(*) as total_reviews, AVG(rating) as avg_rating 
                        FROM reviews WHERE product_id = '$product_id'";
        $rating_result = mysqli_query($con, $rating_query);
        $rating_data = mysqli_fetch_assoc($rating_result);

        echo json_encode([
            'success' => true,
            'message' => "Cảm ơn bạn đã đánh giá sản phẩm!",
            'average_rating' => round($rating_data['avg_rating'], 1),
            'total_reviews' => $rating_data['total_reviews']
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => "Có lỗi xảy ra khi lưu đánh giá"
        ]);
    }
    exit();
}
?>