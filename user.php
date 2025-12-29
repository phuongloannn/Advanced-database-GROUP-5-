<?php 
include("./includes/header.php");

// Database connection
$host = "localhost:3307";
$username = "root";
$password = "";
$database = "fashion_shop_group5";

// Create connection
$con = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['auth_user']['id'])) {
    die("<div class='alert alert-danger text-center'><i class='fas fa-exclamation-circle'></i> Từ chối truy cập</div>");
}

$id = $_SESSION['auth_user']['id'];
$users = getByID("users", $id);
$data = mysqli_fetch_array($users);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin người dùng - Q-Fashion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <style>
        :root {
            --primary-color: #e60000;
            --secondary-color: #333333;
            --background-color: #f8f9fa;
            --border-radius: 15px;
            --box-shadow: 0 0 30px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--background-color);
            color: var(--secondary-color);
        }

        .dashboard-container {
            padding: 2rem 0;
        }

        .user-card {
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2rem;
            margin-bottom: 2rem;
            transition: var(--transition);
        }

        .user-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 40px rgba(0,0,0,0.15);
        }

        .profile-header {
            text-align: center;
            margin-bottom: 2rem;
            position: relative;
        }

        .profile-avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            margin: 0 auto 1.5rem;
            overflow: hidden;
            border: 4px solid #fff;
            box-shadow: var(--box-shadow);
        }

        .profile-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-name {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--secondary-color);
        }

        .profile-email {
            color: #666;
            margin-bottom: 1.5rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-item {
            background: var(--background-color);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            transition: var(--transition);
        }

        .info-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
        }

        .info-label {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }

        .info-label i {
            margin-right: 0.5rem;
            color: var(--primary-color);
        }

        .info-value {
            font-size: 1.1rem;
            font-weight: 500;
        }

        .orders-card {
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            padding: 2rem;
        }

        .orders-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid var(--background-color);
        }

        .orders-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin: 0;
        }

        .order-table {
            margin-bottom: 0;
        }

        .order-table th {
            background: var(--background-color);
            border: none;
            padding: 1rem;
            font-weight: 600;
        }

        .order-table td {
            padding: 1rem;
            vertical-align: middle;
            border-color: #f0f0f0;
        }

        .order-status {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-processing {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-shipping {
            background-color: #d4edda;
            color: #155724;
        }

        .status-completed {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-cancelled {
            background-color: #f8d7da;
            color: #721c24;
        }

        .btn-view {
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius);
            background-color: var(--primary-color);
            color: #fff;
            border: none;
            transition: var(--transition);
            text-decoration: none;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-view:hover {
            background-color: #cc0000;
            color: #fff;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .edit-profile-btn {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            border: none;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .edit-profile-btn:hover {
            background-color: #cc0000;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .empty-orders {
            text-align: center;
            padding: 3rem 0;
        }

        .empty-orders i {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 1.5rem;
        }

        .empty-orders p {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }

        .btn-shop-now {
            background-color: var(--primary-color);
            color: white;
            padding: 0.75rem 2rem;
            border-radius: var(--border-radius);
            text-decoration: none;
            transition: var(--transition);
            display: inline-block;
        }

        .btn-shop-now:hover {
            background-color: #cc0000;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .btn-action {
            padding: 0.375rem 0.75rem;
            border-radius: 0.25rem;
            font-size: 0.875rem;
            line-height: 1.5;
            transition: all 0.15s ease-in-out;
        }

        .btn-cancel {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-pay {
            color: #fff;
            background-color: #28a745;
            border-color: #28a745;
        }

        .modal-content {
            border-radius: var(--border-radius);
        }

        .payment-methods {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .payment-method {
            flex: 1;
            padding: 1rem;
            border: 2px solid #ddd;
            border-radius: var(--border-radius);
            cursor: pointer;
            transition: var(--transition);
        }

        .payment-method:hover,
        .payment-method.selected {
            border-color: var(--primary-color);
            background-color: #fff9f9;
        }

        .payment-method img {
            height: 40px;
            margin-bottom: 0.5rem;
        }

        .product-item {
            margin-bottom: 0.5rem;
            padding: 0.5rem;
            border-bottom: 1px solid #eee;
        }
        .product-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
        }
        .payment-method {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            border: 2px solid #ddd;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .payment-method.selected {
            border-color: #28a745;
            background-color: #f8fff8;
        }
        .payment-method img {
            width: 60px;
            height: auto;
        }
        .payment-method div {
            flex: 1;
        }
        .payment-method input[type="radio"] {
            margin: 0;
        }
        .btn-action {
            margin: 0.25rem;
        }
        .text-muted {
            color: #6c757d;
            font-size: 0.875rem;
        }

        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .user-card, .orders-card {
                padding: 1.5rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }

            .orders-header {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .table-responsive {
                margin: 0 -1.5rem;
                width: calc(100% + 3rem);
            }
        }

        .btn-review {
            background-color: #ffc107;
            color: #000;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0 5px;
        }

        .btn-review:hover {
            background-color: #ffb300;
            transform: translateY(-2px);
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-start;
            gap: 0.5rem;
            margin: 1rem 0;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            color: #ddd;
            font-size: 1.5rem;
            cursor: pointer;
            transition: color 0.2s;
        }

        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #ffc107;
        }

        .review-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            background: #fff;
        }

        .review-item h6 {
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .review-product-info {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .review-product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 4px;
        }

        .review-comment {
            margin-top: 1rem;
        }

        .review-comment textarea {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            resize: vertical;
            min-height: 100px;
            font-size: 0.95rem;
        }

        .review-comment textarea:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
        }

        .rating-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #666;
            font-size: 0.9rem;
        }

        .rating-text {
            margin-left: 1rem;
            color: #666;
            font-size: 0.9rem;
        }

        .modal-lg {
            max-width: 800px;
        }
    </style>
</head>
<body>
    <div class="container dashboard-container">
        <div class="user-card">
            <div class="profile-header">
                <div class="profile-avatar">
                    <?php
                    $avatar_path = !empty($data['avatar']) ? 'uploads/avatars/' . $data['avatar'] : 'assets/images/default-avatar.png';
                    if (!empty($data['avatar']) && !file_exists($avatar_path)) {
                        $avatar_path = 'assets/images/default-avatar.png';
                    }
                    ?>
                    <img src="<?= htmlspecialchars($avatar_path) ?>" alt="Avatar">
                </div>
                <h2 class="profile-name"><?= htmlspecialchars($data['name']) ?></h2>
                <p class="profile-email"><?= htmlspecialchars($data['email']) ?></p>
                <a href="user-profile.php" class="edit-profile-btn">
                    <i class="fas fa-user-edit"></i> Chỉnh sửa thông tin
                </a>
            </div>

            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-envelope"></i>
                        Email
                    </div>
                    <div class="info-value"><?= htmlspecialchars($data['email']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-phone"></i>
                        Số điện thoại
                    </div>
                    <div class="info-value"><?= htmlspecialchars($data['phone']) ?></div>
                </div>
                <div class="info-item">
                    <div class="info-label">
                        <i class="fas fa-map-marker-alt"></i>
                        Địa chỉ
                    </div>
                    <div class="info-value"><?= htmlspecialchars($data['address']) ?></div>
                </div>
            </div>
        </div>

        <div class="orders-card">
            <div class="orders-header">
                <h2 class="orders-title"><i class="fas fa-shopping-bag"></i> Đơn hàng của tôi</h2>
            </div>

            <?php 
            // Lấy danh sách đơn hàng của user
            $orders_query = "SELECT DISTINCT o.*, 
                           (SELECT SUM(od2.selling_price * od2.quantity) 
                            FROM order_detail od2 
                            WHERE od2.order_id = o.id) as total_price
                           FROM orders o
                           WHERE o.user_id = '$id'
                           ORDER BY o.created_at DESC";
            $orders_result = mysqli_query($con, $orders_query);

            if(mysqli_num_rows($orders_result) > 0) {
            ?>
                <div class="table-responsive">
                    <table class="table order-table">
                        <thead>
                            <tr>
                                <th>Mã đơn hàng</th>
                                <th>Sản phẩm</th>
                                <th>Tổng tiền</th>
                                <th>Ngày đặt</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($order = mysqli_fetch_assoc($orders_result)) { 
                                // Lấy danh sách sản phẩm trong đơn hàng
                                $order_id = $order['id'];
                                $products_query = "SELECT p.name, p.slug, od.quantity, od.selling_price 
                                                 FROM order_detail od 
                                                 JOIN products p ON od.product_id = p.id 
                                                 WHERE od.order_id = '$order_id'";
                                $products_result = mysqli_query($con, $products_query);
                            ?>
                                <tr>
                                    <td>#<?= $order['id'] ?></td>
                                    <td>
                                        <?php 
                                        $total = 0;
                                        while($product = mysqli_fetch_assoc($products_result)) { 
                                            $subtotal = $product['selling_price'] * $product['quantity'];
                                            $total += $subtotal;
                                        ?>
                                            <div class="product-item">
                                                <a href="product-detail.php?slug=<?= $product['slug'] ?>">
                                                    <?= htmlspecialchars($product['name']) ?>
                                                </a>
                                                <small class="text-muted">
                                                    (<?= $product['quantity'] ?> x <?= number_format($product['selling_price'], 0, ',', '.') ?> VND)
                                                </small>
                                            </div>
                                        <?php } ?>
                                    </td>
                                    <td><?= number_format($total, 0, ',', '.') ?> VND</td>
                                    <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        $status_text = '';
                                        switch($order['status']) {
                                            case 0:
                                                $status_class = 'warning';
                                                $status_text = 'Chờ xác nhận';
                                                break;
                                            case 1:
                                                $status_class = 'primary';
                                                $status_text = 'Đã xác nhận';
                                                break;
                                            case 2:
                                                $status_class = 'info';
                                                $status_text = 'Đang giao';
                                                break;
                                            case 3:
                                                $status_class = 'success';
                                                $status_text = 'Đã hoàn thành';
                                                break;
                                            case 4:
                                                $status_class = 'danger';
                                                $status_text = 'Đã hủy';
                                                break;
                                        }
                                        ?>
                                        <span class="badge bg-<?= $status_class ?>"><?= $status_text ?></span>
                                    </td>
                                    <td>
                                        <a href="view-order.php?id=<?= $order['id'] ?>" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i> Chi tiết
                                        </a>
                                        <?php if($order['status'] == 0) { ?>
                                            <button type="button" class="btn btn-danger btn-sm" onclick="showCancelModal(<?= $order['id'] ?>)">
                                                <i class="fas fa-times"></i> Hủy đơn
                                            </button>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="empty-orders">
                    <i class="fas fa-shopping-bag"></i>
                    <p>Bạn chưa có đơn hàng nào.</p>
                    <a href="products.php" class="btn-shop-now">
                        <i class="fas fa-shopping-cart"></i>
                        Mua sắm ngay
                    </a>
                </div>
            <?php } ?>
        </div>
                    </div>
                    
    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Chọn phương thức thanh toán</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="paymentForm" method="POST" action="functions/ordercode.php">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="paymentOrderId">
                        <input type="hidden" name="amount" id="paymentAmount">
                        <div class="payment-methods">
                            <div class="payment-method" onclick="selectPaymentMethod('cod')">
                                <img src="assets/images/cod.png" alt="COD">
                                <div>
                                    <strong>Thanh toán khi nhận hàng</strong>
                                    <p class="mb-0">Thanh toán bằng tiền mặt khi nhận hàng</p>
                                </div>
                                <input type="radio" name="payment_method" value="cod" required>
                            </div>
                            <div class="payment-method" onclick="selectPaymentMethod('vnpay')">
                                <img src="assets/images/vnpay.png" alt="VNPay">
                                <div>
                                    <strong>Thanh toán qua VNPay</strong>
                                    <p class="mb-0">Thanh toán bằng thẻ ATM, Visa, MasterCard</p>
                                </div>
                                <input type="radio" name="payment_method" value="vnpay" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" name="process_payment" class="btn btn-success">
                            <i class="fas fa-check"></i> Xác nhận thanh toán
                        </button>
                    </div>
                </form>
            </div>
        </div>
                    </div>
                    
    <!-- Cancel Order Modal -->
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

    <!-- Review Modal -->
    <div class="modal fade" id="reviewModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Đánh giá sản phẩm</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <form id="reviewForm" method="POST" action="functions/ordercode.php">
                    <div class="modal-body">
                        <input type="hidden" name="order_id" id="reviewOrderId">
                        <div id="reviewProducts"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                        <button type="submit" name="submit_review" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Gửi đánh giá
                    </button>
                </div>
            </form>           
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script>
        function showPaymentModal(orderId, amount) {
            $('#paymentOrderId').val(orderId);
            $('#paymentAmount').val(amount);
            $('#paymentModal').modal('show');
        }

        function showCancelModal(orderId) {
            $('#cancel_order_id').val(orderId);
            $('#cancelModal').modal('show');
        }

        function selectPaymentMethod(method) {
            $('.payment-method').removeClass('selected');
            $(`input[value=${method}]`).prop('checked', true)
                .closest('.payment-method').addClass('selected');
        }

        async function showReviewModal(orderId) {
            try {
                const response = await fetch(`get_order_products.php?order_id=${orderId}`);
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                const products = await response.json();
                
                let html = '';
                products.forEach(product => {
                    html += `
                        <div class="review-item">
                            <div class="review-product-info">
                                <img src="anh_xedap/${product.image}" alt="${product.name}" class="review-product-image">
                                <h6>${product.name}</h6>
                            </div>
                            <input type="hidden" name="product_ids[]" value="${product.id}">
                            <div class="rating-section">
                                <span class="rating-label">Đánh giá của bạn:</span>
                                <div class="star-rating" id="rating-${product.id}">
                                    <input type="radio" id="star5-${product.id}" name="rating-${product.id}" value="5" required>
                                    <label for="star5-${product.id}" title="Tuyệt vời"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star4-${product.id}" name="rating-${product.id}" value="4">
                                    <label for="star4-${product.id}" title="Tốt"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star3-${product.id}" name="rating-${product.id}" value="3">
                                    <label for="star3-${product.id}" title="Bình thường"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star2-${product.id}" name="rating-${product.id}" value="2">
                                    <label for="star2-${product.id}" title="Tệ"><i class="fas fa-star"></i></label>
                                    <input type="radio" id="star1-${product.id}" name="rating-${product.id}" value="1">
                                    <label for="star1-${product.id}" title="Rất tệ"><i class="fas fa-star"></i></label>
                                </div>
                                <span class="rating-text" id="rating-text-${product.id}"></span>
                            </div>
                            <div class="review-comment">
                                <textarea name="comment-${product.id}" 
                                        placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm..." 
                                        required></textarea>
                            </div>
                        </div>
                    `;
                });
                
                if (products.length === 0) {
                    html = '<p class="text-center">Bạn đã đánh giá tất cả sản phẩm trong đơn hàng này.</p>';
                }
                
                document.getElementById('reviewOrderId').value = orderId;
                document.getElementById('reviewProducts').innerHTML = html;
                
                // Thêm sự kiện cho các sao đánh giá
                products.forEach(product => {
                    const ratingSection = document.getElementById(`rating-${product.id}`);
                    const ratingText = document.getElementById(`rating-text-${product.id}`);
                    
                    if (ratingSection) {
                        ratingSection.addEventListener('change', (e) => {
                            const value = e.target.value;
                            const texts = {
                                1: 'Rất tệ',
                                2: 'Tệ',
                                3: 'Bình thường',
                                4: 'Tốt',
                                5: 'Tuyệt vời'
                            };
                            ratingText.textContent = texts[value];
                        });
                    }
                });
                
                $('#reviewModal').modal('show');
            } catch (error) {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi tải thông tin sản phẩm. Vui lòng thử lại!');
            }
        }

        $(document).ready(function() {
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
                                location.reload();
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

        <?php if(isset($_SESSION['message'])) { ?>
            alertify.set('notifier','position', 'top-right');
            alertify.success('<?= $_SESSION['message']; ?>');
            <?php unset($_SESSION['message']); ?>
        <?php } ?>
    </script>
</body>
</html>