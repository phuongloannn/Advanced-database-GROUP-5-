<?php
session_start();
include('config/dbcon.php');

// Lấy thông tin sản phẩm
if(isset($_GET['product'])) {
    $product_slug = $_GET['product'];
    $product_query = "SELECT * FROM products WHERE slug='$product_slug' AND status='0' LIMIT 1";
    $product_query_run = mysqli_query($con, $product_query);

    if(mysqli_num_rows($product_query_run) > 0) {
        $product = mysqli_fetch_assoc($product_query_run);
?>
        <div class="py-3 product-data">
            <div class="container">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                <h4><?= $product['name']; ?></h4>
                            </div>
                            <div class="card-body">
                                <!-- Thông tin sản phẩm -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <img src="uploads/<?= $product['image']; ?>" alt="Product Image" class="w-100">
                                    </div>
                                    <div class="col-md-6">
                                        <h4 class="fw-bold"><?= $product['name']; ?></h4>
                                        <hr>
                                        <p><?= $product['small_description']; ?></p>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h5>Giá: <?= number_format($product['selling_price'], 0, ',', '.') ?> VND</h5>
                                            </div>
                                            <div class="col-md-6">
                                                <h5>Đánh giá: <?= number_format($product['rating'], 1); ?>/5.0</h5>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <div class="input-group">
                                                <button class="input-group-text decrement-btn">-</button>
                                                <input type="text" class="form-control text-center input-qty" value="1">
                                                <button class="input-group-text increment-btn">+</button>
                                            </div>
                                        </div>
                                        <div class="mt-3">
                                            <button class="btn btn-primary px-4 addToCartBtn" value="<?= $product['id']; ?>">
                                                <i class="fa fa-shopping-cart me-2"></i>Thêm vào giỏ hàng
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <h5>Mô tả chi tiết</h5>
                                <p><?= $product['description']; ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Phần đánh giá -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <?php include('includes/review_form.php'); ?>
                    </div>
                </div>
            </div>
        </div>
<?php
    } else {
        echo "Sản phẩm không tồn tại";
    }
} else {
    echo "URL không hợp lệ";
}
?> 