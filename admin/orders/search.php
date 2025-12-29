<?php

session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_as']) || $_SESSION['role_as'] != 1) {
    header('Location: ../../auth/login.php');
    exit();
}

require_once '../../includes/db.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$where = '';
if ($search !== '') {
    $search_escaped = $conn->real_escape_string($search);
    $where = "WHERE u.name LIKE '%$search_escaped%' OR o.id = '$search_escaped'";
}

$sql = "SELECT o.id, u.name AS customer_name, 
               COALESCE(SUM(od.selling_price * od.quantity), 0) AS total_price, 
               o.status, o.created_at
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_detail od ON o.id = od.order_id
        $where
        GROUP BY o.id, u.name, o.status, o.created_at
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
$orders = [];
if ($result) {
    $orders = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Kết quả tìm kiếm đơn hàng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Kết quả tìm kiếm đơn hàng</h2>
        <form class="row mb-4" method="get">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Tìm theo tên khách hoặc ID..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                <a href="index.php" class="btn btn-secondary">Quay lại</a>
            </div>
        </form>
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orders) > 0): ?>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td><?= htmlspecialchars($order['id']) ?></td>
                            <td><?= htmlspecialchars($order['customer_name']) ?></td>
                            <td><?= number_format($order['total_price'], 0, ',', '.') ?> đ</td>
                            <td>
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
                                ?>
                            </td>
                            <td><?= htmlspecialchars($order['created_at']) ?></td>
                            <td>
                                <a href="view.php?id=<?= $order['id'] ?>" class="btn btn-info btn-sm">Xem</a>
                                <a href="edit.php?id=<?= $order['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="delete.php?id=<?= $order['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn chắc chắn muốn xóa?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Không có đơn hàng nào phù hợp.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>