<?php
session_start();
require_once('./functions/userfunctions.php');
require_once('./includes/db.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['auth_user'])) {
    header('Location: login.php');
    exit();
}

// Kiểm tra product_id
if (!isset($_GET['product_id'])) {
    header('Location: index.php');
    exit();
}

$product_id = $_GET['product_id'];
$user_id = $_SESSION['auth_user']['id'];

// Lấy thông tin sản phẩm
$product_query = "SELECT * FROM products WHERE id = ? AND status = '0'";
$stmt = mysqli_prepare($con, $product_query);
mysqli_stmt_bind_param($stmt, "i", $product_id);
mysqli_stmt_execute($stmt);
$product_result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($product_result) == 0) {
    header('Location: index.php');
    exit();
}

$product = mysqli_fetch_assoc($product_result);

// Kiểm tra xem người dùng đã đánh giá chưa
$check_review = "SELECT * FROM reviews WHERE product_id = ? AND user_id = ?";
$stmt = mysqli_prepare($con, $check_review);
mysqli_stmt_bind_param($stmt, "ii", $product_id, $user_id);
mysqli_stmt_execute($stmt);
$existing_review = mysqli_stmt_get_result($stmt);
$review = mysqli_fetch_assoc($existing_review);

// Include header sau khi đã xử lý tất cả redirect
include('./includes/header.php');
?>

<div class="py-3 bg-primary">
    <div class="container">
        <h6 class="text-white">
            <a href="index.php" class="text-white">Trang chủ</a> / 
            <a href="product-detail.php?slug=<?= $product['slug'] ?>" class="text-white"><?= htmlspecialchars($product['name']) ?></a> /
            Đánh giá sản phẩm
        </h6>
    </div>
</div>

<div class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h4 class="text-white">Đánh giá sản phẩm</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 text-center">
                                <img src="anh_xedap/<?= htmlspecialchars($product['image']) ?>" 
                                     class="w-100 mb-3" 
                                     style="max-width: 300px; object-fit: cover;"
                                     alt="<?= htmlspecialchars($product['name']) ?>">
                                <h5><?= htmlspecialchars($product['name']) ?></h5>
                            </div>
                            <div class="col-md-8">
                                <form id="reviewForm" method="POST" action="functions/handle_review.php">
                                    <input type="hidden" name="product_id" value="<?= $product_id ?>">
                                    
                                    <div class="mb-4">
                                        <label class="form-label">Đánh giá của bạn</label>
                                        <div class="star-rating">
                                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                                <i class="fas fa-star rating-star" 
                                                   data-value="<?= $i ?>"
                                                   <?= ($review && $review['rating'] == $i) ? 'style="color: #ffc107;"' : '' ?>></i>
                                            <?php endfor; ?>
                                        </div>
                                        <input type="hidden" name="rating" id="rating-value" 
                                               value="<?= $review ? $review['rating'] : '' ?>" required>
                                        <div class="rating-text mt-2"></div>
                                    </div>

                                    <div class="mb-4">
                                        <label class="form-label">Nhận xét của bạn</label>
                                        <textarea name="comment" class="form-control" rows="4" 
                                                  placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."
                                                  required><?= $review ? htmlspecialchars($review['comment']) : '' ?></textarea>
                                    </div>

                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-paper-plane me-2"></i>
                                        <?= $review ? 'Cập nhật đánh giá' : 'Gửi đánh giá' ?>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.star-rating {
    display: inline-flex;
    gap: 10px;
}

.rating-star {
    cursor: pointer;
    font-size: 24px;
    color: #e4e5e7;
    transition: all 0.2s ease;
}

.rating-star:hover,
.rating-star.hover {
    transform: scale(1.2);
}

.rating-star.active,
.rating-star.hover {
    color: #ffc107;
}

.rating-text {
    font-size: 14px;
    color: #6c757d;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stars = document.querySelectorAll('.rating-star');
    const ratingValue = document.getElementById('rating-value');
    const ratingText = document.querySelector('.rating-text');
    
    function updateRatingText(value) {
        const texts = {
            1: 'Rất tệ',
            2: 'Tệ',
            3: 'Bình thường',
            4: 'Tốt',
            5: 'Rất tốt'
        };
        ratingText.textContent = texts[value] || '';
    }

    // Khôi phục đánh giá cũ nếu có
    if (ratingValue.value) {
        updateRatingText(parseInt(ratingValue.value));
        stars.forEach((star, index) => {
            if (index < parseInt(ratingValue.value)) {
                star.classList.add('active');
            }
        });
    }

    stars.forEach((star, index) => {
        // Hover effect
        star.addEventListener('mouseenter', () => {
            const value = star.dataset.value;
            stars.forEach((s, i) => {
                if (i < value) {
                    s.classList.add('hover');
                } else {
                    s.classList.remove('hover');
                }
            });
            updateRatingText(value);
        });

        // Click event
        star.addEventListener('click', () => {
            const value = star.dataset.value;
            ratingValue.value = value;
            stars.forEach((s, i) => {
                if (i < value) {
                    s.classList.add('active');
                } else {
                    s.classList.remove('active');
                }
            });
            updateRatingText(value);
        });
    });

    // Remove hover effect when mouse leaves rating area
    document.querySelector('.star-rating').addEventListener('mouseleave', () => {
        stars.forEach(s => s.classList.remove('hover'));
        updateRatingText(ratingValue.value);
    });

    // Form validation
    document.getElementById('reviewForm').addEventListener('submit', function(e) {
        if (!ratingValue.value) {
            e.preventDefault();
            alert('Vui lòng chọn số sao đánh giá');
        }
    });
});
</script>

<?php include('./includes/footer.php'); ?> 