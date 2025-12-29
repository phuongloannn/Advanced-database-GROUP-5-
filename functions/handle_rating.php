<?php
session_start();
include('../config/dbcon.php');
include('myfunctions.php');
include('userfunctions.php');

if(!isset($_SESSION['auth_user'])) {
    redirect("../login.php", "Vui lòng đăng nhập để đánh giá");
    exit();
}

// Handle product rating
if(isset($_POST['rate_product']))
{
    $product_id = mysqli_real_escape_string($con, $_POST['product_id']);
    $product_slug = mysqli_real_escape_string($con, $_POST['product_slug']);
    $user_id = $_SESSION['auth_user']['user_id'];
    $rating = mysqli_real_escape_string($con, $_POST['rating']);
    $comment = mysqli_real_escape_string($con, $_POST['comment']);
    
    // Check if user has purchased the product
    if (!hasUserPurchased($product_id, $user_id)) {
        redirect("../product-view.php?product=".$product_slug, "Bạn cần mua hàng trước khi đánh giá sản phẩm");
        exit();
    }
    
    // Check if user has already rated
    $check_rating = "SELECT * FROM product_ratings WHERE user_id='$user_id' AND product_id='$product_id'";
    $check_rating_run = mysqli_query($con, $check_rating);
    
    if(mysqli_num_rows($check_rating_run) > 0)
    {
        // Update existing rating
        $update_query = "UPDATE product_ratings SET rating='$rating', comment='$comment', updated_at=NOW() 
                       WHERE user_id='$user_id' AND product_id='$product_id'";
        $update_query_run = mysqli_query($con, $update_query);
        
        if($update_query_run)
        {
            updateAverageRating($product_id);
            redirect("../product-view.php?product=".$product_slug, "Cảm ơn bạn đã cập nhật đánh giá!");
        }
        else
        {
            redirect("../product-view.php?product=".$product_slug, "Đã xảy ra lỗi!");
        }
    }
    else
    {
        // Insert new rating
        $insert_query = "INSERT INTO product_ratings (user_id, product_id, rating, comment) 
                       VALUES ('$user_id', '$product_id', '$rating', '$comment')";
        $insert_query_run = mysqli_query($con, $insert_query);
        
        if($insert_query_run)
        {
            updateAverageRating($product_id);
            redirect("../product-view.php?product=".$product_slug, "Cảm ơn bạn đã đánh giá!");
        }
        else
        {
            redirect("../product-view.php?product=".$product_slug, "Đã xảy ra lỗi!");
        }
    }
}

// Handle website rating
if(isset($_POST['rate_website'])) {
    $user_id = $_SESSION['auth_user']['id'];
    $rating = $_POST['rating'];
    $feedback = $_POST['feedback'] ?? '';

    if(rateWebsite($user_id, $rating, $feedback)) {
        $_SESSION['message'] = "Cảm ơn bạn đã đánh giá website";
    } else {
        $_SESSION['message'] = "Có lỗi xảy ra khi đánh giá website";
    }

    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit();
}

// If no action matched
$_SESSION['message'] = "Yêu cầu không hợp lệ";
header('Location: ' . $_SERVER['HTTP_REFERER']);
exit();

// Function to update product's average rating
function updateAverageRating($product_id)
{
    global $con;
    
    $avg_query = "SELECT AVG(rating) as average_rating FROM product_ratings WHERE product_id='$product_id'";
    $avg_query_run = mysqli_query($con, $avg_query);
    $avg_result = mysqli_fetch_array($avg_query_run);
    $average_rating = round($avg_result['average_rating'], 1);
    
    $update_product = "UPDATE products SET average_rating='$average_rating' WHERE id='$product_id'";
    mysqli_query($con, $update_product);
}
?>