<?php
function renderSpecialProduct($product) {
    if (empty($product)) return;
?>
    <div class="bg-second">
        <div class="section container">
            <div class="row special-product">
                <div class="col-4 col-md-4">
                    <div class="sp-item-img">
                        <img src="anh_xedap/<?= htmlspecialchars($product['image']) ?>" 
                             alt="<?= htmlspecialchars($product['name']) ?>"
                             loading="lazy">
                    </div>
                </div>
                <div class="col-7 col-md-8">
                    <div class="sp-item-info">
                        <h2 class="sp-item-name"><?= htmlspecialchars($product['name']) ?></h2>
                        <p class="sp-item-description">
                            <?= htmlspecialchars($product['small_description']) ?>
                        </p>
                        <div class="sp-item-price">
                            <span class="original-price">
                                <del><?= number_format($product['original_price'], 0, ',', '.') ?> VND</del>
                            </span>
                            <span class="curr-price">
                                <?= number_format($product['selling_price'], 0, ',', '.') ?> VND
                            </span>
                        </div>
                        <div class="sp-item-buttons">
                            <a href="./product-detail.php?slug=<?= urlencode($product['slug']) ?>" 
                               class="btn-flat btn-hover">
                                Xem chi tiết
                            </a>
                            <form method="post" action="add-to-cart.php" class="d-inline">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button type="submit" class="btn-flat btn-hover">
                                    Thêm vào giỏ
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php
}
?> 