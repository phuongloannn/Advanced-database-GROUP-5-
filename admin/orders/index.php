<?php
session_start();

// Kiểm tra session auth_user và role
if (!isset($_SESSION['auth_user']) || !isset($_SESSION['auth_user']['role_as']) || $_SESSION['auth_user']['role_as'] != 1) {
    header('Location: ../../auth/login.php');
    exit();
}

require_once '../../includes/db.php';

// Lấy thông tin admin từ session
$admin_name = isset($_SESSION['auth_user']['name']) ? $_SESSION['auth_user']['name'] : 'Admin';

// Hàm chuyển đổi status
function getStatusText($status) {
    $statusMap = [
        0 => 'Chờ xử lý',
        1 => 'Đang xử lý',
        2 => 'Đang giao', 
        3 => 'Hoàn thành',
        4 => 'Đã hủy'
    ];
    return $statusMap[$status] ?? 'Không xác định';
}

function getStatusColor($status) {
    switch($status) {
        case 0: return '#856404'; // Chờ xử lý - vàng đậm
        case 1: return '#055160'; // Đang xử lý - xanh đậm  
        case 2: return '#084298'; // Đang giao - xanh dương đậm
        case 3: return '#0f5132'; // Hoàn thành - xanh lá đậm
        case 4: return '#721c24'; // Đã hủy - đỏ đậm
        default: return '#000000';
    }
}

function getStatusBgColor($status) {
    switch($status) {
        case 0: return '#fff3cd'; // Chờ xử lý - vàng nhạt
        case 1: return '#cff4fc'; // Đang xử lý - xanh nhạt
        case 2: return '#cfe2ff'; // Đang giao - xanh dương nhạt
        case 3: return '#d1e7dd'; // Hoàn thành - xanh lá nhạt
        case 4: return '#f8d7da'; // Đã hủy - đỏ nhạt
        default: return '#ffffff';
    }
}

// Đếm số đơn hàng theo trạng thái
$status_query = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
$status_result = $conn->query($status_query);

$status_counts = [
    0 => 0, // Chờ xử lý
    1 => 0, // Đang xử lý
    2 => 0, // Đang giao
    3 => 0, // Hoàn thành
    4 => 0  // Đã hủy
];

if ($status_result) {
    while ($row = $status_result->fetch_assoc()) {
        $status_counts[$row['status']] = $row['count'];
    }
}

// Lấy danh sách đơn hàng
$query = "SELECT o.*, 
          CONCAT('ORD', LPAD(o.id, 6, '0')) as display_tracking_no,
          u.name as customer_name,
          u.email as customer_email,
          u.phone as customer_phone,
          u.street_address as customer_address 
          FROM orders o 
          JOIN users u ON o.user_id = u.id 
          ORDER BY o.created_at DESC";
$result = $conn->query($query);

if (!$result) {
    die("Query failed: " . $conn->error);
}

// Tính tổng doanh thu từ đơn hàng hoàn thành
$total_revenue = 0;
$revenue_query = "SELECT SUM(total_price) as total FROM orders WHERE status = 2";
$revenue_result = $conn->query($revenue_query);
if ($revenue_result) {
    $total_revenue = $revenue_result->fetch_assoc()['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý Đơn hàng - Q-Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --secondary-color: #858796;
        }

        body {
            background-color: #f8f9fc;
        }

        .sidebar {
            min-width: 250px;
            max-width: 250px;
            background: linear-gradient(180deg, var(--primary-color) 0%, #224abe 100%);
            color: white;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
            transition: all 0.3s;
        }

        .main-content {
            margin-left: 250px;
            padding: 20px;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .stats-card {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            color: white;
        }

        .stats-card.pending {
            background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
        }

        .stats-card.processing {
            background: linear-gradient(45deg, #36b9cc 0%, #1a8eaf 100%);
        }

        .stats-card.completed {
            background: linear-gradient(45deg, #1cc88a 0%, #13855c 100%);
        }

        .stats-card.cancelled {
            background: linear-gradient(45deg, #e74a3b 0%, #be2617 100%);
        }

        .stats-card.shipping {
            background: linear-gradient(45deg, #6f42c1 0%, #4e2d8c 100%);
        }

        .stats-card .icon {
            font-size: 2rem;
            opacity: 0.4;
        }

        .table-card {
            padding: 1.5rem;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 30px;
            font-weight: 500;
            min-width: 120px;
            text-align: center;
        }

        .table > :not(caption) > * > * {
            padding: 1rem;
        }

        .order-actions .btn {
            padding: 0.5rem 1rem;
            border-radius: 5px;
            margin: 0 0.2rem;
        }

        .status-select {
            border-radius: 20px;
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            background-color: white;
            min-width: 150px;
        }

        .customer-info {
            background-color: #f8f9fc;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            margin-bottom: 0.5rem;
        }

        .contact-info {
            font-size: 0.9rem;
            color: #666;
        }

        .search-box {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .search-box input {
            padding: 1rem 1.5rem;
            border-radius: 30px;
            border: 1px solid #ddd;
            width: 100%;
            padding-left: 3rem;
        }

        .search-box i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-4">
            <h4 class="mb-4">Q-Fashion Admin</h4>
            <ul class="nav flex-column">
                <li class="nav-item mb-3">
                    <a href="../dashboard.php" class="nav-link text-white">
                        <i class="fas fa-chart-line me-2"></i> Thống kê
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a href="../products/index.php" class="nav-link text-white">
                        <i class="fas fa-tshirt me-2"></i> Quản lý Sản phẩm
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a href="index.php" class="nav-link text-white active">
                        <i class="fas fa-shopping-cart me-2"></i> Quản lý Đơn hàng
                    </a>
                </li>
                <li class="nav-item mb-3">
                    <a href="../users/index.php" class="nav-link text-white">
                        <i class="fas fa-users me-2"></i> Quản lý Tài khoản
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container-fluid">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Quản lý Đơn hàng</h2>
                <div class="d-flex align-items-center">
                    <span class="me-3">Xin chào, <?php echo htmlspecialchars($admin_name); ?></span>
                    <a href="../../auth/logout.php" class="btn btn-danger">
                        <i class="fas fa-sign-out-alt"></i> Đăng xuất
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stats-card pending h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50 mb-0">Chờ xử lý</h6>
                                    <h2 class="text-white mb-0"><?php echo number_format($status_counts[0]); ?></h2>
                                    <div class="text-white-50">đơn hàng</div>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-clock fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stats-card processing h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50 mb-0">Đang xử lý</h6>
                                    <h2 class="text-white mb-0"><?php echo number_format($status_counts[1]); ?></h2>
                                    <div class="text-white-50">đơn hàng</div>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-spinner fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stats-card shipping h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50 mb-0">Đang giao</h6>
                                    <h2 class="text-white mb-0"><?php echo number_format($status_counts[2]); ?></h2>
                                    <div class="text-white-50">đơn hàng</div>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-shipping-fast fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stats-card completed h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50 mb-0">Hoàn thành</h6>
                                    <h2 class="text-white mb-0"><?php echo number_format($status_counts[3]); ?></h2>
                                    <div class="text-white-50">đơn hàng</div>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-check-circle fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card stats-card cancelled h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="text-white-50 mb-0">Đã hủy</h6>
                                    <h2 class="text-white mb-0"><?php echo number_format($status_counts[4]); ?></h2>
                                    <div class="text-white-50">đơn hàng</div>
                                </div>
                                <div class="icon">
                                    <i class="fas fa-times-circle fa-2x text-white-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="card table-card">
                <div class="card-body">
                    <!-- Search Box -->
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" id="orderSearch" class="form-control" placeholder="Tìm kiếm đơn hàng...">
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Mã đơn hàng</th>
                                    <th>Khách hàng</th>
                                    <th>Thông tin liên hệ</th>
                                    <th>Tổng tiền</th>
                                    <th>Ngày đặt</th>
                                    <th>Trạng thái</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($order = $result->fetch_assoc()): ?>
                                <tr id="order-<?php echo $order['id']; ?>">
                                    <td>
                                        <strong class="text-primary">
                                            <?php echo $order['display_tracking_no']; ?>
                                        </strong>
                                    </td>
                                    <td>
                                        <div class="customer-info">
                                            <strong><?php echo htmlspecialchars($order['customer_name']); ?></strong>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contact-info">
                                            <i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($order['customer_email']); ?><br>
                                            <i class="fas fa-phone me-1"></i> <?php echo htmlspecialchars($order['customer_phone']); ?><br>
                                            <i class="fas fa-map-marker-alt me-1"></i> <?php echo htmlspecialchars($order['customer_address']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="text-success">
                                            <?php echo number_format($order['total_price'], 0, ',', '.'); ?> đ
                                        </strong>
                                    </td>
                                    <td>
                                        <span class="text-muted">
                                            <?php echo date('d/m/Y H:i', strtotime($order['created_at'])); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <select class="status-select form-select" 
                                                data-order-id="<?php echo $order['id']; ?>"
                                                style="background-color: <?php echo getStatusBgColor($order['status']); ?>; color: <?php echo getStatusColor($order['status']); ?>;">
                                            <option value="0" <?php echo $order['status'] == 0 ? 'selected' : ''; ?>>Chờ xử lý</option>
    <option value="1" <?php echo $order['status'] == 1 ? 'selected' : ''; ?>>Đang xử lý</option>
    <option value="2" <?php echo $order['status'] == 2 ? 'selected' : ''; ?>>Đang giao</option>      
    <option value="3" <?php echo $order['status'] == 3 ? 'selected' : ''; ?>>Hoàn thành</option>     
    <option value="4" <?php echo $order['status'] == 4 ? 'selected' : ''; ?>>Đã hủy</option>        
                                        </select>
                                    </td>
                                    <td>
                                        <div class="order-actions">
                                            <a href="view.php?id=<?php echo $order['id']; ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i> Chi tiết
                                            </a>
                                            <button class="btn btn-danger btn-sm" onclick="deleteOrder(<?php echo $order['id']; ?>)">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Hàm cập nhật trạng thái đơn hàng
        function updateOrderStatus(orderId, status) {
            const statusNum = parseInt(status);
            
            fetch('update-status.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `order_id=${orderId}&status=${statusNum}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Cập nhật màu sắc select
                    const selectElement = document.querySelector(`[data-order-id="${orderId}"]`);
                    
                    // Cập nhật màu nền và màu chữ dựa trên status mới
                    const bgColors = {
                        0: '#fff3cd', 1: '#cff4fc', 2: '#d1e7dd', 
                        3: '#f8d7da', 4: '#cfe2ff'
                    };
                    const textColors = {
                        0: '#856404', 1: '#055160', 2: '#0f5132', 
                        3: '#721c24', 4: '#084298'
                    };
                    
                    selectElement.style.backgroundColor = bgColors[statusNum] || '#ffffff';
                    selectElement.style.color = textColors[statusNum] || '#000000';
                    
                    // Cập nhật số liệu thống kê
                    if (data.status_counts) {
                        updateStatusCounts(data.status_counts);
                    }
                    
                    // Hiển thị thông báo thành công
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công!',
                        text: 'Đã cập nhật trạng thái đơn hàng',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    // Hiển thị thông báo lỗi
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi!',
                        text: data.message || 'Có lỗi xảy ra khi cập nhật trạng thái'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi!',
                    text: 'Không thể kết nối đến máy chủ'
                });
            });
        }

        // Hàm cập nhật số liệu thống kê
        function updateStatusCounts(counts) {
            const statusElements = {
                0: document.querySelector('.stats-card.pending h2'),
                1: document.querySelector('.stats-card.processing h2'),
                2: document.querySelector('.stats-card.completed h2'),
                3: document.querySelector('.stats-card.cancelled h2'),
                4: document.querySelector('.stats-card.shipping h2')
            };
            
            for (let status in statusElements) {
                if (statusElements[status] && counts[status] !== undefined) {
                    statusElements[status].textContent = new Intl.NumberFormat('vi-VN').format(counts[status]);
                }
            }
        }

        // Xử lý sự kiện thay đổi trạng thái
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.status-select').forEach(select => {
                select.addEventListener('change', function() {
                    const orderId = this.getAttribute('data-order-id');
                    const newStatus = this.value;
                    const currentStatus = this.getAttribute('data-original-status') || this.value;
                    
                    // Hiển thị dialog xác nhận
                    Swal.fire({
                        title: 'Xác nhận thay đổi',
                        text: `Bạn có chắc muốn thay đổi trạng thái đơn hàng?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Đồng ý',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            updateOrderStatus(orderId, newStatus);
                        } else {
                            // Khôi phục giá trị cũ nếu người dùng hủy
                            this.value = currentStatus;
                        }
                    });
                });
                
                // Lưu trạng thái ban đầu
                select.setAttribute('data-original-status', select.value);
            });
        });

        // Tìm kiếm đơn hàng
        document.getElementById('orderSearch').addEventListener('keyup', function() {
            let searchText = this.value.toLowerCase();
            let tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                let text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });
        });

        // Xóa đơn hàng
        function deleteOrder(orderId) {
            Swal.fire({
                title: 'Xác nhận xóa',
                text: "Bạn có chắc muốn xóa đơn hàng này?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Thêm logic xóa đơn hàng ở đây
                    fetch('delete-order.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `order_id=${orderId}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            document.getElementById(`order-${orderId}`).remove();
                            Swal.fire(
                                'Đã xóa!',
                                'Đơn hàng đã được xóa.',
                                'success'
                            );
                        } else {
                            Swal.fire(
                                'Lỗi!',
                                data.message || 'Không thể xóa đơn hàng',
                                'error'
                            );
                        }
                    });
                }
            });
        }
    </script>
</body>
</html>