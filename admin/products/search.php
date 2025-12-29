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
    $where = "WHERE p.name LIKE '%$search_escaped%' OR p.id = '$search_escaped'";
}

$sql = "SELECT p.*, c.name AS category_name
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        $where
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
$products = [];
if ($result) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Kết quả tìm kiếm sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Kết quả tìm kiếm sản phẩm</h2>
        <form class="row mb-4" method="get">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Tìm theo tên hoặc ID..." value="<?= htmlspecialchars($search) ?>">
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
                    <th>Tên sản phẩm</th>
                    <th>Danh mục</th>
                    <th>Giá gốc</th>
                    <th>Giá bán</th>
                    <th>Số lượng</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                    <th>Hình ảnh</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?= htmlspecialchars($product['id']) ?></td>
                            <td><?= htmlspecialchars($product['name']) ?></td>
                            <td><?= htmlspecialchars($product['category_name']) ?></td>
                            <td><?= number_format($product['original_price'], 0, ',', '.') ?> VND</td>
                            <td><?= number_format($product['selling_price'], 0, ',', '.') ?> VND</td>
                            <td><?= htmlspecialchars($product['qty']) ?></td>
                            <td>
                                <?= $product['status'] == 0 ? '<span class="badge bg-success">Hiện</span>' : '<span class="badge bg-secondary">Ẩn</span>' ?>
                            </td>
                            <td><?= htmlspecialchars($product['created_at']) ?></td>
                            <td>
                                <?php if (!empty($product['image'])): ?>
                                    <img src="../../uploads/<?= htmlspecialchars($product['image']) ?>" alt="" style="width:60px;">
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit.php?id=<?= $product['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="delete.php?id=<?= $product['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn chắc chắn muốn xóa?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">Không có sản phẩm nào phù hợp.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>