<?php
function renderHeroSlider($products) {
    if (empty($products)) return;
?>
    <div class="hero">
        <div class="slider">
            <div class="container">
                <?php
                $count = 0;
                foreach ($products as $product) {
                    if ($count == 3) break;
                ?>
                    <div class="slide">
                        <div class="info">
                            <div class="info-content">
                                <h3 class="top-down">
                                    <?= htmlspecialchars($product['name']) ?>
                                </h3>
                                <h2 class="top-down trans-delay-0-2">
                                    <?= htmlspecialchars($product['name']) ?>
                                </h2>
                                <p class="top-down trans-delay-0-4">
                                    <?= htmlspecialchars($product['small_description']) ?>
                                </p>
                                <div class="top-down trans-delay-0-6">
                                    <a href="./product-detail.php?slug=<?= urlencode($product['slug']) ?>" class="btn-flat btn-hover">
                                        Mua ngay
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="img right-left">
                            <img src="./uploads/products/<?= htmlspecialchars($product['image']) ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>"
                                 loading="lazy">
                        </div>
                    </div>
                <?php
                    $count++;
                }
                ?>
            </div>
            <button class="slide-controll slide-next">
                <i class='bx bxs-chevron-right'></i>
            </button>
            <button class="slide-controll slide-prev">
                <i class='bx bxs-chevron-left'></i>
            </button>
        </div>
    </div>
<?php
}
?> 