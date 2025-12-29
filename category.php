<?php
include('./includes/header.php');
include('./includes/functions.php');

// Function để lấy sản phẩm đại diện của danh mục
function getCategoryRepresentativeProduct($category_id) {
    global $con;
    $query = "SELECT * FROM products WHERE category_id = '$category_id' AND status = '0' AND image != '' ORDER BY id DESC LIMIT 1";
    $result = mysqli_query($con, $query);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return null;
}
?>

<!-- Custom CSS -->
<style>
    .category-header {
        background: linear-gradient(135deg, #0d6efd 0%, #0099ff 100%);
        padding: 40px 0;
        margin-bottom: 40px;
        color: white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .category-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 15px;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    }

    .category-description {
        font-size: 1.1rem;
        opacity: 0.9;
    }

    .category-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .category-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .category-image {
        position: relative;
        height: 250px;
        overflow: hidden;
    }

    .category-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .category-card:hover .category-image img {
        transform: scale(1.1);
    }

    .category-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to bottom, rgba(0,0,0,0) 0%, rgba(0,0,0,0.7) 100%);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .category-card:hover .category-overlay {
        opacity: 1;
    }

    .category-card .card-body {
        padding: 25px;
        background: white;
    }

    .category-card .card-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 15px;
        color: #2c3e50;
    }

    .category-card .card-text {
        color: #666;
        margin-bottom: 20px;
        line-height: 1.6;
    }

    .product-count {
        display: inline-block;
        padding: 5px 15px;
        background: #e8f4ff;
        color: #0d6efd;
        border-radius: 20px;
        font-weight: 500;
        margin-bottom: 20px;
    }

    .btn-view-products {
        width: 100%;
        padding: 12px;
        border-radius: 25px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s ease;
    }

    .btn-view-products:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
    }

    .product-card {
        border: none;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    .product-image {
        height: 200px;
        overflow: hidden;
    }

    .product-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .product-card:hover .product-image img {
        transform: scale(1.1);
    }

    .product-price {
        font-size: 1.25rem;
        font-weight: 600;
        color: #dc3545;
    }

    .original-price {
        text-decoration: line-through;
        color: #6c757d;
        font-size: 0.9rem;
    }

    .out-of-stock-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        padding: 5px 15px;
        border-radius: 15px;
        font-weight: 500;
        z-index: 1;
    }

    .no-image-placeholder {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        color: #adb5bd;
    }

    /* Animation for cards */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .category-card, .product-card {
        animation: fadeInUp 0.6s ease backwards;
    }

    .category-card:nth-child(2) {
        animation-delay: 0.2s;
    }

    .category-card:nth-child(3) {
        animation-delay: 0.4s;
    }
</style>

<?php
// Nếu có slug, hiển thị sản phẩm của danh mục đó
if (isset($_GET['slug'])) {
    $slug = mysqli_real_escape_string($con, $_GET['slug']);
    $categoryResult = getBySlug("categories", $slug);

    if (mysqli_num_rows($categoryResult) > 0) {
        $category = mysqli_fetch_assoc($categoryResult);
        $category_id = $category['id'];
        $products = getProdByCategory($category_id);
?>
        <!-- Category Header -->
        <div class="category-header">
            <div class="container">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-3">
                        <li class="breadcrumb-item"><a href="index.php" class="text-white">Trang chủ</a></li>
                        <li class="breadcrumb-item"><a href="category.php" class="text-white">Danh mục</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page"><?= htmlspecialchars($category['name']) ?></li>
                    </ol>
                </nav>
                <h1 class="category-title"><?= htmlspecialchars($category['name']) ?></h1>
                <?php if (!empty($category['description'])): ?>
                    <p class="category-description"><?= htmlspecialchars($category['description']) ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="container mb-5">
            <div class="row">
                <?php 
                if ($products && mysqli_num_rows($products) > 0) {
                    while ($item = mysqli_fetch_assoc($products)) { 
                ?>
                    <div class="col-md-3 mb-4">
                        <div class="product-card">
                            <?php if ($item['qty'] <= 0): ?>
                                <span class="out-of-stock-badge">Hết hàng</span>
                            <?php endif; ?>
                            <div class="product-image">
                                <img src="anh_xedap/<?= htmlspecialchars($item['image']) ?>" 
                                     alt="<?= htmlspecialchars($item['name']) ?>">
                            </div>
                            <div class="card-body">
                                <h5 class="card-title text-truncate"><?= htmlspecialchars($item['name']) ?></h5>
                                <?php if (!empty($item['small_description'])): ?>
                                    <p class="card-text small text-muted text-truncate">
                                        <?= htmlspecialchars($item['small_description']) ?>
                                    </p>
                                <?php endif; ?>
                                <div class="mt-3">
                                    <?php if ($item['original_price'] > $item['selling_price']): ?>
                                        <div class="original-price mb-1">
                                            <?= number_format($item['original_price'], 0, ',', '.') ?> đ
                                        </div>
                                    <?php endif; ?>
                                    <div class="product-price mb-3">
                                        <?= number_format($item['selling_price'], 0, ',', '.') ?> đ
                                    </div>
                                    <a href="product-detail.php?slug=<?= htmlspecialchars($item['slug']) ?>" 
                                       class="btn btn-primary btn-view-products">
                                        Xem chi tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                    }
                } else {
                    echo '<div class="col-12"><div class="alert alert-info">Không có sản phẩm nào trong danh mục này.</div></div>';
                }
                ?>
            </div>
        </div>
<?php
    } else {
        echo '<div class="container"><div class="alert alert-warning">Danh mục không tồn tại.</div></div>';
    }
} else {
    // Hiển thị tất cả danh mục
    $categories = getAllActive("categories");
?>
    <!-- Categories Header -->
    <div class="category-header">
        <div class="container">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-3">
                    <li class="breadcrumb-item"><a href="index.php" class="text-white">Trang chủ</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Danh mục</li>
                </ol>
            </nav>
            <h1 class="category-title">Danh mục sản phẩm</h1>
            <p class="category-description">Khám phá các dòng xe đạp chất lượng cao của chúng tôi</p>
        </div>
    </div>

    <div class="container mb-5">
        <div class="row">
            <?php 
            if ($categories && mysqli_num_rows($categories) > 0) {
                while ($category = mysqli_fetch_assoc($categories)) { 
                    $product_count = totalValueByCategory("products", $category['id']);
                    $representative_product = getCategoryRepresentativeProduct($category['id']);
            ?>
                <div class="col-md-4 mb-4">
                    <div class="category-card">
                        <div class="category-image">
                            <?php if ($representative_product): ?>
                                <img src="anh_xedap/<?= htmlspecialchars($representative_product['image']) ?>" 
                                     alt="<?= htmlspecialchars($category['name']) ?>">
                            <?php else: ?>
                                <div class="no-image-placeholder">
                                    <i class="fas fa-bicycle fa-4x"></i>
                                </div>
                            <?php endif; ?>
                            <div class="category-overlay"></div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($category['name']) ?></h5>
                            <?php if (!empty($category['description'])): ?>
                                <p class="card-text"><?= htmlspecialchars($category['description']) ?></p>
                            <?php endif; ?>
                            <div class="product-count">
                                <?= $product_count ?> sản phẩm
                            </div>
                            <a href="category.php?slug=<?= htmlspecialchars($category['slug']) ?>" 
                               class="btn btn-primary btn-view-products">
                                Xem sản phẩm
                            </a>
                        </div>
                    </div>
                </div>
            <?php 
                }
            } else {
                echo '<div class="col-12"><div class="alert alert-info">Không có danh mục nào.</div></div>';
            }
            ?>
        </div>
    </div>
<?php
}

include('./includes/footer.php');
?>