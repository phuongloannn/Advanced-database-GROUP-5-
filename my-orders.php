<?php
include("./includes/header.php");
include('functions/userfunctions.php');

if (!isset($_SESSION['auth_user'])) {
    redirect("login.php", "Vui lòng đăng nhập để xem đơn hàng");
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đơn hàng của tôi - Q-Fashion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css"/>
    <style>
        .order-card {
            background: #fff;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 15px;
            overflow: hidden;
        }
        .order-header {
            background: #f8f9fa;
            padding: 10px 15px;
            border-bottom: 1px solid #dee2e6;
        }
        .order-body {
            padding: 15px;
        }
        .order-footer {
            background: #f8f9fa;
            padding: 10px 15px;
            border-top: 1px solid #dee2e6;
        }
        .product-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .product-item:last-child {
            border-bottom: none;
        }
        .btn-cancel {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-cancel:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <?php include('includes/navbar.php'); ?>

    <div class="container py-5">
        <h2 class="mb-4">Đơn hàng của tôi</h2>

        <?php
        $userId = $_SESSION['auth_user']['id'];
        $orders = getOrderWasBuy();

        if(mysqli_num_rows($orders) > 0) {
            while($order = mysqli_fetch_assoc($orders)) {
                ?>
                <div class="order-card">
                    <div class="order-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Đơn hàng #<?= $order['order_id'] ?></h5>
                            <span class="badge <?php 
                                switch($order['status']) {
                                    case 0:
                                        echo 'badge-warning">Chờ xử lý';
                                        break;
                                    case 1:
                                        echo 'badge-primary">Đã duyệt';
                                        break;
                                    case 2:
                                        echo 'badge-info">Đang giao';
                                        break;
                                    case 3:
                                        echo 'badge-success">Đã hoàn thành';
                                        break;
                                    case 4:
                                        echo 'badge-danger">Đã hủy';
                                        break;
                                    default:
                                        echo 'badge-secondary">Không xác định';
                                }
                            ?>
                            </span>
                        </div>
                        <small class="text-muted">Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></small>
                    </div>

                    <div class="order-body">
                        <div class="product-item">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <img src="anh_xedap/<?= $order['image'] ?>" alt="<?= htmlspecialchars($order['name']) ?>" class="img-fluid">
                                </div>
                                <div class="col-md-6">
                                    <h6>
                                        <a href="product-detail.php?slug=<?= $order['slug'] ?>" class="text-dark">
                                            <?= $order['name'] ?>
                                        </a>
                                    </h6>
                                    <small>Số lượng: <?= $order['quantity'] ?></small>
                                </div>
                                <div class="col-md-4 text-right">
                                    <h6><?= number_format($order['selling_price'], 0, ',', '.') ?> VND</h6>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="order-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <?php if($order['status'] == 0) { ?>
                                <button class="btn btn-danger" onclick="showCancelForm(<?= $order['order_id'] ?>)">
                                    <i class="fas fa-times-circle"></i> Hủy đơn hàng
                                </button>
                            <?php } ?>
                            <?php if($order['status'] == 3) { ?>
                                <a href="product-detail.php?slug=<?= $order['slug'] ?>#review-section" class="btn btn-primary">
                                    <i class="fas fa-star"></i> Đánh giá sản phẩm
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php
            }
        } else {
            echo "<p class='text-center'>Bạn chưa có đơn hàng nào.</p>";
        }
        ?>
    </div>

    <!-- Modal Hủy đơn hàng -->
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hủy đơn hàng</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="cancelForm">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="cancel_order_id">
                        <div class="form-group">
                            <label>Lý do hủy đơn</label>
                            <textarea name="cancel_reason" class="form-control" rows="3" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>

    <script>
        $(document).ready(function() {
            // Kiểm tra nếu có order_id trong URL và hash là #cancelModal
            const urlParams = new URLSearchParams(window.location.search);
            const orderId = urlParams.get('order_id');
            if (orderId && window.location.hash === '#cancelModal') {
                showCancelForm(orderId);
            }

            $('#cancelForm').on('submit', function(e) {
                e.preventDefault();
                
                var formData = {
                    order_id: $('#cancel_order_id').val(),
                    cancel_reason: $('textarea[name="cancel_reason"]').val()
                };

                $.ajax({
                    url: 'functions/handle-order.php',
                    type: 'POST',
                    data: formData,
                    dataType: 'json',
                    success: function(response) {
                        if(response.status == 200) {
                            alertify.success(response.message);
                            setTimeout(function() {
                                // Nếu được chuyển từ view-order.php, quay lại trang đó
                                if (document.referrer.includes('view-order.php')) {
                                    window.location.href = document.referrer;
                                } else {
                                    location.reload();
                                }
                            }, 1500);
                        } else {
                            alertify.error(response.message);
                        }
                        $('#cancelModal').modal('hide');
                    },
                    error: function() {
                        alertify.error('Có lỗi xảy ra khi hủy đơn hàng');
                        $('#cancelModal').modal('hide');
                    }
                });
            });
        });

        function showCancelForm(orderId) {
            $('#cancel_order_id').val(orderId);
            $('#cancelModal').modal('show');
        }

        function reviewProduct(orderId) {
            window.location.href = `review.php?order_id=${orderId}`;
        }
    </script>
</body>
</html> 