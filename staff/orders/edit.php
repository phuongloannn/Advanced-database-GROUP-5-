<?php
// filepath: c:\xampp\htdocs\final\staff\orders\edit.php
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
$sql = "SELECT o.*, COALESCE(u.name, o.guest_name) AS customer_name
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

// Lấy chi tiết đơn hàng (lấy cả ảnh sản phẩm)
$sql = "SELECT od.*, p.name AS product_name, p.image 
        FROM order_detail od
        LEFT JOIN products p ON od.product_id = p.id
        WHERE od.order_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Lấy danh sách sản phẩm để thêm mới
$all_products = [];
$result = $conn->query("SELECT id, name, selling_price, qty FROM products WHERE status = 0 ORDER BY name ASC");
if ($result) {
    $all_products = $result->fetch_all(MYSQLI_ASSOC);
}

// Xử lý thêm sản phẩm vào đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $new_product_id = intval($_POST['new_product_id']);
    $new_quantity = intval($_POST['new_quantity']);
    if ($new_product_id > 0 && $new_quantity > 0) {
        // Lấy giá bán hiện tại
        $stmt_price = $conn->prepare("SELECT selling_price FROM products WHERE id = ?");
        $stmt_price->bind_param("i", $new_product_id);
        $stmt_price->execute();
        $stmt_price->bind_result($selling_price);
        $stmt_price->fetch();
        $stmt_price->close();

        // Thêm vào order_detail
        $stmt_add = $conn->prepare("INSERT INTO order_detail (order_id, product_id, selling_price, quantity) VALUES (?, ?, ?, ?)");
        $stmt_add->bind_param("iidi", $order_id, $new_product_id, $selling_price, $new_quantity);
        $stmt_add->execute();
        $stmt_add->close();

        // Trừ kho
        $conn->query("UPDATE products SET qty = qty - $new_quantity WHERE id = $new_product_id");

        // Reload lại trang để cập nhật chi tiết đơn hàng
        header("Location: edit.php?id=$order_id");
        exit();
    }
}
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
            <strong>Ngày tạo:</strong> <?= htmlspecialchars($order['created_at']) ?><br>
            <strong>Trạng thái:</strong>
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
        </div>

        <h4>Chi tiết sản phẩm</h4>
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