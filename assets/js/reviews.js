// Xử lý lọc đánh giá
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-button');
    const reviewItems = document.querySelectorAll('.review-item');

    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Xóa trạng thái active của tất cả các nút
            filterButtons.forEach(btn => btn.classList.remove('active'));
            // Thêm trạng thái active cho nút được click
            this.classList.add('active');

            const filter = this.dataset.filter;
            
            reviewItems.forEach(item => {
                if (filter === 'all') {
                    item.style.display = 'block';
                } else if (filter === 'images') {
                    item.style.display = item.dataset.hasImages === '1' ? 'block' : 'none';
                } else if (filter === 'verified') {
                    item.style.display = item.dataset.verified === '1' ? 'block' : 'none';
                } else {
                    item.style.display = item.dataset.rating === filter ? 'block' : 'none';
                }
            });
        });
    });
});

// Xử lý xem trước ảnh khi upload
document.getElementById('review-images')?.addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    Array.from(this.files).forEach(file => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-image';
                preview.appendChild(img);
            }
            reader.readAsDataURL(file);
        }
    });
});

// Xử lý đánh giá sao trong form
const ratingInputs = document.querySelectorAll('.rating-input input');
const ratingLabels = document.querySelectorAll('.rating-input label');

ratingInputs.forEach((input, index) => {
    input.addEventListener('change', function() {
        ratingLabels.forEach((label, i) => {
            if (i <= index) {
                label.style.color = '#ffc107';
            } else {
                label.style.color = '#ddd';
            }
        });
    });
});

// Xử lý xem ảnh full size
function showImagePreview(src) {
    const modal = document.getElementById('imagePreviewModal');
    const modalImg = document.getElementById('previewImage');
    modalImg.src = src;
    new bootstrap.Modal(modal).show();
}

// Xử lý đánh dấu đánh giá hữu ích
async function markHelpful(reviewId) {
    try {
        const response = await fetch('functions/handle_helpful.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ review_id: reviewId })
        });

        const data = await response.json();
        
        if (data.status === 'success') {
            const helpfulCount = document.querySelector(`[data-review-id="${reviewId}"] + .helpful-count`);
            helpfulCount.textContent = `${data.helpful_count} người thấy hữu ích`;
            
            const helpfulBtn = document.querySelector(`[data-review-id="${reviewId}"]`);
            if (data.action === 'added') {
                helpfulBtn.classList.add('active');
            } else {
                helpfulBtn.classList.remove('active');
            }
        }
    } catch (error) {
        console.error('Error:', error);
    }
}

// Hiệu ứng loading khi lọc đánh giá
function showLoading() {
    const skeleton = document.querySelector('.review-skeleton');
    const reviewList = document.querySelector('.review-list');
    
    reviewList.style.opacity = '0.5';
    skeleton.style.display = 'block';
}

function hideLoading() {
    const skeleton = document.querySelector('.review-skeleton');
    const reviewList = document.querySelector('.review-list');
    
    reviewList.style.opacity = '1';
    skeleton.style.display = 'none';
} 