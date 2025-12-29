<?php
include("./includes/header.php");

// Database connection
$host = "localhost";
$username = "root";
$password = "";
$database = "fashion_shop_group5";

// Create connection
$con = mysqli_connect($host, $username, $password, $database);

// Check connection
if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!isset($_SESSION['auth_user']['id'])) {
    die("<div class='alert alert-danger text-center'><i class='fas fa-exclamation-circle'></i> Từ chối truy cập</div>");
}

if (!isset($_GET['id'])) {
    header("Location: user-profile.php");
    exit();
}

$order_id = mysqli_real_escape_string($con, $_GET['id']);
$user_id = $_SESSION['auth_user']['id'];

// Lấy thông tin đơn hàng và thông tin người dùng
$order_query = "SELECT o.*, u.name, u.phone, u.address 
                FROM orders o 
                JOIN users u ON o.user_id = u.id 
                WHERE o.id='$order_id' AND o.user_id='$user_id'";
$order_result = mysqli_query($con, $order_query);

if (mysqli_num_rows($order_result) == 0) {
    header("Location: user-profile.php");
    exit();
}

$order = mysqli_fetch_assoc($order_result);

// Lấy chi tiết đơn hàng và thông tin sản phẩm
$order_items_query = "SELECT od.*, od.selling_price as price, 
                            p.name as product_name, p.image as product_image,
                            p.slug as product_slug
                     FROM order_detail od 
                     JOIN products p ON od.product_id = p.id 
                     WHERE od.order_id='$order_id'";
$order_items_result = mysqli_query($con, $order_items_query);

// Tính tổng giá trị đơn hàng
$total_amount = 0;
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng #<?= $order_id ?> - Q-Fashion</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary-color: #e60000;
            --secondary-color: #333333;
            --background-color: #f8f9fa;
            --border-radius: 15px;
            --box-shadow: 0 0 30px rgba(0,0,0,0.1);
            --transition: all 0.3s ease;
        }

        body {
            background-color: var(--background-color);
        }

        .order-details-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .order-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
        }

        .order-header h1 {
            font-size: 1.8rem;
            margin: 0;
            color: var(--secondary-color);
        }

        .order-status {
            padding: 8px 15px;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-0 {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-1 {
            background-color: #cce5ff;
            color: #004085;
        }

        .status-2 {
            background-color: #d4edda;
            color: #155724;
        }

        .status-3 {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .status-4 {
            background-color: #f8d7da;
            color: #842029;
        }

        .order-info {
            background: var(--background-color);
            padding: 20px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
        }

        .order-info h3 {
            color: var(--secondary-color);
            font-size: 1.2rem;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .order-items {
            margin-top: 30px;
        }

        .item-card {
            display: flex;
            align-items: center;
            padding: 15px;
            border: 1px solid #dee2e6;
            border-radius: var(--border-radius);
            margin-bottom: 15px;
            transition: var(--transition);
        }

        .item-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .item-image-wrapper {
            position: relative;
            width: 100px;
            height: 100px;
            margin-right: 20px;
            cursor: pointer;
            overflow: hidden;
            border-radius: 8px;
            transition: var(--transition);
        }

        .item-image-wrapper:hover {
            transform: scale(1.05);
        }

        .item-image-wrapper:hover::after {
            content: '🔍';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 20px;
            color: white;
            text-shadow: 0 0 3px rgba(0,0,0,0.5);
        }

        .item-image-wrapper:hover .item-image {
            filter: brightness(0.8);
        }

        .item-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: var(--transition);
        }

        .item-details {
            flex-grow: 1;
        }

        .item-details h4 {
            margin: 0 0 10px 0;
            font-size: 1.1rem;
            color: var(--secondary-color);
        }

        .item-price {
            font-weight: 600;
            color: var(--primary-color);
            margin: 5px 0;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 10px 20px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: var(--border-radius);
            transition: var(--transition);
            border: none;
        }

        .back-btn:hover {
            background-color: #cc0000;
            color: white;
            text-decoration: none;
            transform: translateY(-2px);
        }

        .total-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 2px solid #f0f0f0;
            text-align: right;
        }

        .total-amount {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        /* Modal styles */
        .image-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.9);
            z-index: 1000;
            overflow: auto;
            animation: fadeIn 0.3s ease;
        }

        .modal-content {
            margin: auto;
            display: block;
            max-width: 80%;
            max-height: 80vh;
            margin-top: 50px;
            border: none;
            box-shadow: none;
            background: none;
        }

        .modal-image {
            width: 100%;
            height: auto;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.3);
        }

        .close-modal {
            position: absolute;
            top: 15px;
            right: 35px;
            color: #f1f1f1;
            font-size: 40px;
            font-weight: bold;
            cursor: pointer;
            transition: var(--transition);
        }

        .close-modal:hover {
            color: var(--primary-color);
            text-decoration: none;
        }

        .modal-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            padding: 16px;
            color: white;
            font-weight: bold;
            font-size: 24px;
            cursor: pointer;
            background: rgba(0,0,0,0.5);
            border-radius: 50%;
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .modal-nav:hover {
            background: rgba(0,0,0,0.8);
            color: var(--primary-color);
        }

        .prev {
            left: 20px;
        }

        .next {
            right: 20px;
        }

        @keyframes fadeIn {
            from {opacity: 0}
            to {opacity: 1}
        }

        @media (max-width: 768px) {
            .order-details-container {
                margin: 20px;
                padding: 20px;
            }

            .order-header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }

            .item-card {
                flex-direction: column;
                text-align: center;
            }

            .item-image-wrapper {
                margin: 0 0 15px 0;
            }

            .modal-content {
                max-width: 95%;
            }

            .modal-nav {
                padding: 12px;
                font-size: 20px;
            }

            .close-modal {
                right: 20px;
            }
        }

        .product-link {
            color: var(--secondary-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .product-link:hover {
            color: var(--primary-color);
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="container order-details-container">
        <div class="order-header">
            <h1>Chi tiết đơn hàng #<?= $order_id ?></h1>
            <?php
            $status_text = '';
            $status_icon = '';
            switch($order['status']) {
                case 0:
                    $status_text = 'Chờ xử lý';
                    $status_icon = 'clock';
                    break;
                case 1:
                    $status_text = 'Đã xác nhận';
                    $status_icon = 'check-circle';
                    break;
                case 2:
                    $status_text = 'Đang giao hàng';
                    $status_icon = 'truck';
                    break;
                case 3:
                    $status_text = 'Đã giao hàng';
                    $status_icon = 'check-double';
                    break;
                case 4:
                    $status_text = 'Đã hủy';
                    $status_icon = 'times-circle';
                    break;
                default:
                    $status_text = 'Không xác định';
                    $status_icon = 'question-circle';
            }
            ?>
            <span class="order-status status-<?= $order['status'] ?>">
                <i class="fas fa-<?= $status_icon ?>"></i>
                <?= $status_text ?>
            </span>
        </div>

        <div class="order-info">
            <div class="row">
                <div class="col-md-6">
                    <h3>
                        <i class="fas fa-info-circle"></i>
                        Thông tin đơn hàng
                    </h3>
                    <p><strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
                    <p><strong>Phương thức thanh toán:</strong> 
                        <?= ($order['payment_method'] ?? 'cod') == 'cod' ? 'Thanh toán khi nhận hàng' : 'VNPay' ?>
                    </p>
                    <p><strong>Mã đơn hàng:</strong> <?= $order['tracking_id'] ?? $order['id'] ?></p>
                </div>
                <div class="col-md-6">
                    <h3>
                        <i class="fas fa-shipping-fast"></i>
                        Thông tin giao hàng
                    </h3>
                    <p><strong>Người nhận:</strong> <?= htmlspecialchars($order['name']) ?></p>
                    <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['phone']) ?></p>
                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['address']) ?></p>
                </div>
            </div>
        </div>

        <div class="order-items">
            <h3>
                <i class="fas fa-shopping-bag"></i>
                Sản phẩm đã đặt
            </h3>
            <?php 
            if(mysqli_num_rows($order_items_result) > 0) {
                while($item = mysqli_fetch_assoc($order_items_result)) { 
                    $item_total = $item['price'] * $item['quantity'];
                    $total_amount += $item_total;
                    
                    // Đường dẫn ảnh sản phẩm
                    $image_path = "uploads/" . $item['product_image'];
                    if (!file_exists($image_path)) {
                        $image_path = "assets/images/default-product.jpg";
                    }
            ?>
                <div class="item-card">
                    <div class="item-image-wrapper" onclick="openImageModal('<?= $item['product_image'] ?>', '<?= $item['product_name'] ?>')">
                        <img src="<?= $image_path ?>" alt="<?= $item['product_name'] ?>" class="item-image">
                    </div>
                    <div class="item-details">
                        <h4>
                            <a href="product-details.php?product=<?= $item['product_slug'] ?>" class="product-link">
                                <?= $item['product_name'] ?>
                            </a>
                        </h4>
                        <p>Số lượng: <?= $item['quantity'] ?></p>
                        <p class="item-price">Đơn giá: <?= number_format($item['price'], 0, ',', '.') ?> VND</p>
                        <p class="item-price">Tổng: <?= number_format($item_total, 0, ',', '.') ?> VND</p>
                    </div>
                </div>
            <?php 
                }
            } else {
                echo "<p class='text-center'>Không tìm thấy sản phẩm nào trong đơn hàng.</p>";
            }
            ?>

            <div class="total-section">
                <p class="total-amount">Tổng giá trị đơn hàng: <?= number_format($total_amount, 0, ',', '.') ?> VND</p>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="user-profile.php" class="back-btn">
                <i class="fas fa-arrow-left"></i>
                Quay lại
            </a>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="image-modal">
        <span class="close-modal" onclick="closeImageModal()">&times;</span>
        <span class="modal-nav prev" onclick="changeImage(-1)">&#10094;</span>
        <span class="modal-nav next" onclick="changeImage(1)">&#10095;</span>
        <div class="modal-content">
            <img id="modalImage" class="modal-image" src="" alt="">
        </div>
    </div>

    <?php include("./includes/footer.php") ?>

    <script>
        let currentImageIndex = 0;
        const images = [];
        const imageNames = [];

        // Collect all images when page loads
        document.addEventListener('DOMContentLoaded', function() {
            const imageElements = document.querySelectorAll('.item-image');
            imageElements.forEach((img, index) => {
                images.push(img.src);
                imageNames.push(img.alt);
            });
        });

        function openImageModal(image, name) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            
            // Find index of clicked image
            currentImageIndex = images.findIndex(img => img.includes(image));
            
            modal.style.display = "block";
            // Use the full path that's already in the img src
            const imgElement = document.querySelector(`img[alt="${name}"]`);
            modalImg.src = imgElement.src;
            modalImg.alt = name;
        }

        function closeImageModal() {
            document.getElementById('imageModal').style.display = "none";
        }

        function changeImage(direction) {
            currentImageIndex += direction;
            
            // Loop around if we reach the end or beginning
            if (currentImageIndex >= images.length) {
                currentImageIndex = 0;
            }
            if (currentImageIndex < 0) {
                currentImageIndex = images.length - 1;
            }

            const modalImg = document.getElementById('modalImage');
            modalImg.src = images[currentImageIndex];
            modalImg.alt = imageNames[currentImageIndex];
        }

        // Close modal when clicking outside the image
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Keyboard navigation
        document.addEventListener('keydown', function(e) {
            if (document.getElementById('imageModal').style.display === "block") {
                if (e.key === "ArrowLeft") {
                    changeImage(-1);
                }
                else if (e.key === "ArrowRight") {
                    changeImage(1);
                }
                else if (e.key === "Escape") {
                    closeImageModal();
                }
            }
        });
    </script>
</body>
</html> 