<?php
include(__DIR__ . "/includes/header.php");
include(__DIR__ . "/config/dbcon.php");

$bestSellingProducts = getBestSelling(8);
$LatestProducts = getLatestProducts(8);

// Hàm kiểm tra và lấy đường dẫn ảnh
function getImagePath($imageName) {
    if (empty($imageName)) {
        return "./assets/images/no-image.jpg";
    }
    
    $imagePath = "./anh_xedap/" . $imageName;
    return file_exists($imagePath) ? $imagePath : "./assets/images/no-image.jpg";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q-FASHION</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f9f9f9;
            min-width: 320px;
            overflow-x: hidden;
        }

        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
        }

        .container-fluid {
            width: 100%;
            padding: 0;
            margin: 0;
        }

        /* Header Section */
        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: linear-gradient(90deg, #3d5af1 0%, #0b1e6b 100%);
            color: #fff;
            z-index: 1000;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .top-header {
            padding: 10px 0;
        }

        .container-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 10px;
        }

        .left-group, .right-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .circle-icon {
            background: #fff;
            color: #3d5af1;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .divider {
            width: 1px;
            height: 16px;
            background: rgba(255, 255, 255, 0.3);
            margin: 0 6px;
        }

        .slogan {
            font-weight: 600;
            letter-spacing: 0.3px;
            color: #ffe600;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .hotline {
            background: #ffe600;
            color: #0b1e6b;
            font-weight: 700;
            padding: 6px 14px;
            border-radius: 20px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .hotline:hover {
            background: #fff200;
            color: #3d5af1;
            transform: translateY(-2px);
        }

        .top-header a {
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .top-header a:hover {
            color: #ffe600;
        }

        .top-header i {
            font-size: 14px;
        }

        /* Navigation */
        .mobile-menu {
            background: transparent;
            padding: 10px 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .mb-logo {
            font-size: 1.8rem;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
        }

        .mb-menu-toggle {
            font-size: 1.6rem;
            color: #fff;
            cursor: pointer;
        }

        .nav-menu {
            display: none;
            background: #0b1e6b;
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            z-index: 999;
            padding: 10px 0;
        }

        .nav-menu.active {
            display: block;
        }

        .nav-menu ul {
            list-style: none;
            padding: 0 15px;
        }

        .nav-menu li {
            margin: 10px 0;
        }

        .nav-menu a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.3s ease;
            display: block;
            padding: 8px 0;
        }

        .nav-menu a:hover {
            color: #ffe600;
        }

        .desktop-menu {
            background: transparent;
            padding: 10px 0;
        }

        .desktop-menu ul {
            list-style: none;
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 0;
            padding: 0;
        }

        .desktop-menu li {
            display: inline-block;
        }

        .desktop-menu a {
            color: #fff;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .desktop-menu a:hover {
            color: #ffe600;
        }

        /* Logout Button */
        .logout-button {
            position: fixed;
            bottom: 250px;  /* Đặt vị trí phía trên các nút shortcut */
            right: 20px;
            background: linear-gradient(135deg, #ff4444, #ff0000);
            color: white;
            border: none;
            width: 50px;  /* Giữ kích thước giống các nút khác */
            height: 50px;
            border-radius: 50%;
            font-size: 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 2px 10px rgba(255, 0, 0, 0.2);
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .logout-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 0, 0, 0.3);
            background: linear-gradient(135deg, #ff0000, #cc0000);
        }

        .logout-button i {
            font-size: 20px;
        }

        .logout-button .tooltip {
            position: absolute;
            right: 60px;
            background: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            color: #333;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .logout-button:hover .tooltip {
            opacity: 1;
            visibility: visible;
        }

        /* Hero Section */
        .hero {
            margin-top: 140px; /* Adjusted for fixed header */
            padding: 20px 0;
            background: linear-gradient(180deg, #ffffff, #f0f4ff);
        }

        /* Slider Styles */
        .slider {
            position: relative;
            width: 100%;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .slide {
            display: none;
            align-items: center;
            justify-content: space-between;
            padding: 30px;
            background: #fff;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .slide.active {
            display: flex;
            opacity: 1;
        }

        .slide .info {
            flex: 1;
            padding-right: 50px;
        }

        .slide .img {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .slide-img {
            max-width: 100%;
            height: auto;
            object-fit: contain;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .slide:hover .slide-img {
            transform: scale(1.05);
        }

        .slide-controll {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background: rgba(0,0,0,0.5);
            color: #fff;
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .slide-prev {
            left: 20px;
        }

        .slide-next {
            right: 20px;
        }

        .slide-controll:hover {
            background: #3d5af1;
            transform: translateY(-50%) scale(1.1);
        }

        .slider {
            position: relative;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .slide {
            display: none;
            align-items: center;
            justify-content: space-between;
            padding: 20px;
            gap: 15px;
        }

        .slide.active {
            display: flex;
        }

        .info {
            flex: 1;
            padding: 10px;
        }

        .info-content h2 {
            font-size: 2.5rem;
            color: #0b1e6b;
            margin-bottom: 10px;
            line-height: 1.2;
            font-weight: 700;
        }

        .info-content h3 {
            font-size: 1.8rem;
            color: #3d5af1;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .info-content p {
            font-size: 1.1rem;
            color: #555;
            margin-bottom: 15px;
            line-height: 1.7;
        }

        .img {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .img img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .slide-controll {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .slide-prev {
            left: 10px;
        }

        .slide-next {
            right: 10px;
        }

        .slide-controll:hover {
            background: #3d5af1;
        }

        /* Promotion Section */
        .promotion {
            padding: 30px 0;
            background: #fff;
        }

        .row {
            display: flex;
            flex-wrap: wrap;
            margin: 0 -10px;
        }

        .col-3, .col-4, .col-7 {
            padding: 10px;
        }

        .col-3 {
            width: 25%;
        }

        .col-4 {
            width: 33.333333%;
        }

        .col-7 {
            width: 58.333333%;
        }

        .promotion-box {
            background: #f8f9fa;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .promotion-box:hover {
            transform: translateY(-5px);
        }

        .promotion-box img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .promotion-box .text {
            padding: 15px;
            text-align: center;
        }

        .promotion-box h3 {
            font-size: 1.3rem;
            color: #0b1e6b;
            margin-bottom: 10px;
            font-weight: 600;
        }

        /* Product Section */
        .section {
            padding: 40px 0;
            background: #fff;
        }

        .section-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .section-header h2 {
            font-size: 2rem;
            color: #0b1e6b;
            margin-bottom: 10px;
            font-weight: 700;
            position: relative;
            display: inline-block;
        }

        .section-header h2:after {
            content: '';
            position: absolute;
            bottom: -6px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: #3d5af1;
            border-radius: 2px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            gap: 20px;
        }

        .product-card {
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card-img {
            position: relative;
            padding-top: 100%;
            overflow: hidden;
        }

        .product-card-img img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .product-card:hover .product-card-img img {
            transform: scale(1.05);
        }

        .product-card-info {
            padding: 15px;
        }

        .product-card-name {
            font-size: 1.1rem;
            color: #0b1e6b;
            margin-bottom: 8px;
            font-weight: 600;
            height: 2.4em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-card-price {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .product-card-price .curr-price {
            font-size: 1.2rem;
            color: #3d5af1;
            font-weight: bold;
        }

        .product-card-price del {
            color: #999;
            font-size: 0.95rem;
        }

        .btn-flat {
            background: #3d5af1;
            color: #fff;
            padding: 8px 16px;
            border-radius: 20px;
            border: none;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .btn-flat:hover {
            background: #0b1e6b;
            transform: translateY(-2px);
        }

        .btn-cart-add {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffe600;
            color: #0b1e6b;
            border: none;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-cart-add:hover {
            background: #fff200;
            transform: scale(1.1);
        }

        .product-btn {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        /* Special Product Section */
        .bg-second {
            background: #f5f5f5;
            padding: 30px 0;
        }

        .sp-item-img img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .sp-item-info {
            padding: 15px;
        }

        .sp-item-name {
            font-size: 1.4rem;
            color: #0b1e6b;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .sp-item-description {
            font-size: 1rem;
            color: #555;
            margin-bottom: 15px;
            line-height: 1.7;
        }

        /* Footer Info Section */
        .footer-info {
            background: #0b1e6b;
            padding: 40px 0;
            color: #fff;
        }

        .footer-info h3 {
            font-size: 1.2rem;
            margin-bottom: 15px;
            font-weight: 600;
        }

        .footer-info p {
            font-size: 0.9rem;
            color: #ddd;
            margin-bottom: 8px;
        }

        .footer-info img {
            max-width: 100%;
            height: auto;
        }

        /* Company Details Section */
        .company-details {
            padding: 20px 0;
            background: #f8f9fa;
        }

        .company-details h3 {
            font-size: 1.2rem;
            color: #0b1e6b;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .company-details p {
            font-size: 0.9rem;
            color: #555;
            margin-bottom: 8px;
        }

        /* Contact Bar */
        .contact-bar {
            position: fixed;
            right: 15px;
            bottom: 80px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 1000;
        }

        .contact-bar a {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #3d5af1;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 22px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
        }

        .contact-bar a:hover {
            transform: scale(1.1);
            background: #0b1e6b;
        }

        .contact-bar-toggle {
            display: none;
            position: fixed;
            right: 15px;
            bottom: 15px;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #3d5af1;
            color: #fff;
            font-size: 22px;
            border: none;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            z-index: 1001;
        }

        /* FPT.AI Chatbot Float Button */
        #fpt-ai-chatbot-button {
            width: 48px !important;
            height: 48px !important;
            border-radius: 50% !important;
            background: #ffe600 !important;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2) !important;
            transition: all 0.3s ease !important;
        }

        #fpt-ai-chatbot-button:hover {
            transform: scale(1.1) !important;
            background: #fff200 !important;
        }

        #fpt-ai-chatbot-button img {
            display: none;
        }

        #fpt-ai-chatbot-button::before {
            content: '\f544';
            font-family: 'Font Awesome 5 Free';
            font-weight: 900;
            font-size: 22px;
            color: #0b1e6b;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .container {
                max-width: 960px;
            }
        }

        @media (max-width: 992px) {
            .container {
                max-width: 720px;
            }

            .col-3, .col-4 {
                width: 50%;
            }

            .col-7 {
                width: 100%;
            }

            .slide {
                flex-direction: column;
                text-align: center;
            }

            .info-content h2 {
                font-size: 2rem;
            }

            .info-content h3 {
                font-size: 1.5rem;
            }

            .promotion-box img {
                height: 160px;
            }

            .sp-item-img img {
                height: 200px;
            }
        }

        @media (max-width: 768px) {
            .container {
                max-width: 540px;
            }

            .header {
                position: relative;
            }

            .hero {
                margin-top: 0;
            }

            .desktop-menu {
                display: none;
            }

            .mobile-menu {
                display: flex;
            }

            .info-content h2 {
                font-size: 1.8rem;
            }

            .info-content h3 {
                font-size: 1.3rem;
            }

            .section-header h2 {
                font-size: 1.8rem;
            }

            .promotion-box img {
                height: 140px;
            }

            .sp-item-img img {
                height: 180px;
            }

            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
                gap: 15px;
            }
        }

        @media (max-width: 576px) {
            .container {
                padding: 0 10px;
            }

            .col-3, .col-4 {
                width: 100%;
            }

            .product-grid {
                grid-template-columns: 1fr;
                gap: 10px;
            }

            .info-content h2 {
                font-size: 1.5rem;
            }

            .section-header h2 {
                font-size: 1.5rem;
            }

            .btn-flat {
                padding: 6px 14px;
                font-size: 13px;
            }

            .promotion-box img {
                height: 100%;
            }

            .sp-item img {
                height: auto;
            }
        }

        /* Contact Buttons */
        .contact-buttons {
            position: fixed;
            right: 20px;
            bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 999;
        }

        /* Auth Buttons */
        .auth-buttons {
            position: fixed;
            bottom: 250px;
            right: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
            z-index: 1000;
        }

        .auth-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .auth-btn.login {
            background: linear-gradient(135deg, #3d5af1, #0b1e6b);
        }

        .auth-btn.register {
            background: linear-gradient(135deg, #00b09b, #96c93d);
        }

        .auth-btn:hover {
            transform: translateY(-2px);
        }

        .auth-btn.login:hover {
            box-shadow: 0 5px 15px rgba(61, 90, 241, 0.3);
        }

        .auth-btn.register:hover {
            box-shadow: 0 5px 15px rgba(0, 176, 155, 0.3);
        }

        .auth-btn i {
            font-size: 20px;
        }

        .auth-btn .tooltip {
            position: absolute;
            right: 60px;
            background: #fff;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 12px;
            color: #333;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .auth-btn:hover .tooltip {
            opacity: 1;
            visibility: visible;
        }
    </style>
</head>
<body>
    <?php if(isset($_SESSION['auth'])) { ?>
    <a href="auth/logout.php" class="logout-button">
        <i class="fas fa-sign-out-alt"></i>
        <span class="tooltip">Đăng xuất</span>
    </a>
    <?php } else { ?>
    <div class="auth-buttons">
        <a href="auth/login.php" class="auth-btn login">
            <i class="fas fa-sign-in-alt"></i>
            <span class="tooltip">Đăng nhập</span>
        </a>
        <a href="auth/register.php" class="auth-btn register">
            <i class="fas fa-user-plus"></i>
            <span class="tooltip">Đăng ký</span>
        </a>
    </div>
    <?php } ?>

    <!-- Header Section -->
    <header class="header">
        <div class="top-header">
            <div class="container">
                <div class="container-header">
                    <div class="left-group">
                        <a href="#" class="mb-logo">Q-FASHION</a>
                    </div>
                    <div class="right-group">
                        <div class="circle-icon"><i class="fas fa-map-marker-alt"></i></div>
                        <a href="#"><i class="fas fa-store"></i>Tìm cửa hàng gần bạn</a>
                        <span class="divider"></span>
                        <div class="circle-icon"><i class="fas fa-truck"></i></div>
                        <span class="slogan"><i class="fas fa-check-circle"></i>FREESHIP NỘI THÀNH</span>
                        <span class="divider"></span>
                        <div class="circle-icon"><i class="fas fa-bolt"></i></div>
                        <span class="slogan"><i class="fas fa-clock"></i>GIAO NHANH 48H</span>
                        <span class="circle-icon"><i class="fas fa-phone-alt"></i></div>
                        <a class="hotline" href="tel:18009473"><i class="fas fa-headset"></i>Hotline:<span>1800 9473</span></a>
                        <span class="divider"></span>
                        <div class="circle-icon"><i class="fas fa-tools"></i></div>
                        <a href="tel:18009063" class="hotline"><i class="fas fa-wrench"></i>Bảo hành:<span>1800 9063</span></a>
                    </div>
                </div>
            </div>
        </div>

        <nav class="nav-menu" id="nav-menu">
            <div class="container">
                <ul>
                    <li><a href="./index.php">Trang chủ</a></li>
                    <li><a href="./products.php">Sản phẩm</a></li>
                    <li><a href="#">Giới thiệu</a></li>
                    <li><a href="#">Liên hệ</a></li>
                </ul>
            </div>
        </nav>
        <div class="desktop-menu">
            <div class="container">
                <ul>
                    <li><a href="./index.php">Trang chủ</a></li>
                    <li><a href="./products.php">Sản phẩm</a></li>
                    <li><a href="#">Giới thiệu</a></li>
                    <li><a href="#">Liên hệ</a></li>
                </ul>
            </div>
        </div>
    </header>

    <!-- Contact Bar -->
    <div class="contact-bar" id="contact-bar">
        <a href="https://zalo.me/[your-zalo-number]" target="_blank" title="Liên hệ qua Zalo">
            <i class="fab fa-telegram-plane"></i>
        </a>
        <a href="https://m.me/[your-messenger-id]" target="_blank" title="Liên hệ qua Messenger">
            <i class="fab fa-facebook-messenger"></i>
        </a>
        <a href="mailto:[email_address@example.com]" title="Liên hệ qua Gmail">
            <i class="fas fa-envelope"></i>
        </a>
    </div>
    <button class="contact-bar-toggle" id="contact-bar-toggle">
        <i class="fas fa-comment-dots"></i>
    </button>

    <!-- Hero Section -->
    <div class="hero">
        <div class="slider">
            <div class="container">
                <?php
                $count = 0;
                foreach ($bestSellingProducts as $product) {
                    if ($count == 3) {
                        break;
                    }
                ?>
                                         <div class="slide<?php echo $count == 0 ? ' active' : '' ?>">
                         <div class="info">
                             <div class="info-content">
                                 <h3 class="top-down"><?php echo $product['name']; ?></h3>
                                 <h2 class="top-down trans-delay-0-2"><?php echo $product['name']; ?></h2>
                                 <p class="top-down trans-delay-0-4"><?php echo $product['small_description']; ?></p>
                                 <div class="top-down trans-delay-0-6">
                                     <a href="./product-detail.php?slug=<?php echo $product['slug']; ?>">
                                         <button class="btn-flat btn-hover"><span>Mua ngay</span></button>
                                     </a>
                                 </div>
                             </div>
                         </div>
                         <div class="img right-left">
                        <img src="<?php echo getImagePath($product['image']); ?>" alt="<?php echo $product['name']; ?>" class="slide-img" style="max-width: 500px; height: auto;">
                         </div>
                     </div>
                <?php
                    $count++;
                }
                ?>
            </div>
            <button class="slide-controll slide-prev"><i class='bx bxs-chevron-left'></i></button>
            <button class="slide-controll slide-next"><i class='bx bxs-chevron-right'></i></button>
        </div>
    </div>

    <!-- Promotion Section -->
    <div class="promotion">
        <div class="container">
            <div class="row">
                <?php
                $count = 0;
                foreach ($LatestProducts as $product) {
                    if ($count == 3) {
                        break;
                    }
                ?>
                    <div class="col-4 col-md-12 col-sm-12">
                        <div class="promotion-box">
                            <div class="text">
                                <h3><?php echo $product['name']; ?></h3>
                                <a href="./product-detail.php?slug=<?php echo $product['slug']; ?>">
                                    <button class="btn-flat btn-hover"><span>Xem chi tiết</span></button>
                                </a>
                            </div>
                            <img src="<?php echo getImagePath($product['image']); ?>" alt="<?php echo $product['name']; ?>">
                        </div>
                    </div>
                <?php
                    $count++;
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Product List -->
    <div class="section">
        <div class="container">
            <div class="section-header">
                <h2>Những sản phẩm mới nhất</h2>
            </div>
            <div class="product-grid" id="latest-products">
                <?php
                foreach ($LatestProducts as $product) {
                ?>
                    <div class="product-card">
                        <div class="product-card-img">
                            <img src="<?php echo getImagePath($product['image']); ?>" alt="<?php echo $product['name']; ?>">
                        </div>
                        <div class="product-card-info">
                            <div class="product-btn">
                                <a href="./product-detail.php?slug=<?php echo $product['slug']; ?>">
                                    <button class="btn-flat btn-hover btn-shop-now">Mua ngay</button>
                                </a>
                                <form method="post" action="add-to-cart.php" style="display:inline;">
                                    <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" class="btn-flat btn-hover btn-cart-add" title="Thêm vào giỏ hàng">
                                        <i class='bx bxs-cart-add'></i>
                                    </button>
                                </form>
                                <button class="btn-flat btn-hover btn-cart-add">
                                    <i class='bx bxs-heart'></i>
                                </button>
                            </div>
                            <div class="product-card-name">
                                <?php echo $product['name']; ?>
                            </div>
                            <div class="product-card-price">
                                <span><del><?php echo number_format($product['original_price'], 0, ',', '.'); ?> VND</del></span>
                                <span class="curr-price"><?php echo number_format($product['selling_price'], 0, ',', '.'); ?> VND</span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="section-footer">
                <a href="./products.php" class="btn-flat btn-hover">Xem tất cả</a>
            </div>
        </div>
    </div>

    <!-- Special Product -->
    <div class="bg-second">
        <div class="section container">
            <div class="row">
                <?php
                foreach ($bestSellingProducts as $product) {
                ?>
                    <div class="col-4 col-md-4">
                        <div class="sp-item-img">
                            <img src="<?php echo getImagePath($product['image']); ?>" alt="<?php echo $product['name']; ?>">
                        </div>
                    </div>
                    <div class="col-7 col-md-8">
                        <div class="sp-item-info">
                            <div class="sp-item-name"><?php echo $product['name']; ?></div>
                            <p class="sp-item-description">
                                <?php echo $product['small_description']; ?>
                            </p>
                            <a href="./product-detail.php?slug=<?php echo $product['slug']; ?>">
                                <button class="btn-flat btn-hover">Xem chi tiết</button>
                            </a>
                        </div>
                    </div>
                <?php
                    break;
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Product List -->
    <div class="section">
        <div class="container">
            <div class="section-header">
                <h2>Những sản phẩm bán chạy nhất</h2>
            </div>
            <div class="product-grid" id="best-products">
                <?php
                foreach ($bestSellingProducts as $product) {
                ?>
                    <div class="product-card">
                        <div class="product-card-img">
                            <img src="<?php echo getImagePath($product['image']); ?>" alt="<?php echo $product['name']; ?>">
                        </div>
                        <div class="product-card-info">
                            <div class="product-btn">
                                <a href="./product-detail.php?slug=<?php echo $product['slug']; ?>">
                                    <button class="btn-flat btn-hover btn-shop-now">Mua ngay</button>
                                </a>
                                <button class="btn-flat btn-hover btn-cart-add">
                                    <i class='bx bxs-cart-add'></i>
                                </button>
                                <button class="btn-flat btn-hover btn-cart-add">
                                    <i class='bx bxs-heart'></i>
                                </button>
                            </div>
                            <div class="product-card-name">
                                <?php echo $product['name']; ?>
                            </div>
                            <div class="product-card-price">
                                <span><del><?php echo number_format($product['original_price'], 0, ',', '.'); ?> VND</del></span>
                                <span class="curr-price"><?php echo number_format($product['selling_price'], 0, ',', '.'); ?> VND</span>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="section-footer">
                <a href="./products.php" class="btn-flat btn-hover">Xem tất cả</a>
            </div>
        </div>
    </div>

    <?php include(__DIR__ . "/includes/footer.php") ?>

    <script>
        // Mobile Menu and Contact Bar Toggle
        document.addEventListener('DOMContentLoaded', () => {


            // Contact Bar Toggle
            const contactToggle = document.querySelector('#contact-bar-toggle');
            const contactBar = document.querySelector('#contact-bar');
            contactToggle.addEventListener('click', () => {
                contactBar.classList.toggle('active');
            });
        });
    </script>
    <script>
        // FPT.AI Chatbot Configs
        let liveChatBaseUrl   = document.location.protocol + '//' + 'livechat.fpt.ai/v36/src';
        let LiveChatSocketUrl = 'livechat.fpt.ai:443';
        let FptAppCode        = '31c45b4e1c2755587f3714e243df6c9d';
        let FptAppName        = 'Bicycle Shop';
        let CustomStyles = {
            headerBackground: 'linear-gradient(86.7deg, #3353a2ff 0.85%, #31b7b7ff 98.94%)',
            headerTextColor: '#ffffffff',
            headerLogoEnable: false,
            headerLogoLink: 'https://chatbot-tools.fpt.ai/livechat-builder/img/Icon-fpt-ai.png',
            headerText: 'Hỗ trợ trực tuyến',
            primaryColor: '#6d9ccbff',
            secondaryColor: '#ecececff',
            primaryTextColor: '#ffffffff',
            secondaryTextColor: '#000000DE',
            buttonColor: '#b4b4b4ff',
            buttonTextColor: '#ffffffff',
            bodyBackgroundEnable: false,
            bodyBackgroundLink: '',
            avatarBot: 'https://chatbot-tools.fpt.ai/livechat-builder/img/bot.png',
            sendMessagePlaceholder: 'Nhập tin nhắn',
            floatButtonLogo: 'https://chatbot-tools.fpt.ai/livechat-builder/img/Icon-fpt-ai.png',
            floatButtonTooltip: 'FPT.AI xin chào',
            floatButtonTooltipEnable: false,
            customerLogo: 'https://chatbot-tools.fpt.ai/livechat-builder/img/bot.png',
            customerWelcomeText: 'Vui lòng nhập tên của bạn',
            customerButtonText: 'Bắt đầu',
            prefixEnable: false,
            prefixType: 'radio',
            prefixOptions: ["Anh","Chị"],
            prefixPlaceholder: 'Danh xưng',
            css: ''
        };
        if (!FptAppCode) {
            let appCodeFromHash = window.location.hash.substr(1);
            if (appCodeFromHash.length === 32) {
                FptAppCode = appCodeFromHash;
            }
        }
        let FptLiveChatConfigs = {
            appName: FptAppName,
            appCode: FptAppCode,
            themes: '',
            styles: CustomStyles
        };
        let FptLiveChatScript = document.createElement('script');
        FptLiveChatScript.id = 'fpt_ai_livechat_script';
        FptLiveChatScript.src = liveChatBaseUrl + '/static/fptai-livechat.js';
        document.body.appendChild(FptLiveChatScript);
        let FptLiveChatStyles = document.createElement('link');
        FptLiveChatStyles.id = 'fpt_ai_livechat_styles';
        FptLiveChatStyles.rel = 'stylesheet';
        FptLiveChatStyles.href = liveChatBaseUrl + '/static/fptai-livechat.css';
        document.body.appendChild(FptLiveChatStyles);
        FptLiveChatScript.onload = function () {
            fpt_ai_render_chatbox(FptLiveChatConfigs, liveChatBaseUrl, LiveChatSocketUrl);
        };
    </script>
         <script src="./assets/js/app.js"></script>
     <script src="./assets/js/index.js"></script>
     <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slides = document.querySelectorAll('.slide');
            const prevBtn = document.querySelector('.slide-prev');
            const nextBtn = document.querySelector('.slide-next');
            let currentSlide = 0;

            function showSlide(index) {
                slides.forEach(slide => slide.classList.remove('active'));
                slides[index].classList.add('active');
            }

            function nextSlide() {
                currentSlide = (currentSlide + 1) % slides.length;
                showSlide(currentSlide);
            }

            function prevSlide() {
                currentSlide = (currentSlide - 1 + slides.length) % slides.length;
                showSlide(currentSlide);
            }

            // Add click event listeners
            prevBtn.addEventListener('click', prevSlide);
            nextBtn.addEventListener('click', nextSlide);

            // Auto slide every 5 seconds
            setInterval(nextSlide, 5000);
        });
     </script>
</body>
</html>