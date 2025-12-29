<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_as']) || $_SESSION['role_as'] != 1) {
    header('Location: index.php?msg=permission_denied');
    exit();
}

require_once '../../includes/db.php';

// Lấy ID người dùng
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit();
}

// Lấy thông tin người dùng
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
if (!$user) {
    header('Location: index.php');
    exit();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $role_as = isset($_POST['role_as']) ? intval($_POST['role_as']) : 0;

    // Không cho phép cập nhật thành admin
    if ($role_as == 1) {
        $errors[] = "Không thể cập nhật quyền thành admin";
        $role_as = $user['role_as']; // Giữ nguyên quyền cũ
    }

    // Validate
    if ($name == '') $errors[] = "Tên không được để trống";
    if ($email == '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ";

    // Kiểm tra email đã tồn tại cho user khác chưa
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) $errors[] = "Email đã tồn tại cho người dùng khác";

    // Nếu có đổi mật khẩu
    $password = '';
    if (!empty($_POST['password'])) {
        if (strlen($_POST['password']) < 6) {
            $errors[] = "Mật khẩu phải từ 6 ký tự";
        } else {
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        }
    }

    if (empty($errors)) {
        if ($password) {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, password=?, role_as=? WHERE id=?");
            $stmt->bind_param("sssii", $name, $email, $password, $role_as, $id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET name=?, email=?, role_as=? WHERE id=?");
            $stmt->bind_param("ssii", $name, $email, $role_as, $id);
        }
        if ($stmt->execute()) {
            header("Location: index.php?msg=edit_success");
            exit();
        } else {
            $errors[] = "Cập nhật thất bại!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa người dùng</title>
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
            <h2>Sửa người dùng</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?= implode('<br>', $errors) ?>
                </div>
            <?php endif; ?>
            <form method="post" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tên</label>
                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars(isset($name) ? $name : $user['name']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars(isset($email) ? $email : $user['email']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mật khẩu mới (bỏ trống nếu không đổi)</label>
                    <input type="password" name="password" class="form-control" minlength="6">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Quyền</label>
                    <select name="role_as" class="form-select">
                        <option value="0" <?= ((isset($role_as) && $role_as == 0) || (!isset($role_as) && $user['role_as'] == 0)) ? 'selected' : '' ?>>Khách</option>
                        <?php if ($user['role_as'] == 1): ?>
                            <option value="1" selected>Admin</option>
                        <?php endif; ?>
                        <option value="2" <?= ((isset($role_as) && $role_as == 2) || (!isset($role_as) && $user['role_as'] == 2)) ? 'selected' : '' ?>>Nhân viên</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                    <a href="index.php" class="btn btn-secondary">Quay lại</a>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>