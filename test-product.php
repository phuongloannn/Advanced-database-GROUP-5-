<?php
session_start();
include("config/dbcon.php");

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Test database connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "Database connection successful<br>";

// Test product query
$slug = isset($_GET['slug']) ? mysqli_real_escape_string($con, $_GET['slug']) : '';
if ($slug) {
    $query = "SELECT * FROM products WHERE slug = '$slug'";
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        die("Query failed: " . mysqli_error($con));
    }
    
    if (mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        echo "<pre>";
        print_r($product);
        echo "</pre>";
    } else {
        echo "No product found with slug: " . htmlspecialchars($slug);
    }
} else {
    echo "No slug provided";
}
?> 