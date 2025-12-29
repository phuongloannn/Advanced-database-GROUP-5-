<?php
session_start();
include('config/dbcon.php');

$product_id = intval($_POST['product_id']);
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

// Lấy thông tin sản phẩm từ database
$product = $con->query("SELECT id, name, image, selling_price FROM products WHERE id = $product_id")->fetch_assoc();
if (!$product) {
    echo "<script>alert('Sản phẩm không tồn tại!');window.location='index.php';</script>";
    exit();
}

// Thêm vào giỏ hàng session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Nếu sản phẩm đã có trong giỏ thì tăng số lượng
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = [
        'id' => $product['id'],
        'name' => $product['name'],
        'image' => $product['image'],
        'price' => $product['selling_price'],
        'quantity' => $quantity
    ];
}

// Nếu là mua ngay thì chuyển thẳng đến trang giỏ hàng
if (isset($_POST['buy_now'])) {
    header("Location: cart.php");
} else {
    // Thông báo và chuyển hướng về trang trước
    echo "<script>alert('Thêm vào giỏ hàng thành công!');window.history.back();</script>";
}
exit();
?>

<!-- XÓA hoặc COMMENT đoạn này -->
<script>
    /*
document.querySelectorAll('.btn-cart-add').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault(); // Chặn submit để test
        alert('Bạn vừa bấm vào icon giỏ hàng!');
    });
});
});
*/
</script>