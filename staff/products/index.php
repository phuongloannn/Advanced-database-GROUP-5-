<?php
// filepath: c:\xampp\htdocs\final\staff\products\index.php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_as']) || $_SESSION['role_as'] != 2) {
    header('Location: ../../auth/login.php');
    exit();
}

require_once '../../includes/db.php';

// Xử lý tìm kiếm
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$where = '';
if ($search !== '') {
    $search_escaped = $conn->real_escape_string($search);
    $where = "WHERE name LIKE '%$search_escaped%' OR id = '$search_escaped'";
}

// Lấy danh sách sản phẩm
$sql = "SELECT * FROM products $where ORDER BY created_at DESC";
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
    <title>Quản lý Sản phẩm (Nhân viên)</title>
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
        <a href="index.php" class="active">Quản lý Sản phẩm</a>
        <a href="../orders/index.php">Quản lý Đơn hàng</a>
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
        <h2>Danh sách sản phẩm</h2>
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
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá bán</th>
                    <th>Số lượng tồn</th>
                    <th>Trạng thái</th>
                    <th>Ngày tạo</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $pro): ?>
                        <tr>
                            <td><?= $pro['id'] ?></td>
                            <td>
                                <?php if (!empty($pro['image'])): ?>
                                    <img src="../../anh_xedap/<?= htmlspecialchars($pro['image']) ?>" alt="Ảnh" style="max-width:80px;max-height:60px;">
                                <?php else: ?>
                                    <span class="text-muted">Không có ảnh</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($pro['name']) ?></td>
                            <td><?= number_format($pro['selling_price'], 0, ',', '.') ?> đ</td>
                            <td><?= $pro['qty'] ?></td>
                            <td>
                                <?php
                                if ($pro['status'] == 0) {
                                    echo '<span class="badge bg-success">Hiện</span>';
                                } else {
                                    echo '<span class="badge bg-secondary">Ẩn</span>';
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($pro['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Không có sản phẩm nào.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="../dashboard.php" class="btn btn-secondary">Quay lại Dashboard</a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>