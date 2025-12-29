<?php 
include('functions/userfunctions.php');
include('includes/header.php');

// Lấy danh mục từ query string
$category_slug = isset($_GET['type']) ? $_GET['type'] : '';

// Lấy thông tin danh mục nếu có
$category_id = null;
$category_name = "Tất cả sản phẩm";
if (!empty($category_slug)) {
    $category_result = getBySlug("categories", $category_slug);
    if (mysqli_num_rows($category_result) > 0) {
        $category = mysqli_fetch_assoc($category_result);
        $category_id = $category['id'];
        $category_name = $category['name'];
    }
}
?>

<div class="py-3 bg-primary">
    <div class="container">
        <h6 class="text-white">
            <a href="index.php" class="text-white">Trang chủ</a> / 
            <a href="products.php" class="text-white">Sản phẩm</a>
            <?php if ($category_id) { ?>
                / <span class="text-white"><?= htmlspecialchars($category_name) ?></span>
            <?php } ?>
        </h6>
    </div>
</div>

<div class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1><?= htmlspecialchars($category_name) ?></h1>
                <hr>
                <div class="row">
                    <?php
                    // Lấy sản phẩm theo danh mục hoặc tất cả sản phẩm
                    $products = $category_id ? getProdByCategory($category_id) : getAllActive("products");

                    if ($products && mysqli_num_rows($products) > 0) {
                        while ($item = mysqli_fetch_assoc($products)) {
                    ?>
                        <div class="col-md-3 mb-4">
                            <div class="card product_data h-100">
                                <div class="card-body d-flex flex-column">
                                    <img src="anh_xedap/<?= htmlspecialchars($item['image']); ?>" 
                                         alt="<?= htmlspecialchars($item['name']); ?>" 
                                         class="w-100 mb-3" 
                                         style="height: 200px; object-fit: cover;">
                                    <h5 class="card-title text-center flex-grow-1">
                                        <?= htmlspecialchars($item['name']); ?>
                                    </h5>
                                    <?php if (!empty($item['small_description'])): ?>
                                        <p class="card-text small text-muted">
                                            <?= htmlspecialchars($item['small_description']); ?>
                                        </p>
                                    <?php endif; ?>
                                    <div class="mt-auto">
                                        <p class="card-text mb-2">
                                            <?php if ($item['original_price'] > $item['selling_price']): ?>
                                                <s class="text-secondary">
                                                    <?= number_format($item['original_price'], 0, ',', '.') ?> đ
                                                </s>
                                            <?php endif; ?>
                                            <span class="text-danger fw-bold d-block">
                                                <?= number_format($item['selling_price'], 0, ',', '.') ?> đ
                                            </span>
                                        </p>
                                        <div class="d-grid gap-2">
                                            <?php if ($item['qty'] > 0): ?>
                                                <?php if (isset($_SESSION['auth_user'])): ?>
                                                    <a href="product-detail.php?slug=<?= htmlspecialchars($item['slug']); ?>" 
                                                       class="btn btn-primary">
                                                        Thêm vào giỏ
                                                    </a>
                                                <?php else: ?>
                                                    <a href="login.php" class="btn btn-primary">
                                                        Đăng nhập để mua
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <button class="btn btn-secondary" disabled>
                                                    Hết hàng
                                                </button>
                                            <?php endif; ?>
                                            <a href="product-detail.php?slug=<?= htmlspecialchars($item['slug']); ?>" 
                                               class="btn btn-outline-primary">
                                                Xem chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php
                        }
                    } else {
                        echo "<div class='col-12'><p class='alert alert-info'>Không có sản phẩm nào trong danh mục này.</p></div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>

<script>
$(document).ready(function() {
    // Xóa đoạn script cũ
});
</script>