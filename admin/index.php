<div class="container-fluid px-4">
    <h1 class="mt-4">Dashboard</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Dashboard</li>
    </ol>
    
    <!-- Thống kê tổng quan -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0"><?= getTotalValue('orders') ?></h4>
                            <div>Tổng đơn hàng</div>
                        </div>
                        <div>
                            <i class="fas fa-shopping-bag fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0"><?= getTotalValue('products') ?></h4>
                            <div>Tổng sản phẩm</div>
                        </div>
                        <div>
                            <i class="fas fa-box fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0"><?= getTotalValue('categories') ?></h4>
                            <div>Tổng danh mục</div>
                        </div>
                        <div>
                            <i class="fas fa-list fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="mb-0"><?= getTotalValue('users') ?></h4>
                            <div>Tổng người dùng</div>
                        </div>
                        <div>
                            <i class="fas fa-users fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Biểu đồ doanh thu -->
    <div class="row">
        <div class="col-xl-8">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-line me-1"></i>
                    Doanh thu theo tháng
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fas fa-chart-pie me-1"></i>
                    Thống kê đơn hàng
                </div>
                <div class="card-body">
                    <canvas id="orderStatusChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Đơn hàng gần đây -->
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-table me-1"></i>
            Đơn hàng gần đây
        </div>
        <div class="card-body">
            <table id="recentOrdersTable" class="table table-striped">
                <thead>
                    <tr>
                        <th>Mã đơn</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $orders = getRecentOrders(5); // Lấy 5 đơn hàng gần nhất
                    if(mysqli_num_rows($orders) > 0) {
                        while($order = mysqli_fetch_assoc($orders)) {
                    ?>
                    <tr>
                        <td>#<?= $order['tracking_no'] ?></td>
                        <td><?= $order['name'] ?></td>
                        <td><?= formatCurrency($order['total_price']) ?></td>
                        <td>
                            <span class="badge bg-<?= getOrderStatusClass($order['status']) ?>">
                                <?= $order['status'] ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></td>
                        <td>
                            <a href="view-order.php?id=<?= $order['id'] ?>" class="btn btn-sm btn-primary">
                                Chi tiết
                            </a>
                        </td>
                    </tr>
                    <?php
                        }
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Thêm Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<!-- Script vẽ biểu đồ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dữ liệu doanh thu
    const revenueData = <?= json_encode(getRevenueData()) ?>;
    
    // Biểu đồ doanh thu
    const revenueChart = new Chart(document.getElementById('revenueChart'), {
        type: 'line',
        data: {
            labels: revenueData.map(item => item.month),
            datasets: [{
                label: 'Doanh thu',
                data: revenueData.map(item => item.revenue),
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Doanh thu theo tháng'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(value);
                        }
                    }
                }
            }
        }
    });
    
    // Dữ liệu trạng thái đơn hàng
    const orderStatusData = <?= json_encode(getOrderStatusStats()) ?>;
    
    // Biểu đồ trạng thái đơn hàng
    const orderStatusChart = new Chart(document.getElementById('orderStatusChart'), {
        type: 'doughnut',
        data: {
            labels: orderStatusData.map(item => item.status),
            datasets: [{
                data: orderStatusData.map(item => item.count),
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)',
                    'rgb(75, 192, 192)'
                ]
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script> 