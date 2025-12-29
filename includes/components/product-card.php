<?php
function renderProductCard($product, $isLarge = false) {
    $isNew = (strtotime($product['created_at']) > strtotime('-7 days'));
    $hasDiscount = ($product['original_price'] > $product['selling_price']);
    $discountPercent = $hasDiscount ? round((($product['original_price'] - $product['selling_price']) / $product['original_price']) * 100) : 0;
?>
    <div class="product-card <?= $isLarge ? 'product-card-large' : '' ?>">
        <!-- Product Labels -->
        <?php if ($isNew || $hasDiscount): ?>
        <div class="product-label">
            <?php if ($isNew): ?>
                <span class="label-new">Mới</span>
            <?php endif; ?>
            <?php if ($hasDiscount): ?>
                <span class="label-sale">-<?= $discountPercent ?>%</span>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="product-card-img">
            <img src="./uploads/products/<?= htmlspecialchars($product['image']) ?>" 
                 alt="<?= htmlspecialchars($product['name']) ?>"
                 loading="lazy">
            <div class="product-card-overlay">
                <div class="product-btn">
                    <a href="./product-detail.php?slug=<?= urlencode($product['slug']) ?>" 
                       class="btn-shop-now">
                        <i class='bx bx-shopping-bag'></i>
                        Chi tiết
                    </a>
                    <form method="post" action="add-to-cart.php" class="d-inline">
                        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                        <button type="submit" class="btn-cart-add" 
                                title="Thêm vào giỏ hàng">
                            <i class='bx bxs-cart-add'></i>
                        </button>
                    </form>
                    <button class="btn-cart-add wishlist-btn" 
                            data-product-id="<?= $product['id'] ?>"
                            title="Thêm vào yêu thích">
                        <i class='bx bxs-heart'></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="product-card-info">
            <div class="product-card-content">
                <h3 class="product-card-name">
                    <a href="./product-detail.php?slug=<?= urlencode($product['slug']) ?>">
                        <?= htmlspecialchars($product['name']) ?>
                    </a>
                </h3>
                <?php if (!empty($product['small_description'])): ?>
                <p class="product-card-desc">
                    <?= htmlspecialchars($product['small_description']) ?>
                </p>
                <?php endif; ?>
                <div class="product-card-price">
                    <?php if ($hasDiscount): ?>
                    <span class="original-price">
                        <?= number_format($product['original_price'], 0, ',', '.') ?> VND
                    </span>
                    <?php endif; ?>
                    <span class="curr-price">
                        <?= number_format($product['selling_price'], 0, ',', '.') ?> VND
                    </span>
                </div>
            </div>
            <div class="product-card-actions">
                <form method="post" action="add-to-cart.php" class="w-100">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <button type="submit" class="btn-buy-now">
                        Mua ngay
                    </button>
                </form>
            </div>
        </div>
    </div>
<?php
}
?> 