$(document).ready(function() {
    // Xử lý khi người dùng chọn số sao
    $('.rating input').change(function() {
        $(this).parent().find('label').removeClass('active');
        $(this).next('label').addClass('active');
    });

    // Xử lý khi submit form đánh giá
    $('.review-form').submit(function(e) {
        e.preventDefault();
        
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalBtnText = submitBtn.text();
        
        // Disable nút submit và hiển thị trạng thái loading
        submitBtn.prop('disabled', true).text('Đang gửi...');

        $.ajax({
            url: 'functions/ordercode.php',
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Hiển thị thông báo thành công
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: response.message,
                        showConfirmButton: false,
                        timer: 2000
                    });

                    // Cập nhật UI
                    const reviewsContainer = $('#product-reviews');
                    const newReview = `
                        <div class="review-item">
                            <div class="review-header">
                                <div class="user-info">
                                    <img src="${response.review.avatar || 'assets/images/default-avatar.png'}" alt="Avatar" class="avatar">
                                    <span class="user-name">${response.review.user_name}</span>
                                </div>
                                <div class="rating">
                                    ${generateStars(response.review.rating)}
                                </div>
                            </div>
                            <div class="review-content">
                                <p>${response.review.comment}</p>
                            </div>
                            <div class="review-footer">
                                <span class="review-date">${response.review.created_at}</span>
                            </div>
                        </div>
                    `;
                    reviewsContainer.prepend(newReview);

                    // Cập nhật số đánh giá và điểm trung bình
                    $('.average-rating').text(response.average_rating);
                    $('.total-reviews').text(response.total_reviews);
                    
                    // Reset form
                    form[0].reset();
                    form.find('label').removeClass('active');
                    
                    // Ẩn form đánh giá
                    form.closest('.review-section').hide();
                } else {
                    // Hiển thị thông báo lỗi
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: response.message
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Có lỗi xảy ra khi gửi đánh giá. Vui lòng thử lại sau.'
                });
            },
            complete: function() {
                // Restore nút submit về trạng thái ban đầu
                submitBtn.prop('disabled', false).text(originalBtnText);
            }
        });
    });
});

// Hàm tạo HTML cho số sao
function generateStars(rating) {
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<i class="fas fa-star"></i>';
        } else {
            stars += '<i class="far fa-star"></i>';
        }
    }
    return stars;
} 