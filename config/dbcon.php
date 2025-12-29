<?php
$host = "localhost:3307";
$username = "root";
$password = "";
$database = "fashion_shop_group5";

// Create connection
$con = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($con, 'utf8');

// No need for global declaration since $con is already in global scope

//check database
if (!$con) {
    die("Connection Faild " . mysqli_connect_errno());
    echo "Something Wrong";
}
