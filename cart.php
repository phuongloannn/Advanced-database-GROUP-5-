<?php
include('functions/userfunctions.php');
include('includes/header.php');
include('middleware/authenticate.php');
?>

<div class="py-3 bg-primary">
    <div class="container">
        <h6 class="text-white">
            <a href="index.php" class="text-white">Trang chủ</a> / 
            <a href="cart.php" class="text-white">Giỏ hàng</a>
        </h6>
    </div>
</div>

<div class="py-5">
    <div class="container">
        <?php
        if(isset($_SESSION['message'])) {
            ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?= $_SESSION['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php
            unset($_SESSION['message']);
        }
        ?>
        <div class="card card-body shadow">
            <div class="row">
                <div class="col-md-12">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <h6>Sản phẩm</h6>
                        </div>
                        <div class="col-md-2">
                            <h6>Giá</h6>
                        </div>
                        <div class="col-md-2">
                            <h6>Số lượng</h6>
                        </div>
                        <div class="col-md-2">
                            <h6>Thành tiền</h6>
                        </div>
                        <div class="col-md-1">
                            <h6>Xóa</h6>
                        </div>
                    </div>
                    <?php
                    $total = 0;
                    if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                        foreach($_SESSION['cart'] as $id => $item) {
                            $total += $item['price'] * $item['quantity'];
                    ?>
                        <div class="card product_data shadow-sm mb-3">
                            <div class="row align-items-center p-2">
                                <div class="col-md-2">
                                    <img src="anh_xedap/<?= $item['image'] ?>" alt="<?= $item['name'] ?>" class="w-100">
                                </div>
                                <div class="col-md-3">
                                    <h5><?= $item['name'] ?></h5>
                                </div>
                                <div class="col-md-2">
                                    <h6 class="price-value"><?= number_format($item['price'], 0, ",", ".") ?> VND</h6>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group">
                                        <input type="hidden" class="prod_id" value="<?= $id ?>">
                                        <button class="input-group-text decrement-btn updateQty">-</button>
                                        <?php
                                        // Lấy số lượng tồn kho của sản phẩm
                                        $stock_query = "SELECT qty FROM products WHERE id = ?";
                                        $stmt = mysqli_prepare($con, $stock_query);
                                        mysqli_stmt_bind_param($stmt, "i", $id);
                                        mysqli_stmt_execute($stmt);
                                        $stock_result = mysqli_stmt_get_result($stmt);
                                        $stock = mysqli_fetch_assoc($stock_result)['qty'];
                                        ?>
                                        <input type="text" class="form-control text-center input-qty bg-white" 
                                               value="<?= $item['quantity'] ?>" 
                                               data-max="<?= $stock ?>" 
                                               readonly>
                                        <button class="input-group-text increment-btn updateQty">+</button>
                                    </div>
                                    <small class="text-muted">Còn <?= $stock ?> sản phẩm</small>
                                </div>
                                <div class="col-md-2">
                                    <h6 class="subtotal"><?= number_format($item['price'] * $item['quantity'], 0, ",", ".") ?> VND</h6>
                                </div>
                                <div class="col-md-1">
                                    <form action="functions/handlecart.php" method="POST">
                                        <input type="hidden" name="scope" value="delete">
                                        <input type="hidden" name="prod_id" value="<?= $id ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">
                                            <i class="fa fa-trash"></i> Xóa
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php
                        }
                    ?>
                        <div class="float-end">
                            <h4>Tổng tiền: <span class="text-success fw-bold total-amount"><?= number_format($total, 0, ",", ".") ?> VND</span></h4>
                            
                            <div class="mt-3">
                                <form action="functions/ordercode.php" method="POST">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold">Phương thức thanh toán</label>
                                        <div class="payment-methods">
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod" checked>
                                                <label class="form-check-label" for="cod">
                                                    <i class="fas fa-money-bill-wave text-success me-2"></i>
                                                    COD (Thanh toán khi nhận hàng)
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_method" id="vnpay" value="vnpay">
                                                <label class="form-check-label" for="vnpay">
                                                    <i class="fab fa-cc-visa text-primary me-2"></i>
                                                    Thanh toán qua VNPAY
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="products.php" class="btn btn-outline-primary">
                                            <i class="fas fa-shopping-bag me-2"></i>Tiếp tục mua sắm
                                        </a>
                                        <button type="submit" name="buy_product" class="btn btn-primary">
                                            <i class="fas fa-shopping-cart me-2"></i>Đặt hàng
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php
                    } else {
                    ?>
                        <div class="card card-body shadow-sm text-center py-5">
                            <h4>Giỏ hàng của bạn đang trống</h4>
                            <div class="mt-3">
                                <a href="index.php" class="btn btn-primary">Tiếp tục mua sắm</a>
                            </div>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-methods {
    background-color: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.payment-methods .form-check {
    padding: 10px 15px;
    border-radius: 5px;
    transition: background-color 0.2s;
}

.payment-methods .form-check:hover {
    background-color: #e9ecef;
}

.payment-methods .form-check-input:checked + .form-check-label {
    font-weight: bold;
}

.gap-2 {
    gap: 0.5rem;
}
</style>

<script>
$(document).ready(function() {
    console.log('Document ready!'); // Debug log

    // Xử lý xóa sản phẩm
    $(document).on('click', '.delete-item', function(e) {
        e.preventDefault();
        console.log('Delete button clicked'); // Debug log
        
        var $btn = $(this);
        var prod_id = $btn.data('id');
        console.log('Product ID:', prod_id); // Debug log

        if (!prod_id) {
            console.error('No product ID found!');
            return;
        }

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
                console.log('Delete confirmed for product:', prod_id); // Debug log
                
                // Disable nút khi đang xử lý
                $btn.prop('disabled', true);
                $btn.html('<i class="fas fa-spinner fa-spin"></i>');

                // Thực hiện AJAX call
                $.ajax({
                    method: "POST",
                    url: "functions/handlecart.php",
                    data: {
                        "scope": "delete",
                        "prod_id": prod_id
                    },
                    beforeSend: function() {
                        console.log('Sending delete request...'); // Debug log
                    },
                    success: function(response) {
                        console.log('Server response:', response); // Debug log
                        
                        try {
                            // Nếu response là string, parse thành JSON
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }

                            if(response.status == 200) {
                                // Xóa phần tử khỏi DOM với hiệu ứng fade
                                $btn.closest('.product_data').fadeOut(300, function() {
                                    $(this).remove();
                                    
                                    // Nếu không còn sản phẩm nào, reload trang
                                    if($('.product_data').length === 0) {
                                        location.reload();
                                    } else {
                                        // Cập nhật tổng tiền
                                        updateTotalPrice();
                                    }
                                });

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Thành công!',
                                    text: response.message || 'Đã xóa sản phẩm khỏi giỏ hàng',
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                            } else {
                                throw new Error(response.message || 'Lỗi không xác định');
                            }
                        } catch (error) {
                            console.error('Error processing response:', error); // Debug log
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi!',
                                text: error.message || 'Có lỗi xảy ra khi xóa sản phẩm'
                            });
                            // Khôi phục nút về trạng thái ban đầu
                            $btn.prop('disabled', false);
                            $btn.html('<i class="fa fa-trash me-1"></i>Xóa');
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
                        
                        // Khôi phục nút về trạng thái ban đầu
                        $btn.prop('disabled', false);
                        $btn.html('<i class="fa fa-trash me-1"></i>Xóa');
                    }
                });
            }
        });
    });

    // Cập nhật số lượng giỏ hàng
    function updateCartCount() {
        $.get("functions/cart-count.php", function(count) {
            $('.cart-count').text(count);
        });
    }

    // Cập nhật tổng tiền
    function updateTotalPrice() {
        var total = 0;
        $('.product_data').each(function() {
            var price = parseFloat($(this).find('.price-value').text().replace(/[^\d]/g, ''));
            var quantity = parseInt($(this).find('.input-qty').val());
            total += price * quantity;
            
            // Cập nhật thành tiền cho từng sản phẩm
            var subtotal = price * quantity;
            $(this).find('.subtotal').text(formatCurrency(subtotal));
        });
        $('.total-amount').text(formatCurrency(total));
    }

    // Format tiền tệ
    function formatCurrency(amount) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(amount).replace('₫', 'VND');
    }

    // Xử lý tăng giảm số lượng
    $('.updateQty').click(function() {
        var $btn = $(this);
        var $row = $btn.closest('.product_data');
        var $input = $row.find('.input-qty');
        var currentQty = parseInt($input.val());
        var maxQty = parseInt($input.data('max'));
        var prod_id = $row.find('.prod_id').val();
        
        if($btn.hasClass('increment-btn')) {
            if(currentQty >= maxQty) {
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
            currentQty++;
        } else if($btn.hasClass('decrement-btn') && currentQty > 1) {
            currentQty--;
        } else {
            return;
        }

        $.ajax({
            method: "POST",
            url: "functions/handlecart.php",
            data: {
                "scope": "update",
                "prod_id": prod_id,
                "prod_qty": currentQty
            },
            dataType: 'json',
            success: function(response) {
                if(response.status == 200) {
                    $input.val(currentQty);
                    updateTotalPrice();
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
    });
});
</script>

<?php include('includes/footer.php'); ?>