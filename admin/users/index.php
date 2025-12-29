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
    $where = "WHERE name LIKE '%$search_escaped%' OR email LIKE '%$search_escaped%' OR id = '$search_escaped'";
}

// Lấy danh sách người dùng
$sql = "SELECT * FROM users $where ORDER BY creat_at DESC";
$result = $conn->query($sql);
$users = [];
if ($result) {
    $users = $result->fetch_all(MYSQLI_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Quản lý Người dùng</title>
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
        <a href="../orders/index.php">Quản lý Đơn hàng</a>
        <a href="index.php" class="active">Quản lý Người dùng</a>
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
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2>Danh sách người dùng</h2>
                <?php if ($_SESSION['role_as'] == 1): ?>
                    <a href="add.php" class="btn btn-success">+ Thêm người dùng</a>
                <?php endif; ?>
            </div>
            <form class="row mb-4" method="get">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Tìm theo tên, email hoặc ID..." value="<?= htmlspecialchars($search) ?>">
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
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Quyền</th>
                        <th>Ngày tạo</th>
                        <?php if ($_SESSION['role_as'] == 1): ?>
                            <th>Hành động</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['id']) ?></td>
                                <td><?= htmlspecialchars($user['name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <?php
                                    if ($user['role_as'] == 1) {
                                        echo '<span class="badge bg-primary">Admin</span>';
                                    } elseif ($user['role_as'] == 2) {
                                        echo '<span class="badge bg-info">Nhân viên</span>';
                                    } else {
                                        echo '<span class="badge bg-secondary">Khách</span>';
                                    }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($user['creat_at']) ?></td>
                                <?php if ($_SESSION['role_as'] == 1): ?>
                                    <td>
                                        <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                                        <a href="delete.php?id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn chắc chắn muốn xóa?')">Xóa</a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $_SESSION['role_as'] == 1 ? '6' : '5' ?>" class="text-center">Không có người dùng nào.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <a href="../admin.php" class="btn btn-secondary">Quay lại trang Admin</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>