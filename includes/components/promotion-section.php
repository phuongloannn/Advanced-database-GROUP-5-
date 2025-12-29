<?php
function renderPromotionSection($products) {
    if (empty($products)) return;
?>
    <div class="promotion">
        <div class="row">
            <?php
            $count = 0;
            foreach ($products as $product) {
                if ($count == 3) break;
            ?>
                <div class="col-4 col-md-12 col-sm-12">
                    <div class="promotion-box">
                        <div class="text">
                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                            <p class="promotion-description">
                                <?= htmlspecialchars($product['small_description']) ?>
                            </p>
                            <div class="promotion-price">
                                <span class="curr-price">
                                    <?= number_format($product['selling_price'], 0, ',', '.') ?> VND
                                </span>
                            </div>
                            <a href="./product-detail.php?slug=<?= urlencode($product['slug']) ?>" 
                               class="btn-flat btn-hover">
                                Xem chi tiết
                            </a>
                        </div>
                        <div class="promotion-image">
                            <img src="./uploads/products/<?= htmlspecialchars($product['image']) ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 loading="lazy">
                        </div>
                    </div>
                </div>
            <?php
                $count++;
            }
            ?>
        </div>
    </div>
<?php
}
?> 