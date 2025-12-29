<?php
include(__DIR__ . "/includes/header.php");
include(__DIR__ . "/config/dbcon.php");

$bestSellingProducts = getBestSelling(8);
$LatestProducts = getLatestProducts(8);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Q-FASHION - Trang chủ</title>
    
    <!-- CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/custom.css" rel="stylesheet">
    <link href="assets/css/header-footer.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/header-functions.js"></script>
</head>
<body>
    <div class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>Sản phẩm bán chạy</h2>
                    <div class="row">
                        <?php
                        if(mysqli_num_rows($bestSellingProducts) > 0) {
                            foreach($bestSellingProducts as $item) {
                                ?>
                                <div class="col-md-3 mb-4">
                                    <a href="product-detail.php?product=<?= $item['slug']; ?>" class="text-decoration-none">
                                        <div class="card product-card">
                                            <div class="card-body">
                                                <img src="<?= $item['image']; ?>" alt="Product Image" class="w-100 mb-3">
                                                <h5 class="text-center"><?= $item['name']; ?></h5>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted"><?= number_format($item['selling_price'], 0, ',', '.'); ?>đ</small>
                                                    <span class="badge rounded-pill bg-danger">Hot</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2>Sản phẩm mới</h2>
                    <div class="row">
                        <?php
                        if(mysqli_num_rows($LatestProducts) > 0) {
                            foreach($LatestProducts as $item) {
                                ?>
                                <div class="col-md-3 mb-4">
                                    <a href="product-detail.php?product=<?= $item['slug']; ?>" class="text-decoration-none">
                                        <div class="card product-card">
                                            <div class="card-body">
                                                <img src="<?= $item['image']; ?>" alt="Product Image" class="w-100 mb-3">
                                                <h5 class="text-center"><?= $item['name']; ?></h5>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted"><?= number_format($item['selling_price'], 0, ',', '.'); ?>đ</small>
                                                    <span class="badge rounded-pill bg-success">New</span>
                                                </div>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include(__DIR__ . "/includes/footer.php"); ?>
</body>
</html> 