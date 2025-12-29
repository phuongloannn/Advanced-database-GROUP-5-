<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_as']) || $_SESSION['role_as'] != 1) {
    header('Location: ../../auth/login.php');
    exit();
}

require_once '../../includes/db.php';

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($order_id <= 0) {
    header('Location: index.php');
    exit();
}

// Xử lý cập nhật trạng thái đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $status = isset($_POST['status']) ? (int)$_POST['status'] : 0;
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("ii", $status, $order_id);
    $stmt->execute();
    $stmt->close();
    header("Location: edit.php?id=$order_id");
    exit();
}

// Xử lý cập nhật sản phẩm trong đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_items'])) {
    foreach ($_POST['items'] as $item_id => $item) {
        $quantity = (int)$item['quantity'];
        $price = (int)$item['price'];
        $stmt = $conn->prepare("UPDATE order_detail SET quantity = ?, selling_price = ? WHERE id = ? AND order_id = ?");
        $stmt->bind_param("iiii", $quantity, $price, $item_id, $order_id);
        $stmt->execute();
        $stmt->close();
    }
    header("Location: edit.php?id=$order_id");
    exit();
}

// Xử lý xóa sản phẩm khỏi đơn hàng
if (isset($_GET['delete_item_id'])) {
    $item_id = (int)$_GET['delete_item_id'];
    $stmt = $conn->prepare("DELETE FROM order_detail WHERE id = ? AND order_id = ?");
    $stmt->bind_param("ii", $item_id, $order_id);
    $stmt->execute();
    $stmt->close();
    header("Location: edit.php?id=$order_id");
    exit();
}

// Lấy dữ liệu đơn hàng
$stmt = $conn->prepare("SELECT o.id, COALESCE(u.name, o.guest_name) AS customer_name, o.status, o.created_at
                        FROM orders o
                        LEFT JOIN users u ON o.user_id = u.id
                        WHERE o.id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();
$stmt->close();

if (!$order) {
    echo "Đơn hàng không tồn tại.";
    exit();
}

// Lấy danh sách sản phẩm trong đơn hàng
$stmt = $conn->prepare("SELECT od.id, od.product_id, p.name AS product_name, od.quantity, od.selling_price
                        FROM order_detail od
                        LEFT JOIN products p ON od.product_id = p.id
                        WHERE od.order_id = ?");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order_items = $result->fetch_all(MYSQLI_ASSOC);
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

        header("Location: edit.php?id=$order_id");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa đơn hàng</title>
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
        <div class="container mt-5">
            <h2>Sửa đơn hàng #<?= htmlspecialchars($order['id']) ?></h2>
            <form method="post" class="mt-4 mb-4">
                <div class="mb-3">
                    <label class="form-label">Khách hàng</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($order['customer_name']) ?>"
                        disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Ngày tạo</label>
                    <input type="text" class="form-control" value="<?= htmlspecialchars($order['created_at']) ?>"
                        disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Trạng thái đơn hàng</label>
                    <select name="status" class="form-select" required>
                        <option value="0" <?= $order['status'] == 0 ? 'selected' : '' ?>>Đang chuẩn bị hàng</option>
                        <option value="1" <?= $order['status'] == 1 ? 'selected' : '' ?>>Đang giao hàng</option>
                        <option value="2" <?= $order['status'] == 2 ? 'selected' : '' ?>>Giao hàng thành công</option>
                        <option value="3" <?= $order['status'] == 3 ? 'selected' : '' ?>>Đã hủy</option>
                        <option value="4" <?= $order['status'] == 4 ? 'selected' : '' ?>>Trả hàng/Đổi hàng</option>
                    </select>
                </div>
                <button type="submit" name="update_status" class="btn btn-primary">Cập nhật trạng thái</button>
                <a href="index.php" class="btn btn-secondary">Quay lại</a>
            </form>

            <h4>Chi tiết sản phẩm trong đơn hàng</h4>
            <form method="post">
                <table class="table table-bordered mt-4">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Tên sản phẩm</th>
                            <th>Đơn giá (VND)</th>
                            <th>Số lượng</th>
                            <th>Tổng</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        if (count($order_items) > 0):
                            foreach ($order_items as $item):
                                $line_total = $item['selling_price'] * $item['quantity'];
                                $total += $line_total;
                        ?>
                                <tr>
                                    <td><?= $item['id'] ?></td>
                                    <td><?= htmlspecialchars($item['product_name'] ?? 'N/A') ?></td>
                                    <td>
                                        <input type="number" name="items[<?= $item['id'] ?>][price]" class="form-control"
                                            value="<?= $item['selling_price'] ?>" required>
                                    </td>
                                    <td>
                                        <input type="number" name="items[<?= $item['id'] ?>][quantity]" class="form-control"
                                            value="<?= $item['quantity'] ?>" required>
                                    </td>
                                    <td><?= number_format($line_total, 0, ',', '.') ?> VND</td>
                                    <td>
                                        <a href="?id=<?= $order_id ?>&delete_item_id=<?= $item['id'] ?>"
                                            class="btn btn-danger btn-sm"
                                            onclick="return confirm('Xóa sản phẩm này khỏi đơn hàng?')">Xóa</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <th colspan="4" class="text-end">Tổng số tiền</th>
                                <th colspan="2"><?= number_format($total, 0, ',', '.') ?> VND</th>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Đơn hàng chưa có sản phẩm nào.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <button type="submit" name="update_items" class="btn btn-primary">Cập nhật sản phẩm</button>
            </form>

            <h5 class="mt-4">Thêm sản phẩm vào đơn hàng</h5>
            <form method="post" class="row g-2 align-items-end mb-4">
                <div class="col-md-6">
                    <select name="new_product_id" class="form-select" required>
                        <option value="">-- Chọn sản phẩm --</option>
                        <?php foreach ($all_products as $pro): ?>
                            <option value="<?= $pro['id'] ?>">
                                <?= htmlspecialchars($pro['name']) ?> (Còn: <?= $pro['qty'] ?>, Giá:
                                <?= number_format($pro['selling_price'], 0, ',', '.') ?>VND)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="number" name="new_quantity" class="form-control" min="1" value="1" required>
                </div>
                <div class="col-md-3">
                    <button type="submit" name="add_product" class="btn btn-success">Thêm sản phẩm</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>