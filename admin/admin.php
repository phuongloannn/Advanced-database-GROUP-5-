<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_as']) || $_SESSION['role_as'] != 1) {
    header('Location: ../auth/login.php');
    exit();
}

require_once '../includes/db.php';

// Lấy dữ liệu doanh thu 6 tháng gần nhất từ đơn hàng đã hoàn thành
$revenue_query = "SELECT 
    DATE_FORMAT(created_at, '%Y-%m') as month,
    SUM(total_price) / 1000000 as revenue
    FROM orders 
    WHERE status = 'Đã hoàn thành'
    AND created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month ASC";
$revenue_result = $conn->query($revenue_query);
$revenue_data = [];
if ($revenue_result) {
    while ($row = $revenue_result->fetch_assoc()) {
        $revenue_data[] = $row;
    }
}

// Lấy dữ liệu đơn hàng theo trạng thái và tính tổng giá trị
$status_query = "SELECT 
    status,
    COUNT(*) as count,
    SUM(total_price) / 1000000 as total_value
    FROM orders 
    GROUP BY status
    ORDER BY FIELD(status, 'Đã hoàn thành', 'Đang xử lý', 'Đang giao hàng', 'Đã hủy')";
$status_result = $conn->query($status_query);
$status_data = [];
if ($status_result) {
    while ($row = $status_result->fetch_assoc()) {
        $status_data[] = $row;
    }
}

// Lấy top 5 sản phẩm có số lượng cao nhất
$top_products_query = "SELECT 
    name,
    qty as total_stock
    FROM products 
    ORDER BY qty DESC
    LIMIT 5";
$top_products_result = $conn->query($top_products_query);
$top_products_data = [];
if ($top_products_result) {
    while ($row = $top_products_result->fetch_assoc()) {
        $top_products_data[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Q-Fashion</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            min-height: 100vh;
            background: #f8f9fa;
        }

        .sidebar {
            min-width: 220px;
            max-width: 220px;
            background: #343a40;
            color: #fff;
            min-height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 100;
        }

        .sidebar h4 {
            padding: 24px 20px 12px 20px;
            border-bottom: 1px solid #495057;
            margin-bottom: 0;
        }

        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 14px 24px;
            transition: background 0.2s;
        }

        .sidebar a.active,
        .sidebar a:hover {
            background: #495057;
            color: #fff;
        }

        .main-content {
            margin-left: 220px;
            padding: 40px 30px 0 30px;
        }

        .navbar {
            margin-left: 220px;
        }

        .chart-container {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        @media (max-width: 991.98px) {
            .sidebar,
            .navbar {
                margin-left: 0;
                position: static;
            }

            .main-content {
                margin-left: 0;
                padding: 20px 10px 0 10px;
            }
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column h-100">
        <h4>Admin Panel</h4>
        <a href="dashboard.php" class="mt-2">Thống kê</a>
        <a href="products/index.php">Quản lý Sản phẩm</a>
        <a href="orders/index.php">Quản lý Đơn hàng</a>
        <a href="users/index.php">Quản lý Tài khoản</a>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand">Xin chào, <?= htmlspecialchars($_SESSION['username']) ?></span>
            <div class="ms-auto">
                <a href="../auth/logout.php" class="btn btn-danger">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <div class="container mt-5">
            <h2 class="mb-4">Tổng quan hệ thống</h2>
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card text-bg-primary mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Sản phẩm</h5>
                            <p class="card-text fs-3">
                                <?php
                                $result = $conn->query("SELECT COUNT(*) AS total FROM products");
                                $product_count = $result ? $result->fetch_assoc()['total'] : 0;
                                echo $product_count;
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-success mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Đơn hàng</h5>
                            <p class="card-text fs-3">
                                <?php
                                $result = $conn->query("SELECT COUNT(*) AS total FROM orders");
                                $order_count = $result ? $result->fetch_assoc()['total'] : 0;
                                echo $order_count;
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-info mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Khách hàng</h5>
                            <p class="card-text fs-3">
                                <?php
                                $result = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role_as = 0");
                                $user_count = $result ? $result->fetch_assoc()['total'] : 0;
                                echo $user_count;
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-bg-warning mb-3">
                        <div class="card-body">
                            <h5 class="card-title">Tổng số hàng trong kho</h5>
                            <p class="card-text fs-3">
                                <?php
                                $result = $conn->query("SELECT SUM(qty) AS total_qty FROM products");
                                $total_qty = $result ? ($result->fetch_assoc()['total_qty'] ?? 0) : 0;
                                echo $total_qty;
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Biểu đồ thống kê -->
            <div class="row">
                <!-- Biểu đồ doanh thu -->
                <div class="col-md-8 mb-4">
                    <div class="chart-container">
                        <h4>Doanh thu 6 tháng gần nhất</h4>
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
                <!-- Biểu đồ trạng thái đơn hàng -->
                <div class="col-md-4 mb-4">
                    <div class="chart-container">
                        <h4>Trạng thái đơn hàng</h4>
                        <canvas id="orderStatusChart"></canvas>
                    </div>
                </div>
                <!-- Biểu đồ top sản phẩm -->
                <div class="col-md-12">
                    <div class="chart-container">
                        <h4>Top 5 sản phẩm tồn kho nhiều nhất</h4>
                        <canvas id="topProductsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Dữ liệu cho biểu đồ trạng thái đơn hàng
        const statusData = <?= json_encode($status_data) ?>;
        const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: statusData.map(item => {
                    const count = item.count;
                    const value = parseFloat(item.total_value).toFixed(1);
                    return `${item.status} (${count} đơn)`;
                }),
                datasets: [{
                    data: statusData.map(item => item.count),
                    backgroundColor: [
                        '#22c55e', // Xanh lá - Đã hoàn thành
                        '#3d5af1', // Xanh dương - Đang xử lý
                        '#f59e0b', // Cam - Đang giao hàng
                        '#ef4444'  // Đỏ - Đã hủy
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20
                        }
                    },
                    title: {
                        display: true,
                        text: [
                            'Trạng thái đơn hàng',
                            '🟢 Đã hoàn thành = Đã giao và thanh toán',
                            '🔵 Đang xử lý = Chờ xác nhận',
                            '🟡 Đang giao = Đang vận chuyển',
                            '🔴 Đã hủy = Hủy bởi KH/hệ thống'
                        ],
                        font: {
                            size: 13,
                            weight: 'normal'
                        },
                        padding: {
                            bottom: 15
                        },
                        color: '#666'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const data = statusData[context.dataIndex];
                                const percentage = ((data.count / statusData.reduce((a, b) => a + parseInt(b.count), 0)) * 100).toFixed(1);
                                return [
                                    `${data.status}: ${data.count} đơn (${percentage}%)`,
                                    `Giá trị: ${parseFloat(data.total_value).toFixed(1)} triệu VNĐ`
                                ];
                            }
                        }
                    }
                }
            }
        });

        // Dữ liệu cho biểu đồ doanh thu
        const revenueData = <?= json_encode($revenue_data) ?>;
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: revenueData.map(item => {
                    const [year, month] = item.month.split('-');
                    return `Tháng ${month}/${year}`;
                }),
                datasets: [{
                    label: 'Doanh thu (Triệu VNĐ)',
                    data: revenueData.map(item => parseFloat(item.revenue).toFixed(1)),
                    borderColor: '#3d5af1',
                    tension: 0.3,
                    fill: true,
                    backgroundColor: 'rgba(61, 90, 241, 0.1)'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Doanh thu từ đơn hàng đã hoàn thành (6 tháng gần nhất)',
                        font: {
                            size: 14
                        },
                        padding: {
                            bottom: 20
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value.toLocaleString('vi-VN') + ' tr';
                            }
                        },
                        title: {
                            display: true,
                            text: 'Triệu VNĐ'
                        }
                    }
                }
            }
        });

        // Dữ liệu cho biểu đồ top sản phẩm
        const productData = <?= json_encode($top_products_data) ?>;
        const productCtx = document.getElementById('topProductsChart').getContext('2d');
        new Chart(productCtx, {
            type: 'bar',
            data: {
                labels: productData.map(item => item.name),
                datasets: [{
                    label: 'Số lượng trong kho',
                    data: productData.map(item => item.total_stock),
                    backgroundColor: '#3d5af1',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>