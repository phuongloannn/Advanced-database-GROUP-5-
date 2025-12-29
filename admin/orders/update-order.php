<?php
session_start();
include('../../config/dbcon.php');
include('../../functions/myfunctions.php');

if(isset($_SESSION['auth_user']) && $_SESSION['auth_user']['role_as'] == 1)
{
    if(isset($_POST['order_id']) && isset($_POST['status']))
    {
        $order_id = mysqli_real_escape_string($con, $_POST['order_id']);
        $status = mysqli_real_escape_string($con, $_POST['status']);

        // Cập nhật trạng thái đơn hàng
        $update_query = "UPDATE orders SET status='$status' WHERE id='$order_id'";
        $update_query_run = mysqli_query($con, $update_query);

        if($update_query_run)
        {
            echo 'success';
        }
        else
        {
            echo 'error';
        }
    }
    else
    {
        echo 'missing_params';
    }
}
else
{
    echo 'unauthorized';
}
?> 