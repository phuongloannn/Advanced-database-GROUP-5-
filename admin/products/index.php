<?php

session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_as']) || ($_SESSION['role_as'] != 1 && $_SESSION['role_as'] != 2)) {
    header('Location: ../../auth/login.php');
    exit();
}

require_once '../../includes/db.php';

// Xử lý tìm kiếm
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
if ($search !== '') {
    $search_escaped = $conn->real_escape_string($search);
    $where = "WHERE p.name LIKE '%$search_escaped%' OR p.id = '$search_escaped'";
}

// Lấy danh sách sản phẩm (JOIN với categories để lấy tên danh mục)
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
    <title>Quản lý Sản phẩm</title>
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
        <a href="index.php" class="active">Quản lý Sản phẩm</a>
        <a href="../orders/index.php">Quản lý Đơn hàng</a>
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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>Danh sách sản phẩm</h2>
            <a href="add.php" class="btn btn-success">+ Thêm sản phẩm</a>
        </div>
        <form class="row mb-4" method="get">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Tìm theo tên hoặc ID..." value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                <a href="index.php" class="btn btn-secondary">Reset</a>
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
                                    <img src="../../anh_xedap/<?= htmlspecialchars($product['image']) ?>" alt="" style="width:40px;">
                                <?php endif; ?>
                                <?php if (!empty($product['image1'])): ?>
                                    <img src="../../anh_xedap/<?= htmlspecialchars($product['image1']) ?>" alt="" style="width:40px;">
                                <?php endif; ?>
                                <?php if (!empty($product['image2'])): ?>
                                    <img src="../../anh_xedap/<?= htmlspecialchars($product['image2']) ?>" alt="" style="width:40px;">
                                <?php endif; ?>
                                <?php if (!empty($product['image3'])): ?>
                                    <img src="../../anh_xedap/<?= htmlspecialchars($product['image3']) ?>" alt="" style="width:40px;">
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
                        <td colspan="10" class="text-center">Không có sản phẩm nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="../admin.php" class="btn btn-secondary">Quay lại trang Admin</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>