<?php
include_once 'functions/rating_functions.php';

if(!isset($product_id)) {
    die("Product ID not set");
}

$user_rating = isset($_SESSION['auth_user']['id']) ? 
    getProductRatingByUser($product_id, $_SESSION['auth_user']['id']) : null;
$all_ratings = getProductRatings($product_id);
?>

<div class="product-ratings mt-4">
    <h4 class="mb-3">Đánh giá sản phẩm</h4>
    
    <?php if(isset($_SESSION['auth_user'])): ?>
        <div class="rating-form mb-4">
            <form action="functions/handle_rating.php" method="POST" class="needs-validation" novalidate>
                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                <div class="form-group">
                    <label>Đánh giá của bạn:</label>
                    <div class="rating-stars">
                        <?php for($i = 5; $i >= 1; $i--): ?>
                            <input type="radio" name="rating" value="<?= $i ?>" 
                                   id="product-rate-<?= $i ?>" 
                                   <?= ($user_rating && $user_rating['rating'] == $i) ? 'checked' : '' ?> required>
                            <label for="product-rate-<?= $i ?>">
                                <i class="fas fa-star"></i>
                            </label>
                        <?php endfor; ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="comment">Nhận xét của bạn:</label>
                    <textarea class="form-control" name="comment" id="comment" rows="3"><?= $user_rating['comment'] ?? '' ?></textarea>
                </div>
                <button type="submit" name="rate_product" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> Gửi đánh giá
                </button>
            </form>
        </div>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Vui lòng <a href="login.php">đăng nhập</a> để đánh giá sản phẩm
        </div>
    <?php endif; ?>

    <div class="rating-summary mb-4">
        <h5>Đánh giá từ khách hàng</h5>
        <?php if(empty($all_ratings)): ?>
            <p class="text-muted">Chưa có đánh giá nào cho sản phẩm này</p>
        <?php else: ?>
            <div class="ratings-list">
                <?php foreach($all_ratings as $rating): ?>
                    <div class="rating-item border-bottom py-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong><?= htmlspecialchars($rating['user_name']) ?></strong>
                                <div class="rating-stars">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <i class="fas fa-star <?= $i <= $rating['rating'] ? 'text-warning' : 'text-muted' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <small class="text-muted">
                                <?= date('d/m/Y', strtotime($rating['created_at'])) ?>
                            </small>
                        </div>
                        <?php if(!empty($rating['comment'])): ?>
                            <p class="mt-2 mb-0"><?= nl2br(htmlspecialchars($rating['comment'])) ?></p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.rating-stars {
    display: inline-flex;
    flex-direction: row-reverse;
    gap: 3px;
}

.rating-stars input {
    display: none;
}

.rating-stars label {
    cursor: pointer;
    color: #ddd;
    font-size: 1.5rem;
    transition: color 0.2s ease-in-out;
}

.rating-stars label:hover,
.rating-stars label:hover ~ label,
.rating-stars input:checked ~ label {
    color: #ffc107;
}

.rating-stars label:hover,
.rating-stars label:hover ~ label {
    transform: scale(1.1);
}

.rating-item .rating-stars {
    display: inline-flex;
    flex-direction: row;
    font-size: 1rem;
}

.rating-item {
    transition: background-color 0.2s ease;
}

.rating-item:hover {
    background-color: #f8f9fa;
}

.product-ratings {
    background: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
}

.btn-primary {
    background-color: #e60000;
    border-color: #e60000;
}

.btn-primary:hover {
    background-color: #cc0000;
    border-color: #cc0000;
}
</style> 