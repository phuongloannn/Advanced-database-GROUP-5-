<!-- Footer Info Section -->
<div class="section footer-info">
    <div class="container">
        <div class="row">
            <div class="col-3 col-md-6 col-sm-12">
                <h3>Về Q-FASHION</h3>
                <p>Giới Thiệu</p>
                <p>Chính Sách Bảo Mật</p>
                <p>Điều kiện & Điều khoản</p>
                <p>Đổi Trả và Hoàn Tiền</p>
            </div>
            <div class="col-3 col-md-6 col-sm-12">
                <h3>Chăm Sóc Khách Hàng</h3>
                <p>Câu Hỏi Thường Gặp</p>
                <p>Quản Lý Thông Tin</p>
                <p>Chính Sách Đổi Hàng</p>
                <p>Chính Sách Bảo Hành</p>
            </div>
            <div class="col-3 col-md-6 col-sm-12">
                <h3>Phương Thức Thanh Toán</h3>
                <div class="payment-methods" style="display: flex; justify-content: space-between; align-items: center; gap: 10px;">
                    <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRB6YnQoM78NCEw3f--iWcGhpQFjBxfo9k6fw&s" alt="Visa" style="max-width: 40px; height: auto;">
                    <img src="https://ibrand.vn/wp-content/uploads/2024/07/Mastercard-Logo-1.png" alt="MasterCard" style="max-width: 40px; height: auto;">
                    <img src="https://play-lh.googleusercontent.com/dQbjuW6Jrwzavx7UCwvGzA_sleZe3-Km1KISpMLGVf1Be5N6hN6-tdKxE5RDQvOiGRg=w240-h480-rw" alt="MoMo" style="max-width: 40px; height: auto;">
                    <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-ZaloPay.png" alt="ZaloPay" style="max-width: 40px; height: auto;">
                    <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/10/Logo-VNPAY-QR.png" alt="VNPay" style="max-width: 40px; height: auto;">
                </div>
                <p>Chứng Nhận Bộ Công Thương</p>
                <img src="https://pos.nvncdn.com/b47809-47548/art/artCT/20190711_9MTXY8s2VKUAvSRYYKpweJkb.jpg" alt="Bộ Công Thương" style="max-width: 120px; height: auto; margin: 10px 0;">
            </div>
            <div class="col-3 col-md-6 col-sm-12">
                <h3>Kết Nối Với Chúng Tôi</h3>
                <div class="social-links" style="display: flex; justify-content: flex-start; align-items: center; gap: 10px;">
                    <a href="#"><img src="https://cdn2.fptshop.com.vn/unsafe/Uploads/images/tin-tuc/164679/Originals/facebook-la-gi-1.jpg" alt="Facebook" style="width: 35px; height: 35px; object-fit: cover;"></a>
                    <a href="#"><img src="https://upload.wikimedia.org/wikipedia/commons/e/ef/Youtube_logo.png" alt="YouTube" style="width: 35px; height: 35px; object-fit: contain;"></a>
                    <a href="#"><img src="https://store-images.s-microsoft.com/image/apps.4784.13634052595610511.c45457c9-b4af-46b0-8e61-8d7c0aec3f56.3d483847-81a6-4078-8f83-a35c5c38ee92" alt="TikTok" style="width: 35px; height: 35px; object-fit: contain;"></a>
                </div>
                <p>Tải Ứng Dụng Q-FASHION</p>
                <div class="app-links" style="display: flex; justify-content: flex-start; align-items: center; gap: 10px;">
                    <a href="#"><img src="https://developer.apple.com/assets/elements/badges/download-on-the-app-store.svg" alt="App Store" style="width: 120px; height: auto;"></a>
                    <a href="#"><img src="https://play.google.com/intl/en_us/badges/static/images/badges/en_badge_web_generic.png" alt="Google Play" style="width: 140px; height: auto;"></a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Company Details Section -->
<div class="section company-details">
    <div class="container">
        <h3>CÔNG TY CỔ PHẦN TRUNG TÂM THƯƠNG MẠI Q-FASHION VIỆT NAM</h3>
        <p>Trụ sở chính: 469 Nguyễn Hữu Thọ, Phường Tân Hưng, Quận 7, Thành phố Hồ Chí Minh</p>
        <p>Giờ hoạt động: 8:00 - 21:00</p>
        <p>Hotline: 0901 057 057 | Email: info@qfashion.vn</p>
        <p>Giấy chứng nhận Đăng ký kinh doanh số 0304741634 do Sở Kế hoạch và Đầu tư Thành phố Hồ Chí Minh cấp lần đầu ngày 24/10/2006 và sửa đổi lần thứ 21 ngày 22/08/2023</p>
        <p>© 2023 - Bản quyền của Công ty Cổ phần Trung Tâm Thương Mại Q-FASHION Việt Nam</p>
    </div>
</div>

<!-- Scripts -->
<script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
<script>
  <?php if(isset($_SESSION['message']))
  {
  ?>
    alertify.set('notifier','position', 'top-right');
    alertify.success('<?= $_SESSION['message'] ?>');
  <?php 
    unset($_SESSION['message']);
  }
  ?>
</script>

<!-- FPT.AI Chatbot -->
<script>
    let liveChatBaseUrl = 'https://livechat.fpt.ai/v36/src';
    let LiveChatSocketUrl = 'livechat.fpt.ai:443';
    let FptAppCode = '31c45b4e1c2755587f3714e243df6c9d';
    let FptAppName = 'Q-FASHION Support';

    // Define custom styles
    let CustomStyles = {
        // header
        headerBackground: 'linear-gradient(86.7deg, #3353a2ff 0.85%, #31b7b7ff 98.94%)',
        headerTextColor: '#ffffffff',
        headerLogoEnable: true,
        headerLogoLink: 'https://chatbot-tools.fpt.ai/livechat-builder/img/Icon-fpt-ai.png',
        headerText: 'Hỗ trợ trực tuyến',
        // main
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
        // float button
        floatButtonLogo: 'https://chatbot-tools.fpt.ai/livechat-builder/img/Icon-fpt-ai.png',
        floatButtonTooltip: 'Q-FASHION xin chào',
        floatButtonTooltipEnable: true,
        // start screen
        customerLogo: 'https://chatbot-tools.fpt.ai/livechat-builder/img/bot.png',
        customerWelcomeText: 'Vui lòng nhập tên của bạn',
        customerButtonText: 'Bắt đầu',
        prefixEnable: false,
        prefixType: 'radio',
        prefixOptions: ["Anh","Chị"],
        prefixPlaceholder: 'Danh xưng',
        // custom css
        css: ''
    };

    // Set Configs
    let FptLiveChatConfigs = {
        appName: FptAppName,
        appCode: FptAppCode,
        themes: '',
        styles: CustomStyles
    };

    // Append Script
    let FptLiveChatScript = document.createElement('script');
    FptLiveChatScript.id = 'fpt_ai_livechat_script';
    FptLiveChatScript.src = liveChatBaseUrl + '/static/fptai-livechat.js';
    document.body.appendChild(FptLiveChatScript);

    // Append Stylesheet
    let FptLiveChatStyles = document.createElement('link');
    FptLiveChatStyles.id = 'fpt_ai_livechat_styles';
    FptLiveChatStyles.rel = 'stylesheet';
    FptLiveChatStyles.href = liveChatBaseUrl + '/static/fptai-livechat.css';
    document.body.appendChild(FptLiveChatStyles);

    // Init
    FptLiveChatScript.onload = function () {
        fpt_ai_render_chatbox(FptLiveChatConfigs, liveChatBaseUrl, LiveChatSocketUrl);
    };
</script>

<!-- Custom Scripts -->
<script src="./assets/js/app.js"></script>
<script src="./assets/js/index.js"></script>

<!-- Shortcut buttons -->
<?php if(isset($_SESSION['auth'])) { ?>
    <div class="shortcut-buttons">
        <a href="cart.php" class="shortcut-btn">
            <i class="fas fa-shopping-cart"></i>
            <span class="tooltip">Giỏ hàng của tôi</span>
            <?php
            if(isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
                echo '<span class="cart-count" style="position: absolute; top: -5px; right: -5px; background: #ff4444; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px;">' . count($_SESSION['cart']) . '</span>';
            }
            ?>
        </a>

        <a href="user-profile.php" class="shortcut-btn">
            <i class="fas fa-user"></i>
            <span class="tooltip">Thông tin cá nhân</span>
        </a>

        <a href="my-orders.php" class="shortcut-btn">
            <i class="fas fa-box"></i>
            <span class="tooltip">Đơn hàng của tôi</span>
            <?php
            // Lấy số lượng đơn hàng
            $userId = $_SESSION['auth_user']['id'];
            $orderQuery = "SELECT COUNT(*) as order_count FROM orders WHERE user_id = ?";
            $stmt = mysqli_prepare($con, $orderQuery);
            mysqli_stmt_bind_param($stmt, "i", $userId);
            mysqli_stmt_execute($stmt);
            $orderResult = mysqli_stmt_get_result($stmt);
            $orderCount = mysqli_fetch_assoc($orderResult)['order_count'];
            
            if($orderCount > 0) {
                echo '<span class="order-count" style="position: absolute; top: -5px; right: -5px; background: #ff4444; color: white; border-radius: 50%; width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; font-size: 12px;">' . $orderCount . '</span>';
            }
            ?>
        </a>

        <?php if(!isset($_SESSION['auth'])) { ?>
            <a href="login.php" class="shortcut-btn">
                <i class="fas fa-sign-in-alt"></i>
                <span class="tooltip">Đăng nhập</span>
            </a>
            <a href="register.php" class="shortcut-btn">
                <i class="fas fa-user-plus"></i>
                <span class="tooltip">Đăng ký</span>
            </a>
        <?php } ?>
    </div>

    <style>
        .shortcut-buttons {
            position: fixed;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 10px;
            z-index: 1000;
        }

        .shortcut-btn {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #333;
            text-decoration: none;
            position: relative;
            transition: all 0.3s ease;
        }

        .shortcut-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        .shortcut-btn .tooltip {
            position: absolute;
            right: 60px;
            background: #333;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .shortcut-btn:hover .tooltip {
            opacity: 1;
            visibility: visible;
        }

        .shortcut-btn .tooltip::after {
            content: '';
            position: absolute;
            right: -4px;
            top: 50%;
            transform: translateY(-50%);
            border-left: 4px solid #333;
            border-top: 4px solid transparent;
            border-bottom: 4px solid transparent;
        }

        .shortcut-btn i {
            font-size: 20px;
        }
    </style>
<?php } ?>
<!-- End Shortcut buttons -->
