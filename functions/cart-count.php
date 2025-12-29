<?php
session_start();

if(isset($_SESSION['cart'])) {
    $count = count($_SESSION['cart']);
    echo $count;
} else {
    echo "0";
}
?> 