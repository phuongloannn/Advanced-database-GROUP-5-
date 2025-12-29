<?php
include('config/dbcon.php');

function getProductComments($product_id) {
    global $con;
    
    $query = "SELECT c.*, u.name as user_name, u.avatar 
              FROM comments c 
              JOIN users u ON c.user_id = u.id 
              WHERE c.product_id = ? 
              ORDER BY c.created_at DESC";
              
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    $comments = array();
    while($row = mysqli_fetch_assoc($result)) {
        $comments[] = $row;
    }
    
    return $comments;
}

function canDeleteComment($comment_id, $user_id) {
    global $con;
    
    $query = "SELECT * FROM comments c 
              WHERE c.id = ? AND 
              (c.user_id = ? OR ? IN (SELECT id FROM users WHERE role_as = 1))";
              
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "iii", $comment_id, $user_id, $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    return mysqli_num_rows($result) > 0;
}
?> 