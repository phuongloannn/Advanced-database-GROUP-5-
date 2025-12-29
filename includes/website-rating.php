<?php
include_once 'functions/rating_functions.php';

$website_stats = getWebsiteAverageRating();
$user_rating = isset($_SESSION['auth_user']['id']) ? 
    getWebsiteRatingByUser($_SESSION['auth_user']['id']) : null;
?>

<div class="website-rating">
    <div class="rating-container">
        <h4>Đánh giá website</h4>
        
        <div class="rating-overview mb-4">
            <div class="average-rating text-center">
                <div class="rating-number">
                    <?= number_format($website_stats['average_rating'] ?? 0, 1) ?>
                    <small>/5</small>
                </div>
                <div class="rating-stars">
                    <?php 
                    $avg = $website_stats['average_rating'] ?? 0;
                    for($i = 1; $i <= 5; $i++): 
                    ?>
                        <i class="fas fa-star <?= $i <= round($avg) ? 'text-warning' : 'text-muted' ?>"></i>
                    <?php endfor; ?>
                </div>
                <div class="rating-count text-muted">
                    <?= $website_stats['total_ratings'] ?? 0 ?> đánh giá
                </div>
            </div>
        </div>

        <?php if(isset($_SESSION['auth_user'])): ?>
            <div class="rating-form">
                <form action="functions/handle_rating.php" method="POST" class="needs-validation" novalidate>
                    <div class="form-group">
                        <label>Đánh giá của bạn:</label>
                        <div class="rating-stars">
                            <?php for($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" name="rating" value="<?= $i ?>" 
                                       id="website-rate-<?= $i ?>" 
                                       <?= ($user_rating && $user_rating['rating'] == $i) ? 'checked' : '' ?> required>
                                <label for="website-rate-<?= $i ?>">
                                    <i class="fas fa-star"></i>
                                </label>
                            <?php endfor; ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="feedback">Góp ý của bạn:</label>
                        <textarea class="form-control" name="feedback" id="feedback" rows="3" 
                                  placeholder="Chia sẻ trải nghiệm của bạn..."><?= $user_rating['feedback'] ?? '' ?></textarea>
                    </div>
                    <button type="submit" name="rate_website" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Gửi đánh giá
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Vui lòng <a href="login.php">đăng nhập</a> để đánh giá website
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.website-rating {
    padding: 30px 0;
    background: #f8f9fa;
}

.rating-container {
    max-width: 600px;
    margin: 0 auto;
    padding: 30px;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
}

.rating-overview {
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
}

.rating-number {
    font-size: 3rem;
    font-weight: bold;
    color: #e60000;
    line-height: 1;
    margin-bottom: 10px;
}

.rating-number small {
    font-size: 1.5rem;
    color: #666;
}

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
    transition: all 0.2s ease-in-out;
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

.rating-count {
    margin-top: 5px;
    font-size: 0.9rem;
}

.rating-form {
    margin-top: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.btn-primary {
    background-color: #e60000;
    border-color: #e60000;
    width: 100%;
    padding: 12px;
}

.btn-primary:hover {
    background-color: #cc0000;
    border-color: #cc0000;
}

@media (max-width: 576px) {
    .rating-container {
        margin: 15px;
        padding: 20px;
    }
}
</style> 