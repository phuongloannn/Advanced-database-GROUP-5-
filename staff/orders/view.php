<?php
// filepath: c:\xampp\htdocs\final\staff\orders\view.php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_as']) || $_SESSION['role_as'] != 2) {
    header('Location: ../../auth/login.php');
    exit();
}

require_once '../../includes/db.php';

// Lấy ID đơn hàng
$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($order_id <= 0) {
    header('Location: index.php');
    exit();
}

// Lấy thông tin đơn hàng
$sql = "SELECT o.*, COALESCE(u.name, o.guest_name) AS customer_name, u.email
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        WHERE o.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header('Location: index.php?msg=not_found');
    exit();
}

// Lấy chi tiết đơn hàng (có cả ảnh sản phẩm)
$sql = "SELECT od.*, p.name AS product_name, p.image 
        FROM order_detail od
        LEFT JOIN products p ON od.product_id = p.id
        WHERE od.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng #<?= $order_id ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
        <h4>Staff Panel</h4>
        <a href="../dashboard.php">Thống kê</a>
        <a href="../products/index.php">Quản lý Sản phẩm</a>
        <a href="index.php" class="active">Quản lý Đơn hàng</a>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container-fluid">
            <span class="navbar-brand">Xin chào, <?= htmlspecialchars($_SESSION['username'] ?? '') ?></span>
            <div class="ms-auto">
                <a href="../../auth/logout.php" class="btn btn-danger">Đăng xuất</a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content">
        <h2>Chi tiết đơn hàng #<?= $order_id ?></h2>
        <div class="mb-4">
            <strong>Khách hàng:</strong> <?= htmlspecialchars($order['customer_name']) ?><br>
            <?php if ($order['email']): ?>
                <strong>Email:</strong> <?= htmlspecialchars($order['email']) ?><br>
            <?php endif; ?>
            <strong>Trạng thái:</strong>
            <?php
            switch ($order['status']) {
                case 0:
                    echo '<span class="badge bg-warning">Chờ xử lý</span>';
                    break;
                case 1:
                    echo '<span class="badge bg-success">Đã duyệt</span>';
                    break;
                case 2:
                    echo '<span class="badge bg-danger">Đã hủy</span>';
                    break;
                default:
                    echo '<span class="badge bg-secondary">Không xác định</span>';
            }
            ?><br>
            <strong>Ngày tạo:</strong> <?= htmlspecialchars($order['created_at']) ?>
        </div>
        <h4>Sản phẩm</h4>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá bán</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $total = 0;
                foreach ($order_details as $item):
                    $line_total = $item['selling_price'] * $item['quantity'];
                    $total += $line_total;
                ?>
                    <tr>
                        <td>
                            <?php if (!empty($item['image'])): ?>
                                <img src="../../anh_xedap/<?= htmlspecialchars($item['image']) ?>" alt="Ảnh" style="max-width:80px;max-height:60px;">
                            <?php else: ?>
                                <span class="text-muted">Không có ảnh</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= number_format($item['selling_price'], 0, ',', '.') ?> VND</td>
                        <td><?= $item['quantity'] ?></td>
                        <td><?= number_format($line_total, 0, ',', '.') ?> VND</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Tổng cộng</th>
                    <th><?= number_format($total, 0, ',', '.') ?> VND</th>
                </tr>
            </tfoot>
        </table>
        <a href="index.php" class="btn btn-secondary">Quay lại</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>