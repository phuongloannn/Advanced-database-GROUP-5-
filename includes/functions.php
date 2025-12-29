<?php
// includes/functions.php

include_once(__DIR__ . "/../config/dbcon.php");
include_once(__DIR__ . "/../functions/userfunctions.php");

// Lấy sản phẩm theo category_id với phân trang
function getProductsByCategory($category_id, $limit = 9, $page = 1)
{
    global $con;
    $offset = ($page - 1) * $limit;
    $category_id = mysqli_real_escape_string($con, $category_id);
    $query = "SELECT * FROM products WHERE category_id = '$category_id' AND status = '0' ORDER BY id DESC LIMIT $limit OFFSET $offset";
    $result = mysqli_query($con, $query);
    $products = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
    }
    return $products;
}

// Tổng số bản ghi theo danh mục
function totalValueByCategory($table, $category_id)
{
    global $con;
    $query = "SELECT COUNT(*) as total FROM $table WHERE category_id = '$category_id' AND status = '0'";
    $result = mysqli_query($con, $query);
    if ($result) {
        $data = mysqli_fetch_assoc($result);
        return $data['total'];
    }
    return 0;
}

function getRelatedProducts($category_id, $exclude_product_id, $limit = 4)
{
    global $con;
    $category_id = (int)$category_id;
    $exclude_product_id = (int)$exclude_product_id;

    $query = "SELECT * FROM products WHERE category_id = $category_id AND id != $exclude_product_id AND status = '0' ORDER BY created_at DESC LIMIT $limit";

    $result = mysqli_query($con, $query);
    $relatedProducts = [];
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $relatedProducts[] = $row;
        }
    }
    return $relatedProducts;
}

function getProductAverageRating($product_id) {
    global $con;
    $query = "SELECT AVG(rating) as avg_rating FROM reviews WHERE product_id = ?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "i", $product_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    return $row['avg_rating'] ? round($row['avg_rating'], 1) : 0;
}
