<?php
session_start();

// Kiểm tra session auth_user và role
if (!isset($_SESSION['auth_user']) || !isset($_SESSION['auth_user']['role_as']) || $_SESSION['auth_user']['role_as'] != 1) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db.php';

// Lấy thông tin admin từ session
$admin_name = isset($_SESSION['auth_user']['name']) ? $_SESSION['auth_user']['name'] : 'Admin';

// Lấy tổng số sản phẩm
$products_query = "SELECT COUNT(*) as total FROM products";
$products_result = $conn->query($products_query);
$total_products = $products_result->fetch_assoc()['total'];

// Lấy tổng số đơn hàng
$orders_query = "SELECT COUNT(*) as total FROM orders";
$orders_result = $conn->query($orders_query);
$total_orders = $orders_result->fetch_assoc()['total'];

// Lấy tổng số khách hàng
$users_query = "SELECT COUNT(*) as total FROM users WHERE role_as = 0";
$users_result = $conn->query($users_query);
$total_users = $users_result->fetch_assoc()['total'];

// Lấy tổng doanh thu từ đơn hàng đã hoàn thành
$revenue_query = "SELECT SUM(total_price) as total FROM orders WHERE status = 'Completed'";
$revenue_result = $conn->query($revenue_query);
$total_revenue = $revenue_result->fetch_assoc()['total'] ?? 0;

// Lấy thống kê đơn hàng theo trạng thái
$status_query = "SELECT status, COUNT(*) as count FROM orders GROUP BY status";
$status_result = $conn->query($status_query);
$status_stats = [];
while ($row = $status_result->fetch_assoc()) {
    $status_stats[$row['status']] = $row['count'];
}

// Lấy doanh thu theo tháng (6 tháng gần nhất)
$monthly_revenue_query = "SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    COUNT(*) as order_count,
    SUM(total_price) as revenue
    FROM orders 
    WHERE status = 'Completed'
    AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC
    LIMIT 6";
$monthly_revenue_result = $conn->query($monthly_revenue_query);
$monthly_revenue = [];
while ($row = $monthly_revenue_result->fetch_assoc()) {
    $monthly_revenue[] = $row;
}

// Lấy top 5 sản phẩm bán chạy
$top_products_query = "SELECT 
    p.name,
    COUNT(DISTINCT o.id) as order_count,
    SUM(od.quantity) as total_quantity,
    SUM(od.quantity * p.selling_price) as revenue
    FROM products p
    LEFT JOIN order_detail od ON p.id = od.product_id
    LEFT JOIN orders o ON od.order_id = o.id AND o.status = 'Completed'
    GROUP BY p.id, p.name
    HAVING total_quantity > 0
    ORDER BY total_quantity DESC
    LIMIT 5";
$top_products_result = $conn->query($top_products_query);

if (!$top_products_result) {
    die("Query failed: " . $conn->error);
}

$top_products = [];
while ($row = $top_products_result->fetch_assoc()) {
    $top_products[] = $row;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Q-Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --info-color: #36b9cc;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', sans-serif;
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

        .header {
            background: white;
            box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15);
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            transition: transform 0.2s;
            margin-bottom: 1.5rem;
        }

        .card:hover {
            transform: translateY(-3px);
        }

        .stats-card {
            padding: 1.5rem;
            color: white;
        }

        .stats-card.primary {
            background: linear-gradient(45deg, #4e73df 0%, #224abe 100%);
        }

        .stats-card.success {
            background: linear-gradient(45deg, #1cc88a 0%, #13855c 100%);
        }

        .stats-card.warning {
            background: linear-gradient(45deg, #f6c23e 0%, #dda20a 100%);
        }

        .stats-card.danger {
            background: linear-gradient(45deg, #e74a3b 0%, #be2617 100%);
        }

        .stats-card .icon {
            font-size: 2rem;
            opacity: 0.4;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 1rem 1.5rem;
            transition: all 0.3s;
        }

        .nav-link:hover, .nav-link.active {
            color: white;
            background: rgba(255, 255, 255, 0.1);
        }

        .nav-link i {
            width: 1.5rem;
        }

        .chart-card {
            padding: 1.5rem;
            background: white;
        }

        .top-products-list {
            list-style: none;
            padding: 0;
        }

        .top-products-list li {
            padding: 1rem;
            border-bottom: 1px solid #e3e6f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .top-products-list li:last-child {
            border-bottom: none;
        }

        .progress {
            height: 0.5rem;
            margin-top: 0.5rem;
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
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link active">
                        <i class="fas fa-chart-line me-2"></i> Thống kê
                    </a>
                </li>
                <li class="nav-item">
                    <a href="products/index.php" class="nav-link">
                        <i class="fas fa-tshirt me-2"></i> Quản lý Sản phẩm
                    </a>
                </li>
                <li class="nav-item">
                    <a href="orders/index.php" class="nav-link">
                        <i class="fas fa-shopping-cart me-2"></i> Quản lý Đơn hàng
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users/index.php" class="nav-link">
                        <i class="fas fa-users me-2"></i> Quản lý Tài khoản
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Header -->
        <div class="header d-flex justify-content-between align-items-center">
            <h2 class="mb-0">Dashboard</h2>
            <div class="d-flex align-items-center">
                <span class="me-3">Xin chào, <?= htmlspecialchars($admin_name) ?></span>
                <a href="../auth/logout.php" class="btn btn-danger">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card stats-card primary">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">TỔNG DOANH THU</h6>
                            <h3 class="mb-0"><?= number_format($total_revenue, 0, ',', '.') ?> đ</h3>
                        </div>
                        <div class="icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stats-card success">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">TỔNG ĐƠN HÀNG</h6>
                            <h3 class="mb-0"><?= $total_orders ?></h3>
                        </div>
                        <div class="icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stats-card warning">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">SẢN PHẨM</h6>
                            <h3 class="mb-0"><?= $total_products ?></h3>
                        </div>
                        <div class="icon">
                            <i class="fas fa-tshirt"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6">
                <div class="card stats-card danger">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50">KHÁCH HÀNG</h6>
                            <h3 class="mb-0"><?= $total_users ?></h3>
                        </div>
                        <div class="icon">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row">
            <div class="col-xl-8">
                <div class="card chart-card">
                    <h5 class="card-header bg-transparent">Doanh thu theo tháng</h5>
                    <div class="card-body">
                        <canvas id="revenueChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-xl-4">
                <div class="card chart-card">
                    <h5 class="card-header bg-transparent">Trạng thái đơn hàng</h5>
                    <div class="card-body">
                        <canvas id="orderStatusChart" height="300"></canvas>
                        <div class="mt-4">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-primary me-2" style="width: 30px;">&nbsp;</span>
                                <span>Chờ xử lý - Đơn hàng mới</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-success me-2" style="width: 30px;">&nbsp;</span>
                                <span>Đang xử lý - Đang chuẩn bị hàng</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-info me-2" style="width: 30px;">&nbsp;</span>
                                <span>Hoàn thành - Đã giao hàng</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-danger me-2" style="width: 30px;">&nbsp;</span>
                                <span>Đã hủy - Không thành công</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="row">
            <div class="col-12">
                <div class="card chart-card">
                    <h5 class="card-header bg-transparent">Top 5 sản phẩm bán chạy nhất</h5>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Số lượng đã bán</th>
                                        <th>Số đơn hàng</th>
                                        <th>Doanh thu</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($top_products as $product): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($product['name']) ?></td>
                                        <td><?= number_format($product['total_quantity']) ?></td>
                                        <td><?= number_format($product['order_count']) ?></td>
                                        <td><?= number_format($product['revenue'], 0, ',', '.') ?> đ</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Biểu đồ doanh thu
        const monthlyRevenue = <?= json_encode(array_reverse($monthly_revenue)) ?>;
        new Chart(document.getElementById('revenueChart'), {
            type: 'line',
            data: {
                labels: monthlyRevenue.map(item => {
                    const [year, month] = item.month.split('-');
                    return `Tháng ${month}/${year}`;
                }),
                datasets: [{
                    label: 'Doanh thu (VNĐ)',
                    data: monthlyRevenue.map(item => item.revenue),
                    borderColor: '#4e73df',
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    tension: 0.3,
                    fill: true
                }, {
                    label: 'Số đơn hàng',
                    data: monthlyRevenue.map(item => item.order_count),
                    borderColor: '#1cc88a',
                    backgroundColor: 'rgba(28, 200, 138, 0.1)',
                    tension: 0.3,
                    fill: true,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Doanh thu (VNĐ)'
                        }
                    },
                    y1: {
                        beginAtZero: true,
                        position: 'right',
                        title: {
                            display: true,
                            text: 'Số đơn hàng'
                        },
                        grid: {
                            drawOnChartArea: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top'
                    }
                }
            }
        });

        // Biểu đồ trạng thái đơn hàng
        const statusStats = <?= json_encode($status_stats) ?>;
        new Chart(document.getElementById('orderStatusChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(statusStats),
                datasets: [{
                    data: Object.values(statusStats),
                    backgroundColor: [
                        '#4e73df',  // Primary
                        '#1cc88a',  // Success
                        '#36b9cc',  // Info
                        '#f6c23e',  // Warning
                        '#e74a3b'   // Danger
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '70%'
            }
        });
    </script>
</body>
</html> 