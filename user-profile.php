<?php 
include('includes/header.php');
include('functions/userfunctions.php');

// Kiểm tra đăng nhập
if (!isset($_SESSION['auth'])) {
    $_SESSION['message'] = "Vui lòng đăng nhập để xem thông tin cá nhân";
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['auth_user']['id'];

// Lấy thông tin user từ database
$query = "SELECT u.*, 
          p.name as province_name, 
          d.name as district_name
          FROM users u 
          LEFT JOIN provinces p ON u.province_id = p.id
          LEFT JOIN districts d ON u.district_id = d.id 
          WHERE u.id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
} else {
    $_SESSION['message'] = "Đã xảy ra lỗi khi lấy thông tin người dùng";
    header('Location: index.php');
    exit();
}

// Lấy số lượng đơn hàng của user
$orderQuery = "SELECT COUNT(*) as order_count FROM orders WHERE user_id = ?";
$stmt = mysqli_prepare($con, $orderQuery);
mysqli_stmt_bind_param($stmt, "i", $userId);
mysqli_stmt_execute($stmt);
$orderResult = mysqli_stmt_get_result($stmt);
$orderCount = mysqli_fetch_assoc($orderResult)['order_count'];

// Xử lý cập nhật thông tin
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $address = mysqli_real_escape_string($con, $_POST['address']);

    // Bắt đầu transaction
    mysqli_begin_transaction($con);

    try {
        // Cập nhật thông tin trong bảng users
        $update_user = "UPDATE users SET 
                       name = ?, 
                       email = ?, 
                       phone = ?, 
                       address = ? 
                       WHERE id = ?";
        
        $stmt = mysqli_prepare($con, $update_user);
        mysqli_stmt_bind_param($stmt, "ssssi", $name, $email, $phone, $address, $userId);
        mysqli_stmt_execute($stmt);

        // Cập nhật thông tin trong các đơn hàng chưa hoàn thành
        $update_orders = "UPDATE orders SET 
                         user_name = ?,
                         user_email = ?,
                         user_phone = ?,
                         user_address = ?
                         WHERE user_id = ? AND status NOT IN ('Completed', 'Cancelled')";
        
        $stmt = mysqli_prepare($con, $update_orders);
        mysqli_stmt_bind_param($stmt, "ssssi", $name, $email, $phone, $address, $userId);
        mysqli_stmt_execute($stmt);

        // Commit transaction
        mysqli_commit($con);

        $_SESSION['message'] = "Cập nhật thông tin thành công!";
        $_SESSION['auth_user']['name'] = $name;
        $_SESSION['auth_user']['email'] = $email;
        $_SESSION['auth_user']['phone'] = $phone;
        $_SESSION['auth_user']['address'] = $address;

    } catch (Exception $e) {
        // Rollback nếu có lỗi
        mysqli_rollback($con);
        $_SESSION['message'] = "Có lỗi xảy ra khi cập nhật thông tin!";
    }

    header('Location: user-profile.php');
    exit();
}
?>

<style>
    .profile-container {
        padding: 30px 0;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
    
    .profile-image-container {
        position: relative;
        width: 180px;
        height: 180px;
        margin: 0 auto 20px;
        border-radius: 50%;
        padding: 5px;
        background: linear-gradient(45deg, #2196F3, #3F51B5);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    
    .profile-image {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
    }
    
    .profile-name {
        color: #2c3e50;
        font-size: 24px;
        font-weight: 600;
        margin: 10px 0;
    }
    
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
    }
    
    .card-header {
        background: linear-gradient(45deg, #2196F3, #3F51B5);
        color: white;
        border-radius: 15px 15px 0 0 !important;
        padding: 15px 20px;
    }
    
    .stat-card {
        background: white;
        border-radius: 10px;
        padding: 15px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }
    
    .stat-icon {
        font-size: 24px;
        color: #3F51B5;
        margin-bottom: 10px;
    }
    
    .stat-info h3 {
        color: #2c3e50;
        font-size: 22px;
        margin: 0;
    }
    
    .stat-info p {
        color: #7f8c8d;
        margin: 5px 0 0;
        font-size: 14px;
    }
    
    .table {
        margin: 0;
    }
    
    .table thead th {
        background-color: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        color: #2c3e50;
        font-weight: 600;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        padding: 8px 12px;
        font-weight: 500;
        border-radius: 20px;
    }
    
    .btn-outline-primary {
        border-radius: 20px;
        padding: 5px 15px;
        font-size: 14px;
        transition: all 0.3s ease;
    }
    
    .btn-outline-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
    }
    
    .text-muted {
        color: #6c757d !important;
    }
    
    .mb-0 h6 {
        color: #2c3e50;
        font-weight: 600;
    }
    
    .row.mb-3 {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
    }
    
    .row.mb-3:last-child {
        border-bottom: none;
    }
    
    @media (max-width: 768px) {
        .profile-image-container {
            width: 150px;
            height: 150px;
        }
        
        .stat-card {
            margin-bottom: 15px;
        }
    }
</style>

<div class="py-3 bg-primary">
    <div class="container">
        <h6 class="text-white">
            <a href="index.php" class="text-white text-decoration-none">Trang chủ</a> / 
            <span class="text-white-50">Thông tin cá nhân</span>
        </h6>
    </div>
</div>

<div class="profile-container">
    <div class="container">
        <div class="card shadow">
            <div class="card-body">
                <div class="row">
                    <!-- Thông tin cá nhân -->
                    <div class="col-md-4">
                        <div class="text-center mb-4">
                            <div class="profile-image-container mb-3">
                                <?php if(!empty($user['avatar'])): ?>
                                    <img src="<?= htmlspecialchars($user['avatar']) ?>" class="profile-image" alt="Avatar">
                                <?php else: ?>
                                    <img src="assets/images/user-avatar.png" class="profile-image" alt="Default Avatar">
                                <?php endif; ?>
                            </div>
                            <h4 class="profile-name"><?= htmlspecialchars($user['name']) ?></h4>
                            <p class="text-muted mb-1"><?= htmlspecialchars($user['email']) ?></p>
                            <p class="text-muted">Thành viên từ: 
                                <?php 
                                if(isset($user['created_at']) && $user['created_at']) {
                                    echo date('d/m/Y', strtotime($user['created_at']));
                                } else {
                                    echo date('d/m/Y');
                                }
                                ?>
                            </p>
                        </div>

                        <!-- Thống kê -->
                        <div class="stats-container">
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="stat-card">
                                        <div class="stat-icon">
                                            <i class="fas fa-shopping-bag"></i>
                                        </div>
                                        <div class="stat-info">
                                            <h3><?= $orderCount ?></h3>
                                            <p>Đơn hàng</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-card">
                                        <div class="stat-icon">
                                            <i class="fas fa-star"></i>
                                        </div>
                                        <div class="stat-info">
                                            <h3>0</h3>
                                            <p>Đánh giá</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form cập nhật thông tin -->
                    <div class="col-md-8">
                        <div class="card border-0">
                            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Thông tin cá nhân</h5>
                                <div>
                                    <a href="auth/forgot-password.php" class="btn btn-warning me-2">
                                        <i class="fas fa-key"></i> Đổi mật khẩu
                                    </a>
                                    <a href="update-profile.php" class="btn btn-primary">
                                        <i class="fas fa-user-edit"></i> Cập nhật thông tin
                                    </a>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Họ tên</h6>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="text-muted mb-0"><?= htmlspecialchars($user['name']) ?></p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Email</h6>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="text-muted mb-0"><?= htmlspecialchars($user['email']) ?></p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Số điện thoại</h6>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="text-muted mb-0"><?= htmlspecialchars($user['phone']) ?></p>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-3">
                                        <h6 class="mb-0">Địa chỉ</h6>
                                    </div>
                                    <div class="col-sm-9">
                                        <p class="text-muted mb-0">
                                            <?php
                                            $address_parts = array();
                                            
                                            if(!empty($user['street_address'])) {
                                                $address_parts[] = htmlspecialchars($user['street_address']);
                                            }
                                            
                                            if(!empty($user['district_name'])) {
                                                $address_parts[] = htmlspecialchars($user['district_name']);
                                            }
                                            
                                            if(!empty($user['province_name'])) {
                                                $address_parts[] = htmlspecialchars($user['province_name']);
                                            }
                                            
                                            echo !empty($address_parts) ? implode(", ", $address_parts) : "Chưa cập nhật địa chỉ";
                                            ?>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Lịch sử đơn hàng gần đây -->
                        <div class="card border-0 mt-4">
                            <div class="card-header bg-transparent border-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-0"><i class="fas fa-history me-2"></i>Đơn hàng gần đây</h5>
                                    <a href="my-orders.php" class="btn btn-outline-primary btn-sm">Xem tất cả</a>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead class="bg-light">
                                            <tr>
                                                <th>Mã ĐH</th>
                                                <th>Sản phẩm</th>
                                                <th>Ngày đặt</th>
                                                <th>Tổng tiền</th>
                                                <th>Trạng thái</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Lấy thông tin đơn hàng
                                            $recentOrders = "SELECT o.* FROM orders o 
                                                WHERE o.user_id = ? 
                                                ORDER BY o.created_at DESC 
                                                LIMIT 5";
                                            $stmt = mysqli_prepare($con, $recentOrders);
                                            mysqli_stmt_bind_param($stmt, "i", $userId);
                                            mysqli_stmt_execute($stmt);
                                            $ordersResult = mysqli_stmt_get_result($stmt);

                                            if(mysqli_num_rows($ordersResult) > 0) {
                                                while($order = mysqli_fetch_assoc($ordersResult)) {
                                                    // Lấy sản phẩm của đơn hàng
                                                    $orderItemsQuery = "SELECT p.name, od.quantity 
                                                        FROM order_detail od 
                                                        JOIN products p ON od.product_id = p.id 
                                                        WHERE od.order_id = ?";
                                                    $itemStmt = mysqli_prepare($con, $orderItemsQuery);
                                                    mysqli_stmt_bind_param($itemStmt, "i", $order['id']);
                                                    mysqli_stmt_execute($itemStmt);
                                                    $itemsResult = mysqli_stmt_get_result($itemStmt);
                                                    
                                                    $productList = array();
                                                    while($item = mysqli_fetch_assoc($itemsResult)) {
                                                        $productList[] = $item['name'] . ' (x' . $item['quantity'] . ')';
                                                    }
                                                    $products = implode(', ', $productList);
                                                    
                                                    if (strlen($products) > 50) {
                                                        $products = substr($products, 0, 47) . '...';
                                                    }
                                                    ?>
                                                    <tr>
                                                        <td>#<?= str_pad($order['id'], 3, '0', STR_PAD_LEFT) ?></td>
                                                        <td>
                                                            <?php if(!empty($products)): ?>
                                                                <?= $products ?>
                                                            <?php else: ?>
                                                                <span class="text-muted">Không có sản phẩm</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                                                        <td class="fw-bold"><?= number_format($order['total_price'], 0, ",", ".") ?> VND</td>
                                                        <td>
                                                            <?php
                                                            $statusClass = '';
                                                            $statusText = '';
                                                            switch($order['status']) {
                                                                case 0:
        $statusClass = 'bg-warning';
        $statusText = 'Chờ xử lý';
        break;
    case 1:
        $statusClass = 'bg-info';
        $statusText = 'Đang xử lý';
        break;
    case 2:
        $statusClass = 'bg-primary';
        $statusText = 'Đang giao';
        break;
    case 3:
        $statusClass = 'bg-success';
        $statusText = 'Hoàn thành';
        break;
    case 4:
        $statusClass = 'bg-danger';
        $statusText = 'Đã hủy';
        break;
                                                            }
                                                            ?>
                                                            <span class="badge <?= $statusClass ?>"><?= $statusText ?></span>
                                                        </td>
                                                        <td>
                                                            <a href="view-order.php?id=<?= $order['id'] ?>" 
                                                               class="btn btn-sm btn-outline-primary">
                                                                Chi tiết
                                                            </a>
                                                        </td>
                                                    </tr>
                                                    <?php
                                                }
                                            } else {
                                                echo "<tr><td colspan='6' class='text-center'>Chưa có đơn hàng nào</td></tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>