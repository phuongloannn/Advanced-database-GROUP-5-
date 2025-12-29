<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Q-Fashion</title>
    
    <!-- Bootstrap CSS -->
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        .order-status {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 500;
        }
        .status-pending { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .status-preparing { background: #cce5ff; color: #004085; border: 1px solid #b8daff; }
        .status-delivered { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-completed { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .status-cancelled { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status-returned { background: #e2e3e5; color: #383d41; border: 1px solid #d6d8db; }

        .sidebar {
            min-height: 100vh;
            background: #333;
            color: white;
        }

        .nav-link {
            color: white;
        }

        .nav-link:hover {
            background: #444;
            color: #fff;
        }

        .active {
            background: #444;
        }

        .content {
            padding: 20px;
        }

        .card {
            margin-bottom: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }

        .card-header {
            background: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="../dashboard.php">Q-Fashion Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="../../index.php" target="_blank">
                        <i class="fas fa-store"></i> Xem cửa hàng
                    </a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user"></i> Admin
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="../settings.php"><i class="fas fa-cog"></i> Cài đặt</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Sidebar & Content -->
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 d-none d-md-block sidebar py-4">
            <div class="sidebar-sticky">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="../dashboard.php">
                            <i class="fas fa-tachometer-alt"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../categories/">
                            <i class="fas fa-list"></i> Danh mục
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../products/">
                            <i class="fas fa-box"></i> Sản phẩm
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="../orders/">
                            <i class="fas fa-shopping-cart"></i> Đơn hàng
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../users/">
                            <i class="fas fa-users"></i> Người dùng
                        </a>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Main Content -->
        <main role="main" class="col-md-10 ms-sm-auto px-4 content"> 