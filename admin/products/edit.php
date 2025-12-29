<?php

session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_as']) || ($_SESSION['role_as'] != 1 && $_SESSION['role_as'] != 2)) {
    header('Location: ../../auth/login.php');
    exit();
}

require_once '../../includes/db.php';

// Lấy ID sản phẩm
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: index.php');
    exit();
}

// Lấy danh mục
$categories = [];
$result = $conn->query("SELECT id, name FROM categories ORDER BY name ASC");
if ($result) {
    $categories = $result->fetch_all(MYSQLI_ASSOC);
}

// Lấy thông tin sản phẩm
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) {
    header('Location: index.php');
    exit();
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category_id = intval($_POST['category_id']);
    $original_price = floatval($_POST['original_price']);
    $selling_price = floatval($_POST['selling_price']);
    $qty = intval($_POST['qty']);
    $status = isset($_POST['status']) ? 1 : 0;
    $image = $product['image'];
    $image1 = $product['image1'];
    $image2 = $product['image2'];
    $image3 = $product['image3'];
    $slug = !empty($_POST['slug']) ? trim($_POST['slug']) : $name;
    $slug = strtolower($slug);
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    $slug = preg_replace('/-+/', '-', $slug);
    $slug = trim($slug, '-');
    $description = trim($_POST['description']);
    $small_description = trim($_POST['small_description']);
    $target_dir = "../../uploads/products/";
    if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

    // Xử lý upload ảnh mới
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = time() . '_' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $target_dir . $image);
    }
    if (isset($_FILES['image1']) && $_FILES['image1']['error'] == 0) {
        $image1 = time() . '_' . basename($_FILES['image1']['name']);
        move_uploaded_file($_FILES['image1']['tmp_name'], $target_dir . $image1);
    }
    if (isset($_FILES['image2']) && $_FILES['image2']['error'] == 0) {
        $image2 = time() . '_' . basename($_FILES['image2']['name']);
        move_uploaded_file($_FILES['image2']['tmp_name'], $target_dir . $image2);
    }
    if (isset($_FILES['image3']) && $_FILES['image3']['error'] == 0) {
        $image3 = time() . '_' . basename($_FILES['image3']['name']);
        move_uploaded_file($_FILES['image3']['tmp_name'], $target_dir . $image3);
    }

    // Validate
    if ($name == '') $errors[] = "Tên sản phẩm không được để trống";
    if ($category_id == 0) $errors[] = "Vui lòng chọn danh mục";
    if ($selling_price <= 0) $errors[] = "Giá bán phải lớn hơn 0";
    if ($qty < 0) $errors[] = "Số lượng không hợp lệ";

    if (empty($errors)) {
        $stmt = $conn->prepare("UPDATE products SET category_id=?, name=?, slug=?, small_description=?, description=?, original_price=?, selling_price=?, qty=?, status=?, image=?, image1=?, image2=?, image3=? WHERE id=?");
        $stmt->bind_param("isssssdiissssi", $category_id, $name, $slug, $small_description, $description, $original_price, $selling_price, $qty, $status, $image, $image1, $image2, $image3, $id);
        if ($stmt->execute()) {
            header("Location: index.php?msg=edit_success");
            exit();
        } else {
            $errors[] = "Cập nhật sản phẩm thất bại!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Sửa sản phẩm</title>
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
        <div class="container mt-5">
            <h2>Sửa sản phẩm</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <?= implode('<br>', $errors) ?>
                </div>
            <?php endif; ?>
            <form method="post" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Tên sản phẩm</label>
                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars(isset($name) ? $name : $product['name']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Danh mục</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Chọn danh mục --</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= ((isset($category_id) && $category_id == $cat['id']) || (!isset($category_id) && $product['category_id'] == $cat['id'])) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Giá gốc</label>
                    <input type="number" name="original_price" class="form-control" min="100000" step="100000" value="<?= htmlspecialchars(isset($original_price) ? $original_price : $product['original_price']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Giá bán</label>
                    <input type="number" name="selling_price" class="form-control" min="100000" step="100000" required value="<?= htmlspecialchars(isset($selling_price) ? $selling_price : $product['selling_price']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Số lượng</label>
                    <input type="number" name="qty" class="form-control" min="0" required value="<?= htmlspecialchars(isset($qty) ? $qty : $product['qty']) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hình ảnh chính</label>
                    <input type="file" name="image" class="form-control">
                    <?php if (!empty($product['image'])): ?>
                        <div class="mt-2">
                            <img src="../../uploads/products/<?= htmlspecialchars($product['image']) ?>" alt="" style="width:80px;">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hình ảnh phụ 1</label>
                    <input type="file" name="image1" class="form-control">
                    <?php if (!empty($product['image1'])): ?>
                        <div class="mt-2">
                            <img src="../../uploads/products/<?= htmlspecialchars($product['image1']) ?>" alt="" style="width:80px;">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hình ảnh phụ 2</label>
                    <input type="file" name="image2" class="form-control">
                    <?php if (!empty($product['image2'])): ?>
                        <div class="mt-2">
                            <img src="../../uploads/products/<?= htmlspecialchars($product['image2']) ?>" alt="" style="width:80px;">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Hình ảnh phụ 3</label>
                    <input type="file" name="image3" class="form-control">
                    <?php if (!empty($product['image3'])): ?>
                        <div class="mt-2">
                            <img src="../../uploads/products/<?= htmlspecialchars($product['image3']) ?>" alt="" style="width:80px;">
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Slug (đường dẫn SEO)</label>
                    <input type="text" name="slug" class="form-control" required value="<?= htmlspecialchars(isset($slug) ? $slug : $product['slug']) ?>">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Mô tả chi tiết</label>
                    <textarea name="description" class="form-control" rows="4"><?= htmlspecialchars(isset($description) ? $description : $product['description']) ?></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Mô tả ngắn</label>
                    <textarea name="small_description" class="form-control" rows="2"><?= htmlspecialchars(isset($small_description) ? $small_description : $product['small_description']) ?></textarea>
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="status" id="status" <?= ((isset($status) && $status == 1) || (!isset($status) && $product['status'] == 1)) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="status">
                            Hiển thị
                        </label>
                    </div>
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