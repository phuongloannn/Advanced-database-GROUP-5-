<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include core files
require_once(__DIR__ . "/../config/dbcon.php");
require_once(__DIR__ . "/../functions/userfunctions.php");

// Initialize variables
$search = isset($_GET["search"]) ? $_GET["search"] : "";
$page = isset($_GET["page"]) ? max(1, (int)$_GET["page"]) : 1;
$type = isset($_GET["type"]) ? $_GET["type"] : "";

$page = $page - 1;
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q-Fashion</title>
    
    <!-- CSS -->
    <link href="/fashion_shop_group5/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="/fashion_shop_group5/assets/css/custom.css" rel="stylesheet">
    <link href="/fashion_shop_group5/assets/css/header-footer.css" rel="stylesheet">
    <link href="/fashion_shop_group5/assets/css/footer.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="stylesheet" href="assets/css/user-menu.css">
    
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script src="/fashion_shop_group5/assets/js/bootstrap.bundle.min.js"></script>
    <script src="/fashion_shop_group5/assets/js/header-functions.js"></script>
    <script src="assets/js/jquery-3.6.0.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/custom.js"></script>
    <script src="assets/js/user-menu.js"></script>

    <!-- Custom CSS -->
    <style>
        /* CSS cho phần rating */
        .rating-stars i {
            margin: 0 2px;
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            cursor: pointer;
            font-size: 1.5em;
            color: #ddd;
            margin: 0 2px;
        }

        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #ffc107;
        }

        /* CSS cho avatar */
        .avatar {
            width: 40px;
            height: 40px;
        }

        .avatar-text {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2em;
        }

        /* CSS cho review item */
        .review-item {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .review-text {
            color: #444;
            line-height: 1.6;
        }

        /* Hiệu ứng hover cho nút */
        .btn-primary {
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Styles for Orders Icon */
        .icon-orders {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: transparent;
            color: #495057;
            transition: all 0.2s ease;
        }

        .icon-orders:hover {
            background: #f8f9fa;
            color: #212529;
            text-decoration: none;
        }

        .icon-orders i {
            font-size: 1.1rem;
        }

        .icon-orders .order-badge {
            position: absolute;
            top: -2px;
            right: -2px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 14px;
            height: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.65rem;
            font-weight: 500;
        }

        /* Tooltip styles */
        .icon-orders::after {
            content: 'Đơn hàng của tôi';
            position: absolute;
            bottom: -30px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.75rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.2s ease;
        }

        .icon-orders:hover::after {
            opacity: 1;
            visibility: visible;
        }

        @media (max-width: 768px) {
            .btn-orders {
                padding: 4px;
            }
            .btn-orders span {
                display: none;
            }
            .btn-orders i {
                margin: 0;
                font-size: 1rem;
            }
        }
    </style>
    
    <!-- Debug Script -->
    <script>
        $(document).ready(function() {
            console.log('jQuery version:', $.fn.jquery);
            console.log('Document ready fired');
            
            // Test jQuery event binding
            $(document).on('click', '.addToCartBtn', function(e) {
                console.log('Add to cart button clicked - Debug listener');
            });
        });
    </script>
</head>

<body class="<?= isset($_SESSION['auth']) ? 'user-logged-in' : '' ?>">
    <?php include(__DIR__ . "/navbar.php"); ?>

    <!-- Header -->
    <header class="header">
        <!-- Navigation -->
        <nav class="nav-menu">
            <div class="container">
                <div class="nav-wrapper">
                    <a href="/fashion_shop_group5/index.php" class="logo">Q-Fashion</a>
                    <div class="nav-content">
                        <ul class="nav-links">
                            <li><a href="/fashion_shop_group5/index.php" class="nav-link"><i class="fas fa-home"></i> Trang chủ</a></li>
                            <li><a href="/fashion_shop_group5/category.php" class="nav-link"><i class="fas fa-list"></i> Danh mục</a></li>
                            <li><a href="/fashion_shop_group5/products.php" class="nav-link"><i class="fas fa-bicycle"></i> Sản phẩm</a></li>
                            <li><a href="/fashion_shop_group5/about.php" class="nav-link"><i class="fas fa-info-circle"></i> Giới thiệu</a></li>
                            <li><a href="/fashion_shop_group5/contact.php" class="nav-link"><i class="fas fa-envelope"></i> Liên hệ</a></li>
                        </ul>
                        <div class="nav-actions">
                            <div class="search-bar">
                                <form action="/fashion_shop_group5/products.php" method="GET" class="search-form">
                                    <input type="text" name="search" class="search-input" placeholder="Tìm kiếm sản phẩm..." value="<?= htmlspecialchars($search) ?>">
                                    <button type="submit" class="search-button">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </form>
                            </div>
                            <?php if(isset($_SESSION['auth'])) { ?>
                                <a href="/fashion_shop_group5/cart.php" class="nav-action-btn" title="Giỏ hàng">
                                    <i class="fas fa-shopping-cart"></i>
                                    <?php
                                    $items = getCartItems();
                                    if(mysqli_num_rows($items) > 0) {
                                    ?>
                                    <span class="cart-count"><?= mysqli_num_rows($items) ?></span>
                                    <?php
                                    }
                                    ?>
                                </a>
                                <a href="/fashion_shop_group5/wishlist.php" class="nav-action-btn" title="Yêu thích">
                                    <i class="fas fa-heart"></i>
                                </a>
                                <a href="/fashion_shop_group5/my-orders.php" class="nav-action-btn" title="Đơn hàng">
                                    <i class="fas fa-box"></i>
                                </a>
                               <div class="user-menu-wrapper">
    <button class="user-menu-trigger">
        <div class="user-avatar">
            <?php 
            // Lấy thông tin user từ database với avatar
            $userId = $_SESSION['auth_user']['id'];
            $userQuery = "SELECT avatar, name FROM users WHERE id = ?";
            $stmt = mysqli_prepare($con, $userQuery);
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            $userResult = mysqli_stmt_get_result($stmt);
            
            if ($userResult && mysqli_num_rows($userResult) > 0) {
                $userData = mysqli_fetch_assoc($userResult);
                $avatar = $userData['avatar'];
                $userName = $userData['name'];
                
                if(!empty($avatar)) {
                    echo '<img src="'.htmlspecialchars($avatar).'" alt="Avatar" width="32" height="32" style="border-radius: 50%; object-fit: cover;">';
                } else {
                    echo '<div class="avatar-text">';
                    echo strtoupper(substr($userName, 0, 2));
                    echo '</div>';
                }
            } else {
                // Fallback nếu không lấy được từ database
                $userName = $_SESSION['auth_user']['name'];
                echo '<div class="avatar-text">';
                echo strtoupper(substr($userName, 0, 2));
                echo '</div>';
            }
            ?>
        </div>
        <span class="d-none d-md-inline"><?= htmlspecialchars($_SESSION['auth_user']['name']) ?></span>
        <i class="fas fa-chevron-down"></i>
    </button>
    
    <div class="user-menu">
        <div class="user-menu-header">
            <div class="user-info">
                <div class="user-avatar">
                    <?php 
                    if(!empty($avatar)) {
                        echo '<img src="'.htmlspecialchars($avatar).'" alt="Avatar" width="48" height="48" style="border-radius: 50%; object-fit: cover;">';
                    } else {
                        echo '<div class="avatar-text" style="width:48px;height:48px;">';
                        echo strtoupper(substr($userName, 0, 2));
                        echo '</div>';
                    }
                    ?>
                </div>
                <div class="user-details">
                    <h5><?= htmlspecialchars($_SESSION['auth_user']['name']) ?></h5>
                    <p><?= htmlspecialchars($_SESSION['auth_user']['email']) ?></p>
                </div>
            </div>
        </div>
        
        <div class="user-menu-items">
            <a href="user-profile.php" class="menu-item">
                <i class="fas fa-user"></i>
                Thông tin cá nhân
            </a>
            <a href="my-orders.php" class="menu-item">
                <i class="fas fa-shopping-bag"></i>
                Đơn hàng của tôi
            </a>
            <a href="wishlist.php" class="menu-item">
                <i class="fas fa-heart"></i>
                Sản phẩm yêu thích
            </a>
            <a href="my-reviews.php" class="menu-item">
                <i class="fas fa-star"></i>
                Đánh giá của tôi
            </a>
            
            <div class="menu-divider"></div>
            
            <?php if($_SESSION['auth_user']['role_as'] == 1) { ?>
            <a href="admin/index.php" class="menu-item">
                <i class="fas fa-cog"></i>
                Quản trị website
            </a>
            <div class="menu-divider"></div>
            <?php } ?>
            
            <a href="logout.php" class="menu-item logout-item">
                <i class="fas fa-sign-out-alt"></i>
                Đăng xuất
            </a>
        </div>
    </div>
</div>
                            <?php } else { ?>
                                <a href="/fashion_shop_group5/auth/login.php" class="nav-action-btn" title="Đăng nhập">
                                    <i class="fas fa-user"></i>
                                </a>
                                <a href="/fashion_shop_group5/auth/register.php" class="nav-action-btn" title="Đăng ký">
                                    <i class="fas fa-user-plus"></i>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </header>

    <!-- Loading Overlay -->
    <div class="loading-overlay">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Đang tải...</span>
        </div>
    </div>
</body>
</html>