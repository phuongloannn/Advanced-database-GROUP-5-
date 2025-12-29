<?php
if(isset($_SESSION['message'])) {
    echo '<div class="alert alert-info">'.$_SESSION['message'].'</div>';
    unset($_SESSION['message']);
}
?>

<div class="review-section mt-4">
    <div class="card">
        <div class="card-body">
            <!-- Phần đánh giá tổng quan -->
            <div class="row mb-4">
                <div class="col-md-4 text-center border-end">
                    <h2 class="display-4 fw-bold"><?= number_format($product['rating'], 1) ?></h2>
                    <div class="rating-stars mb-2">
                        <?php
                        $rating = $product['rating'];
                        for($i = 1; $i <= 5; $i++) {
                            if($i <= $rating) {
                                echo '<i class="fas fa-star text-warning fa-lg"></i>';
                            } elseif($i - $rating < 1) {
                                echo '<i class="fas fa-star-half-alt text-warning fa-lg"></i>';
                            } else {
                                echo '<i class="far fa-star text-warning fa-lg"></i>';
                            }
                        }
                        ?>
                    </div>
                    <p class="text-muted">Dựa trên đánh giá của khách hàng</p>
                </div>
                <div class="col-md-8">
                    <h4>Viết đánh giá của bạn</h4>
                    <?php if(isset($_SESSION['auth_user'])) { ?>
                        <form action="functions/handle_review.php" method="POST" class="review-form">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            
                            <div class="rating-input mb-3">
                                <label class="form-label">Đánh giá của bạn:</label>
                                <div class="star-rating">
                                    <input type="radio" id="star5" name="rating" value="5" required>
                                    <label for="star5" title="5 sao"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star4" name="rating" value="4">
                                    <label for="star4" title="4 sao"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star3" name="rating" value="3">
                                    <label for="star3" title="3 sao"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star2" name="rating" value="2">
                                    <label for="star2" title="2 sao"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star1" name="rating" value="1">
                                    <label for="star1" title="1 sao"><i class="fas fa-star"></i></label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nhận xét của bạn:</label>
                                <textarea name="comment" class="form-control" rows="3" placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..." required></textarea>
                            </div>

                            <button type="submit" name="submit_review" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Gửi đánh giá
                            </button>
                        </form>
                    <?php } else { ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-info-circle me-2"></i>
                            Vui lòng <a href="login.php" class="alert-link">đăng nhập</a> để viết đánh giá
                        </div>
                    <?php } ?>
                </div>
            </div>

            <!-- Danh sách đánh giá -->
            <div class="reviews-list">
                <h5 class="mb-4">
                    <i class="fas fa-comments me-2"></i>
                    Đánh giá từ khách hàng
                </h5>
                <?php
                $reviews_query = "SELECT r.*, u.name as user_name 
                                FROM reviews r 
                                JOIN users u ON r.user_id = u.id 
                                WHERE r.product_id = '{$product['id']}' 
                                ORDER BY r.created_at DESC";
                $reviews_result = mysqli_query($con, $reviews_query);

                if(mysqli_num_rows($reviews_result) > 0) {
                    while($review = mysqli_fetch_assoc($reviews_result)) {
                        ?>
                        <div class="review-item mb-4">
                            <div class="d-flex align-items-center mb-2">
                                <div class="avatar me-3">
                                    <div class="avatar-text bg-primary text-white rounded-circle">
                                        <?= strtoupper(substr($review['user_name'], 0, 1)) ?>
                                    </div>
                                </div>
                                <div>
                                    <h6 class="mb-0"><?= htmlspecialchars($review['user_name']) ?></h6>
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i>
                                        <?= date('d/m/Y', strtotime($review['created_at'])) ?>
                                    </small>
                                </div>
                            </div>
                            <div class="rating mb-2">
                                <?php
                                for($i = 1; $i <= 5; $i++) {
                                    if($i <= $review['rating']) {
                                        echo '<i class="fas fa-star text-warning"></i>';
                                    } else {
                                        echo '<i class="far fa-star text-warning"></i>';
                                    }
                                }
                                ?>
                            </div>
                            <p class="review-text mb-0">
                                <?= htmlspecialchars($review['comment']) ?>
                            </p>
                        </div>
                        <hr>
                        <?php
                    }
                } else {
                    ?>
                    <div class="text-center text-muted py-5">
                        <i class="far fa-comment-dots fa-3x mb-3"></i>
                        <p>Chưa có đánh giá nào cho sản phẩm này</p>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>

<style>
/* CSS cho phần rating */
.rating-stars i {
    margin: 0 2px;
}

.star-rating {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.star-rating input {
    display: none;
}

.star-rating label {
    cursor: pointer;
    font-size: 1.5em;
    color: #ddd;
    margin: 0 2px;
}

.star-rating label:hover,
.star-rating label:hover ~ label,
.star-rating input:checked ~ label {
    color: #ffc107;
}

/* CSS cho avatar */
.avatar {
    width: 40px;
    height: 40px;
}

.avatar-text {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 1.2em;
}

/* CSS cho review item */
.review-item {
    background-color: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
}

.review-text {
    color: #444;
    line-height: 1.6;
}

/* Hiệu ứng hover cho nút */
.btn-primary {
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style> 