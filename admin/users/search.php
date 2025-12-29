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
    $where = "WHERE name LIKE '%$search_escaped%' OR email LIKE '%$search_escaped%' OR id = '$search_escaped'";
}

$sql = "SELECT * FROM users $where ORDER BY created_at DESC";
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
    <title>Kết quả tìm kiếm người dùng</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-5">
        <h2>Kết quả tìm kiếm người dùng</h2>
        <form class="row mb-4" method="get">
            <div class="col-md-4">
                <input type="text" name="q" class="form-control" placeholder="Tìm theo tên, email hoặc ID..." value="<?= htmlspecialchars($search) ?>">
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
                    <th>Tên</th>
                    <th>Email</th>
                    <th>Quyền</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
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
                                <?= $user['role_as'] == 1 ? '<span class="badge bg-primary">Admin</span>' : '<span class="badge bg-secondary">Khách</span>' ?>
                            </td>
                            <td><?= htmlspecialchars($user['created_at']) ?></td>
                            <td>
                                <a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm">Sửa</a>
                                <a href="delete.php?id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn chắc chắn muốn xóa?')">Xóa</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Không có người dùng nào phù hợp.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>