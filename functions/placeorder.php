<?php
session_start();
include('../config/dbcon.php');

if(isset($_SESSION['auth']))
{
    if(isset($_POST['placeOrderBtn']))
    {
        $name = mysqli_real_escape_string($con, $_POST['name']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $phone = mysqli_real_escape_string($con, $_POST['phone']);
        $address = mysqli_real_escape_string($con, $_POST['address']);
        $payment_mode = mysqli_real_escape_string($con, $_POST['payment_mode']);
        $payment_id = mysqli_real_escape_string($con, $_POST['payment_id']);

        if($name == "" || $email == "" || $phone == "" || $address == "")
        {
            $_SESSION['message'] = "Vui lòng điền đầy đủ thông tin";
            header('Location: ../checkout.php');
            exit();
        }

        $userId = $_SESSION['auth_user']['id'];
        $query = "SELECT c.*, p.* FROM carts c, products p WHERE c.prod_id=p.id AND c.user_id='$userId' ORDER BY c.id DESC";
        $query_run = mysqli_query($con, $query);

        $totalPrice = 0;
        foreach($query_run as $citem)
        {
            $totalPrice += $citem['selling_price'] * $citem['prod_qty'];
        }

        $tracking_no = "Q-FASHION".rand(1111,9999).substr($phone,2);
        
        // Bắt đầu transaction
        mysqli_begin_transaction($con);
        
        try {
            // Insert vào bảng orders với trạng thái ban đầu là 0 (Chờ xác nhận)
            $insert_query = "INSERT INTO orders (tracking_no, user_id, name, email, phone, address, total_price, payment_mode, payment_id, status) 
                           VALUES ('$tracking_no', '$userId', '$name', '$email', '$phone', '$address', '$totalPrice', '$payment_mode', '$payment_id', '0')";
            $insert_query_run = mysqli_query($con, $insert_query);

            if($insert_query_run)
            {
                $order_id = mysqli_insert_id($con);
                foreach($query_run as $citem)
                {
                    $prod_id = $citem['prod_id'];
                    $prod_qty = $citem['prod_qty'];
                    $price = $citem['selling_price'];
                    
                    // Insert vào bảng order_detail
                    $insert_items_query = "INSERT INTO order_detail (order_id, product_id, quantity, selling_price) 
                                         VALUES ('$order_id', '$prod_id', '$prod_qty', '$price')";
                    $insert_items_query_run = mysqli_query($con, $insert_items_query);

                    // Cập nhật số lượng sản phẩm trong kho
                    $product_query = "UPDATE products SET qty=qty-$prod_qty WHERE id='$prod_id'";
                    $product_query_run = mysqli_query($con, $product_query);
                }

                // Xóa giỏ hàng sau khi đặt hàng thành công
                $deleteCartQuery = "DELETE FROM carts WHERE user_id='$userId'";
                $deleteCartQuery_run = mysqli_query($con, $deleteCartQuery);

                mysqli_commit($con);

                $_SESSION['message'] = "Đặt hàng thành công! Mã đơn hàng của bạn là $tracking_no";
                header('Location: ../my-orders.php');
                exit();
            }
        }
        catch (Exception $e) {
            mysqli_rollback($con);
            $_SESSION['message'] = "Đã xảy ra lỗi khi đặt hàng";
            header('Location: ../checkout.php');
            exit();
        }
    }
}
else
{
    header('Location: ../index.php');
}
?> 