<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_as']) || ($_SESSION['role_as'] != 1 && $_SESSION['role_as'] != 2)) {
    header('Location: ../../auth/login.php');
    exit();
}

require_once '../../includes/db.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($order_id <= 0) {
    echo "ID không hợp lệ.";
    exit();
}

// Lấy thông tin đơn hàng và khách hàng
$stmt = $conn->prepare("SELECT o.id, u.name AS customer_name, o.status, o.created_at
                        FROM orders o
                        LEFT JOIN users u ON o.user_id = u.id
                        WHERE o.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "Không tìm thấy đơn hàng.";
    exit();
}

// Lấy danh sách sản phẩm trong đơn hàng
$stmt = $conn->prepare("SELECT od.quantity, od.selling_price, p.name AS product_name, p.image
                        FROM order_detail od
                        LEFT JOIN products p ON od.product_id = p.id
                        WHERE od.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_items = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Tính tổng tiền
$total_price = 0;
foreach ($order_items as $item) {
    $total_price += $item['selling_price'] * $item['quantity'];
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Chi tiết đơn hàng</title>
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
        <h4>Admin Panel</h4>
        <a href="../admin.php">Thống kê</a>
        <a href="../products/index.php">Quản lý Sản phẩm</a>
        <a href="index.php" class="active">Quản lý Đơn hàng</a>
        <a href="../users/index.php">Quản lý Người dùng</a>
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
        <h2>Chi tiết đơn hàng #<?= htmlspecialchars($order['id']) ?></h2>
        <div class="mb-4">
            <p><strong>Khách hàng:</strong> <?= htmlspecialchars($order['customer_name']) ?></p>
            <p><strong>Ngày tạo:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
            <p><strong>Trạng thái:</strong>
                <?php
                switch ($order['status']) {
                    case 0:
                        echo '<span class="badge bg-warning">Đang chuẩn bị hàng</span>';
                        break;
                    case 1:
                        echo '<span class="badge bg-info text-dark">Đang giao hàng</span>';
                        break;
                    case 2:
                        echo '<span class="badge bg-success">Giao hàng thành công</span>';
                        break;
                    case 3:
                        echo '<span class="badge bg-danger">Đã hủy</span>';
                        break;
                    case 4:
                        echo '<span class="badge bg-secondary">Trả hàng/Đổi hàng</span>';
                        break;
                    default:
                        echo '<span class="badge bg-dark">Không xác định</span>';
                }
                ?>
            </p>
        </div>

        <h4>Sản phẩm trong đơn</h4>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Ảnh</th>
                    <th>Sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($order_items) > 0): ?>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td>
                                <?php if (!empty($item['image'])): ?>
                                    <img src="../../anh_xedap/<?= htmlspecialchars($item['image']) ?>" alt="Ảnh" style="max-width:80px;max-height:60px;">
                                <?php else: ?>
                                    <span class="text-muted">Không có ảnh</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($item['product_name'] ?? 'Không rõ') ?></td>
                            <td><?= number_format($item['selling_price'], 0, ',', '.') ?> VND</td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= number_format($item['selling_price'] * $item['quantity'], 0, ',', '.') ?> VND</td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Không có sản phẩm nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Tổng cộng:</th>
                    <th><?= number_format($total_price, 0, ',', '.') ?> VND</th>
                </tr>
            </tfoot>
        </table>

        <a href="index.php" class="btn btn-secondary">Quay lại</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>