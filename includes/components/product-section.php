<?php
function renderProductSection($title, $products, $viewAllLink = null) {
    if (empty($products)) return;
?>
    <div class="section">
        <div class="container">
            <div class="section-header">
                <h2><?= htmlspecialchars($title) ?></h2>
            </div>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <?php renderProductCard($product); ?>
                <?php endforeach; ?>
            </div>
            <?php if ($viewAllLink): ?>
            <div class="section-footer text-center">
                <a href="<?= $viewAllLink ?>" class="btn-flat btn-hover btn-view-all">
                    Xem tất cả sản phẩm
                    <i class='bx bx-right-arrow-alt'></i>
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
<?php
}
?> 