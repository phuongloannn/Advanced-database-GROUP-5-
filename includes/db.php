<?php
// includes/db.php

$host   = 'localhost';
$port   = 3307; // hoặc 3306 – đúng với MySQL của bạn
$user   = 'root';
$pass   = '';
$dbname = 'fashion_shop_group5';

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die("Kết nối DB thất bại: " . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
