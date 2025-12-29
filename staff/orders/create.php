<?php
session_start();
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role_as']) || $_SESSION['role_as'] != 2) {
    header('Location: ../../auth/login.php');
    exit();
}

require_once '../../includes/db.php';

// Lấy danh sách khách hàng
$customers = [];
$result = $conn->query("SELECT id, name FROM users WHERE role_as = 0 ORDER BY name ASC");
if ($result) {
    $customers = $result->fetch_all(MYSQLI_ASSOC);
}

// Lấy danh sách sản phẩm
$products = [];
$result = $conn->query("SELECT id, name, selling_price, qty FROM products WHERE status = 0 ORDER BY name ASC");
if ($result) {
    $products = $result->fetch_all(MYSQLI_ASSOC);
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
    $guest_name = isset($_POST['guest_name']) ? trim($_POST['guest_name']) : '';
    $product_ids = isset($_POST['product_id']) ? $_POST['product_id'] : [];
    $quantities = isset($_POST['quantity']) ? $_POST['quantity'] : [];

    if ($user_id <= 0 && $guest_name == '') $errors[] = "Vui lòng chọn khách hàng hoặc nhập tên khách lẻ";
    if (empty($product_ids)) $errors[] = "Vui lòng chọn ít nhất một sản phẩm";

    foreach ($quantities as $qty) {
        if (intval($qty) <= 0) {
            $errors[] = "Số lượng sản phẩm phải lớn hơn 0";
            break;
        }
    }

    if (empty($errors)) {
        // Tạo đơn hàng mới
        if ($user_id > 0) {
            $stmt = $conn->prepare("INSERT INTO orders (user_id, status, created_at) VALUES (?, 0, NOW())");
            $stmt->bind_param("i", $user_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO orders (guest_name, status, created_at) VALUES (?, 0, NOW())");
            $stmt->bind_param("s", $guest_name);
        }
        if ($stmt->execute()) {
            $order_id = $conn->insert_id;
            $stmt_detail = $conn->prepare("INSERT INTO order_detail (order_id, product_id, selling_price, quantity) VALUES (?, ?, ?, ?)");
            foreach ($product_ids as $idx => $pid) {
                $pid = intval($pid);
                $qty = intval($quantities[$idx]);
                $stmt_price = $conn->prepare("SELECT selling_price FROM products WHERE id = ?");
                $stmt_price->bind_param("i", $pid);
                $stmt_price->execute();
                $stmt_price->bind_result($selling_price);
                $stmt_price->fetch();
                $stmt_price->close();

                $stmt_detail->bind_param("iidi", $order_id, $pid, $selling_price, $qty);
                $stmt_detail->execute();
                $conn->query("UPDATE products SET qty = qty - $qty WHERE id = $pid");
            }
            $stmt_detail->close();
            header("Location: index.php?msg=add_success");
            exit();
        } else {
            $errors[] = "Tạo đơn hàng thất bại!";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Tạo đơn hàng mới</title>
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
    <script>
        // Thêm dòng này để truyền dữ liệu khách hàng từ PHP sang JS
        const customers = <?= json_encode($customers) ?>;

        function addProductRow() {
            const row = document.querySelector('.product-row').cloneNode(true);
            row.querySelectorAll('input, select').forEach(el => {
                if (el.type === 'number') el.value = 1;
                if (el.tagName === 'SELECT') el.selectedIndex = 0;
            });
            document.getElementById('products-list').appendChild(row);
        }

        function removeProductRow(btn) {
            const rows = document.querySelectorAll('.product-row');
            if (rows.length > 1) btn.closest('.product-row').remove();
        }

        function toggleCustomerType(type) {
            if (type === 'guest') {
                document.getElementById('guest-name-group').style.display = 'block';
                document.getElementById('account-customer-group').style.display = 'none';
                document.getElementById('selected-user-id').value = '';
                document.getElementById('search-customer').value = '';
                document.getElementById('customer-search-results').innerHTML = '';
            } else {
                document.getElementById('guest-name-group').style.display = 'none';
                document.getElementById('account-customer-group').style.display = 'block';
            }
        }

        function searchCustomer() {
            const input = document.getElementById('search-customer').value.toLowerCase();
            const resultsDiv = document.getElementById('customer-search-results');
            resultsDiv.innerHTML = '';
            if (input.length === 0) return;
            let found = false;
            customers.forEach(function(cus) {
                if (cus.name.toLowerCase().includes(input)) {
                    found = true;
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = cus.name;
                    item.onclick = function() {
                        document.getElementById('search-customer').value = cus.name;
                        document.getElementById('selected-user-id').value = cus.id;
                        resultsDiv.innerHTML = '';
                    };
                    resultsDiv.appendChild(item);
                }
            });
            if (!found) {
                const item = document.createElement('div');
                item.className = 'list-group-item text-danger';
                item.textContent = 'Không tìm thấy khách hàng';
                resultsDiv.appendChild(item);
            }
        }
    </script>
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
        <div class="container mt-5">
            <h2>Tạo đơn hàng mới</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger"><?= implode('<br>', $errors) ?></div>
            <?php endif; ?>
            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Loại khách hàng</label><br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="customer_type" id="guestRadio" value="guest"
                            onclick="toggleCustomerType('guest')" <?= (!isset($user_id) || $user_id == 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="guestRadio">Khách lẻ (không tài khoản)</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="customer_type" id="accountRadio" value="account"
                            onclick="toggleCustomerType('account')" <?= (isset($user_id) && $user_id > 0) ? 'checked' : '' ?>>
                        <label class="form-check-label" for="accountRadio">Khách có tài khoản</label>
                    </div>
                </div>
                <div class="mb-3" id="guest-name-group" style="display:<?= (!isset($user_id) || $user_id == 0) ? 'block' : 'none' ?>">
                    <label class="form-label">Tên khách lẻ</label>
                    <input type="text" name="guest_name" class="form-control" value="<?= isset($guest_name) ? htmlspecialchars($guest_name) : '' ?>">
                </div>
                <div class="mb-3" id="account-customer-group" style="display:<?= (isset($user_id) && $user_id > 0) ? 'block' : 'none' ?>">
                    <label class="form-label">Tìm kiếm khách hàng</label>
                    <input type="text" id="search-customer" class="form-control mb-2" onkeyup="searchCustomer()" placeholder="Nhập tên khách hàng...">
                    <input type="hidden" name="user_id" id="selected-user-id" value="<?= isset($user_id) ? $user_id : '' ?>">
                    <div id="customer-search-results" class="list-group" style="max-height:200px;overflow-y:auto;">
                        <!-- Kết quả tìm kiếm sẽ hiển thị ở đây -->
                    </div>
                </div>
                <label class="form-label">Sản phẩm</label>
                <div id="products-list">
                    <div class="row g-2 align-items-end product-row mb-2">
                        <div class="col-md-6">
                            <select name="product_id[]" class="form-select" required>
                                <option value="">-- Chọn sản phẩm --</option>
                                <?php foreach ($products as $pro): ?>
                                    <option value="<?= $pro['id'] ?>">
                                        <?= htmlspecialchars($pro['name']) ?> (Còn: <?= $pro['qty'] ?>, Giá: <?= number_format($pro['selling_price'], 0, ',', '.') ?>đ)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <input type="number" name="quantity[]" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-success" onclick="addProductRow()">+</button>
                            <button type="button" class="btn btn-danger" onclick="removeProductRow(this)">-</button>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Tạo đơn hàng</button>
                <a href="index.php" class="btn btn-secondary mt-3">Quay lại</a>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>