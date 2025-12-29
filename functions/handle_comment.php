<?php
session_start();
include('../config/dbcon.php');
include('myfunctions.php');

if(!isset($_SESSION['auth_user'])) {
    redirect("../login.php", "Vui lòng đăng nhập để bình luận");
    exit();
}

if(isset($_POST['add_comment']))
{
    $product_id = mysqli_real_escape_string($con, $_POST['product_id']);
    $product_slug = mysqli_real_escape_string($con, $_POST['product_slug']);
    $user_id = $_SESSION['auth_user']['user_id'];
    $comment = mysqli_real_escape_string($con, $_POST['comment']);
    
    if(empty($comment)) {
        redirect("../product-view.php?product=".$product_slug, "Vui lòng nhập nội dung bình luận!");
        exit();
    }
    
    $query = "INSERT INTO comments (user_id, product_id, comment) VALUES (?, ?, ?)";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "iis", $user_id, $product_id, $comment);
    
    if(mysqli_stmt_execute($stmt))
    {
        redirect("../product-view.php?product=".$product_slug, "Đã thêm bình luận thành công!");
    }
    else
    {
        redirect("../product-view.php?product=".$product_slug, "Đã xảy ra lỗi khi thêm bình luận!");
    }
}

// Xóa bình luận (chỉ admin hoặc người viết bình luận mới được xóa)
if(isset($_POST['delete_comment']))
{
    $comment_id = mysqli_real_escape_string($con, $_POST['comment_id']);
    $product_slug = mysqli_real_escape_string($con, $_POST['product_slug']);
    $user_id = $_SESSION['auth_user']['user_id'];
    
    // Kiểm tra quyền xóa
    $check_query = "SELECT * FROM comments WHERE id = ? AND (user_id = ? OR ? IN (SELECT id FROM users WHERE role_as = 1))";
    $stmt = mysqli_prepare($con, $check_query);
    mysqli_stmt_bind_param($stmt, "iii", $comment_id, $user_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if(mysqli_num_rows($result) > 0)
    {
        $delete_query = "DELETE FROM comments WHERE id = ?";
        $stmt = mysqli_prepare($con, $delete_query);
        mysqli_stmt_bind_param($stmt, "i", $comment_id);
        
        if(mysqli_stmt_execute($stmt))
        {
            redirect("../product-view.php?product=".$product_slug, "Đã xóa bình luận!");
        }
        else
        {
            redirect("../product-view.php?product=".$product_slug, "Đã xảy ra lỗi khi xóa bình luận!");
        }
    }
    else
    {
        redirect("../product-view.php?product=".$product_slug, "Bạn không có quyền xóa bình luận này!");
    }
}
?> 