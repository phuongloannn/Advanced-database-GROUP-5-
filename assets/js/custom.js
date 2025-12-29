$(document).ready(function() {
    console.log('custom.js loaded');
    
    // Xử lý thêm vào giỏ hàng
    $(document).on('click', '.addToCartBtn', function(e) {
        e.preventDefault();
        console.log('Add to cart button clicked');

        var $btn = $(this);
        var $productData = $btn.closest('.product_data');
        
        // Debug: Kiểm tra container
        console.log('Product data container found:', $productData.length > 0);
        
        // Lấy thông tin sản phẩm
        var prod_id = $productData.find('.prod_id').val();
        var prod_qty = $productData.find('.input-qty').val();
        var prod_name = $productData.find('.prod_name').val();
        var prod_price = $productData.find('.prod_price').val();
        var prod_image = $productData.find('.prod_image').val();

        // Debug: In ra thông tin sản phẩm
        console.log('Product data collected:', {
            id: prod_id,
            name: prod_name,
            price: prod_price,
            image: prod_image,
            qty: prod_qty
        });

        // Validate dữ liệu
        if (!prod_id || !prod_qty || !prod_name || !prod_price || !prod_image) {
            console.error('Missing product data:', {
                id: !prod_id ? 'missing' : 'ok',
                qty: !prod_qty ? 'missing' : 'ok',
                name: !prod_name ? 'missing' : 'ok',
                price: !prod_price ? 'missing' : 'ok',
                image: !prod_image ? 'missing' : 'ok'
            });
            
            Swal.fire({
                icon: 'error',
                title: 'Lỗi!',
                text: 'Thiếu thông tin sản phẩm. Vui lòng tải lại trang và thử lại.'
            });
            return;
        }

        // Disable nút và hiển thị loading
        $btn.prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...');

        // Gửi request AJAX
        $.ajax({
            method: "POST",
            url: "functions/handlecart.php",
            data: {
                "scope": "add",
                "prod_id": prod_id,
                "prod_qty": prod_qty,
                "prod_name": prod_name,
                "prod_price": prod_price,
                "prod_image": prod_image
            },
            success: function(response) {
                console.log('AJAX success response:', response);
                
                try {
                    if(response.status == 201) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Thêm vào giỏ hàng thành công!',
                            text: response.message,
                            showDenyButton: true,
                            confirmButtonText: 'Xem giỏ hàng',
                            denyButtonText: 'Tiếp tục mua sắm'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'cart.php';
                            }
                        });
                        
                        // Cập nhật số lượng giỏ hàng
                        updateCartCount();
                    }
                    else if (response.status == 401) {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Vui lòng đăng nhập',
                            text: response.message,
                            showCancelButton: true,
                            confirmButtonText: 'Đăng nhập ngay',
                            cancelButtonText: 'Để sau'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'login.php';
                            }
                        });
                    }
                    else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: response.message
                        });
                    }
                } catch(e) {
                    console.error('Error parsing response:', e);
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: 'Có lỗi xảy ra, vui lòng thử lại'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi kết nối!',
                    text: 'Không thể kết nối đến server'
                });
            },
            complete: function() {
                // Enable nút và reset text
                $btn.prop('disabled', false)
                    .html('<i class="fa fa-shopping-cart me-2"></i>Thêm vào giỏ hàng');
            }
        });
    });

    // Hàm cập nhật số lượng giỏ hàng
    function updateCartCount() {
        $.get("functions/cart-count.php", function(count) {
            if(count > 0) {
                $('.cart-count').text(count).show();
            } else {
                $('.cart-count').hide();
            }
        });
    }

    // Xử lý tăng giảm số lượng
    $(document).on('click', '.increment-btn, .decrement-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var $btn = $(this);
        var $row = $btn.closest('.product_data');
        var $input = $btn.siblings('.input-qty');
        var currentQty = parseInt($input.val());
        var maxQty = parseInt($input.data('max'));
        var isIncrement = $btn.hasClass('increment-btn');
        
        // Tính toán số lượng mới
        var newQty = currentQty;
        if (isIncrement) {
            if (currentQty >= maxQty) {
                // Chỉ hiển thị thông báo khi cố gắng tăng số lượng vượt quá tối đa
                Swal.fire({
                    icon: 'warning',
                    title: 'Đã đạt giới hạn',
                    text: 'Bạn đã chọn số lượng tối đa có sẵn trong kho',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
                return;
            }
            newQty = currentQty + 1;
        } else if (!isIncrement && currentQty > 1) {
            newQty = currentQty - 1;
        } else {
            return;
        }

        // Cập nhật giao diện
        $input.val(newQty);
        
        // Thêm hiệu ứng
        $input.addClass('quantity-changed');
        setTimeout(function() {
            $input.removeClass('quantity-changed');
        }, 300);

        // Kiểm tra nếu trong trang giỏ hàng thì gửi request cập nhật
        if ($('.cart-items-container').length > 0) {
            var prod_id = $row.find('.prod_id').val();
            
            // Debug log
            console.log('Updating cart quantity:', {
                prod_id: prod_id,
                newQty: newQty
            });

            $.ajax({
                method: "POST",
                url: "functions/handlecart.php",
                data: {
                    "scope": "update",
                    "prod_id": prod_id,
                    "prod_qty": newQty
                },
                dataType: 'json',
                success: function(response) {
                    console.log('Update quantity response:', response);
                    if(response.status == 200) {
                        location.reload();
                    } else {
                        // Khôi phục số lượng cũ nếu có lỗi
                        $input.val(currentQty);
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi!',
                            text: response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    // Khôi phục số lượng cũ nếu có lỗi
                    $input.val(currentQty);
                    console.error('AJAX error:', {
                        status: status,
                        error: error,
                        response: xhr.responseText
                    });
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi kết nối!',
                        text: 'Không thể kết nối đến server'
                    });
                }
            });
        }
    });

    // Thêm style cho hiệu ứng nhảy số
    $('<style>')
        .text(`
            .quantity-changed {
                animation: quantityChange 0.3s ease;
            }
            @keyframes quantityChange {
                0% { transform: scale(1); }
                50% { transform: scale(1.2); }
                100% { transform: scale(1); }
            }
        `)
        .appendTo('head');

    // Xử lý xóa sản phẩm
    $(document).on('click', '.deleteItem', function() {
        var $btn = $(this);
        var prod_id = $btn.closest('.product_data').find('.prod_id').val();

        Swal.fire({
            title: 'Xác nhận xóa?',
            text: "Bạn có chắc chắn muốn xóa sản phẩm này khỏi giỏ hàng?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Có, xóa ngay!',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "functions/handlecart.php",
                    data: {
                        "scope": "delete",
                        "prod_id": prod_id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if(response.status == 200) {
                            $btn.closest('.product_data').fadeOut(300, function() {
                                $(this).remove();
                                $.get("functions/cart-count.php", function(count) {
                                    $('.cart-count').text(count);
                                });
                                if($('.product_data').length === 0) {
                                    location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', {
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi kết nối!',
                            text: 'Không thể kết nối đến server'
                        });
                    }
                });
            }
        });
    });

    // Hàm kiểm tra đăng nhập
    function isUserLoggedIn() {
        return document.body.classList.contains('user-logged-in');
    }

    // Review Section Functionality
    $(document).ready(function() {
        // Filter buttons
        $('.filter-btn').click(function() {
            const filter = $(this).data('filter');
            $('.filter-btn').removeClass('active');
            $(this).addClass('active');
            
            // Show loading
            $('.review-list .review-item').hide();
            $('.review-skeleton').show();
            
            // Simulate loading delay
            setTimeout(function() {
                $('.review-skeleton').hide();
                
                if (filter === 'latest') {
                    $('.review-item').show();
                } else if (filter === 'has_images') {
                    $('.review-item[data-has-images="1"]').show();
                } else if (filter === 'verified') {
                    $('.review-item[data-verified="1"]').show();
                } else if (filter.startsWith('rating_')) {
                    const rating = filter.split('_')[1];
                    $('.review-item[data-rating="' + rating + '"]').show();
                }
                
                // Show no reviews message if needed
                if ($('.review-item:visible').length === 0) {
                    $('.review-list').append(
                        '<div class="text-center py-5 no-reviews-message">' +
                        '<i class="fas fa-comment-alt fa-3x text-muted mb-3"></i>' +
                        '<p class="text-muted">Không tìm thấy đánh giá nào phù hợp với bộ lọc</p>' +
                        '</div>'
                    );
                } else {
                    $('.no-reviews-message').remove();
                }
            }, 500);
        });

        // Helpful button functionality
        $('.helpful-btn').click(function() {
            if (!isUserLoggedIn()) {
                window.location.href = 'login.php';
                return;
            }
            
            const $btn = $(this);
            const reviewId = $btn.data('review-id');
            const $count = $btn.next('.helpful-count');
            const currentCount = parseInt($count.text());
            
            $.ajax({
                url: 'functions/handle_review.php',
                type: 'POST',
                data: {
                    action: 'toggle_helpful',
                    review_id: reviewId
                },
                success: function(response) {
                    if (response.success) {
                        if (response.added) {
                            $btn.addClass('active');
                            $count.text((currentCount + 1) + ' người thấy hữu ích');
                        } else {
                            $btn.removeClass('active');
                            $count.text((currentCount - 1) + ' người thấy hữu ích');
                        }
                    }
                },
                error: function() {
                    alertify.error('Có lỗi xảy ra. Vui lòng thử lại sau.');
                }
            });
        });

        // Image preview functionality
        $('.review-image').click(function() {
            const imgSrc = $(this).attr('src');
            $('#previewImage').attr('src', imgSrc);
        });

        // Review form image preview
        $('input[name="review_images[]"]').change(function() {
            const files = this.files;
            const $preview = $('#imagePreview');
            $preview.empty();
            
            if (files.length > 5) {
                alertify.error('Bạn chỉ có thể tải lên tối đa 5 ảnh');
                this.value = '';
                return;
            }
            
            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    $preview.append(
                        '<div class="position-relative" style="width: 100px;">' +
                        '<img src="' + e.target.result + '" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover;">' +
                        '<button type="button" class="btn-close position-absolute top-0 end-0" style="background-color: white;"></button>' +
                        '</div>'
                    );
                }
                
                reader.readAsDataURL(file);
            }
        });

        // Remove preview image
        $('#imagePreview').on('click', '.btn-close', function() {
            $(this).parent().remove();
        });

        // Star rating functionality
        $('.rating-input input').change(function() {
            const value = $(this).val();
            $('.rating-input label i').removeClass('active');
            $('.rating-input label:nth-child(-n+' + value + ') i').addClass('active');
        });

        // Form validation
        $('#reviewForm').submit(function(e) {
            e.preventDefault();
            
            const rating = $('input[name="rating"]:checked').val();
            const comment = $('textarea[name="comment"]').val().trim();
            
            if (!rating) {
                alertify.error('Vui lòng chọn số sao đánh giá');
                return;
            }
            
            if (comment.length < 10) {
                alertify.error('Vui lòng viết nhận xét ít nhất 10 ký tự');
                return;
            }
            
            // Submit form
            const formData = new FormData(this);
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#writeReviewModal').modal('hide');
                        alertify.success('Cảm ơn bạn đã đánh giá sản phẩm!');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        alertify.error(response.message || 'Có lỗi xảy ra. Vui lòng thử lại sau.');
                    }
                },
                error: function() {
                    alertify.error('Có lỗi xảy ra. Vui lòng thử lại sau.');
                }
            });
        });
    });
}); 