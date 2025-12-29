<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['auth'])) {
    $_SESSION['message'] = "Vui lòng đăng nhập để xem đơn hàng!";
    header("Location: login.php");
    exit();
}

include("./includes/header.php");
include('config/dbcon.php');

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$order_id = mysqli_real_escape_string($con, $_GET['id']);
$userId = isset($_SESSION['auth_user']['id']) ? $_SESSION['auth_user']['id'] : 0;

// Fetch order details
$orderQuery = "SELECT o.*, u.name as username, u.email, u.phone, u.address,
              (SELECT SUM(od.selling_price * od.quantity) 
               FROM order_detail od 
               WHERE od.order_id = o.id) as total_price
               FROM orders o 
               LEFT JOIN users u ON o.user_id = u.id 
               WHERE o.id = '$order_id'";

// Nếu không phải admin, chỉ cho xem đơn hàng của chính mình
if (!isset($_SESSION['auth_user']['role_as']) || $_SESSION['auth_user']['role_as'] != 1) {
    $orderQuery .= " AND o.user_id = '$userId'";
}

$orderResult = mysqli_query($con, $orderQuery);

if (mysqli_num_rows($orderResult) < 1) {
    ?>
    <div class="container mt-5">
        <div class="alert alert-danger text-center">
            Không tìm thấy đơn hàng hoặc bạn không có quyền xem đơn hàng này!
        </div>
    </div>
    <?php
    include("./includes/footer.php");
    exit();
}

$orderData = mysqli_fetch_assoc($orderResult);

// Fetch order items
$orderItemsQuery = "SELECT od.*, p.name as product_name, p.image, p.slug,
                   od.selling_price as price, od.quantity
                   FROM order_detail od 
                   JOIN products p ON od.product_id = p.id 
                   WHERE od.order_id = '$order_id'";
$orderItemsResult = mysqli_query($con, $orderItemsQuery);

// Status mapping - ĐÃ SỬA CHO ĐỒNG BỘ
$statusMap = [
    0 => ['text' => 'Chờ xử lý', 'class' => 'warning'],
    1 => ['text' => 'Đang xử lý', 'class' => 'primary'],
    2 => ['text' => 'Đang giao', 'class' => 'info'],
    3 => ['text' => 'Hoàn thành', 'class' => 'success'],
    4 => ['text' => 'Đã hủy', 'class' => 'danger']
];
?>

<div class="container mt-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Chi tiết đơn hàng #<?php echo $orderData['id']; ?></h4>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5>Thông tin khách hàng</h5>
                            <p><strong>Tên:</strong> <?php echo htmlspecialchars($orderData['username']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($orderData['email']); ?></p>
                            <p><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($orderData['phone']); ?></p>
                            <p><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($orderData['address']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Thông tin đơn hàng</h5>
                            <p><strong>Mã đơn hàng:</strong> #<?php echo $orderData['id']; ?></p>
                            <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($orderData['created_at'])); ?></p>
                            <p><strong>Phương thức thanh toán:</strong> <?php echo $orderData['payment_method']; ?></p>
                            <p>
                                <strong>Trạng thái:</strong> 
                                <span class="badge bg-<?php echo $statusMap[$orderData['status']]['class']; ?>">
                                    <?php echo $statusMap[$orderData['status']]['text']; ?>
                                </span>
                            </p>
                        </div>
                    </div>

                    <?php if (isset($_SESSION['auth_user']['role_as']) && $_SESSION['auth_user']['role_as'] == 1) { ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <form action="functions/handle-order.php" method="POST">
                                    <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                                    <div class="input-group">
                                        <select name="order_status" class="form-select">
                                            <?php foreach ($statusMap as $status => $details) { ?>
                                                <option value="<?php echo $status; ?>" <?php echo $orderData['status'] == $status ? 'selected' : ''; ?>>
                                                    <?php echo $details['text']; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <button type="submit" name="update_order_btn" class="btn btn-primary">
                                            <i class="fas fa-save"></i> Cập nhật trạng thái
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Hình ảnh</th>
                                    <th>Giá</th>
                                    <th>Số lượng</th>
                                    <th>Tổng</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total = 0;
                                while ($item = mysqli_fetch_assoc($orderItemsResult)) {
                                    $itemTotal = $item['price'] * $item['quantity'];
                                    $total += $itemTotal;
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                        <td>
                                            <img src="anh_xedap/<?php echo $item['image']; ?>" 
                                                 alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                 style="max-width: 100px;">
                                        </td>
                                        <td><?php echo number_format($item['price'], 0, ',', '.'); ?> VND</td>
                                        <td><?php echo $item['quantity']; ?></td>
                                        <td><?php echo number_format($itemTotal, 0, ',', '.'); ?> VND</td>
                                        <td>
                                            <?php if($orderData['status'] == 3) { ?>
                                                <a href="product-detail.php?slug=<?php echo $item['slug']; ?>#review-section" 
                                                   class="btn btn-sm btn-primary">
                                                    <i class="fas fa-star"></i> Đánh giá
                                                </a>
                                            <?php } ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                <tr>
                                    <td colspan="4" class="text-end"><strong>Tổng cộng:</strong></td>
                                    <td colspan="2"><strong><?php echo number_format($total, 0, ',', '.'); ?> VND</strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        <?php if($orderData['status'] == 0) { ?>
                            <a href="my-orders.php?order_id=<?php echo $orderData['id']; ?>#cancelModal" class="btn btn-danger">
                                <i class="fas fa-times"></i> Hủy đơn hàng
                            </a>
                        <?php } ?>

                        <?php if (isset($_SESSION['auth_user']['role_as']) && $_SESSION['auth_user']['role_as'] == 1) { ?>
                            <a href="admin/admin.php" class="btn btn-primary">
                                <i class="fas fa-tachometer-alt"></i> Quay lại Dashboard
                            </a>
                            <a href="admin/orders/index.php" class="btn btn-secondary">
                                <i class="fas fa-list"></i> Quay lại Danh sách đơn hàng
                            </a>
                        <?php } else { ?>
                            <a href="user-profile.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Quay lại
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Hủy đơn hàng -->
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hủy đơn hàng #<?php echo $orderData['id']; ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="cancelForm">
                <div class="modal-body">
                    <input type="hidden" name="order_id" id="cancel_order_id">
                    <div class="form-group">
                        <label>Lý do hủy đơn <span class="text-danger">*</span></label>
                        <textarea name="cancel_reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="submit" class="btn btn-danger">Xác nhận hủy</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#cancelForm').on('submit', function(e) {
        e.preventDefault();
        
        var formData = {
            order_id: $('#cancel_order_id').val(),
            cancel_reason: $('textarea[name="cancel_reason"]').val()
        };

        $.ajax({
            url: 'functions/handle-order.php',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if(response.status == 200) {
                    Swal.fire({
                        title: 'Thành công!',
                        text: response.message,
                        icon: 'success'
                    }).then((result) => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: 'Lỗi!',
                        text: response.message,
                        icon: 'error'
                    });
                }
                $('#cancelModal').modal('hide');
            },
            error: function() {
                Swal.fire({
                    title: 'Lỗi!',
                    text: 'Có lỗi xảy ra khi hủy đơn hàng',
                    icon: 'error'
                });
                $('#cancelModal').modal('hide');
            }
        });
    });
});

function showCancelForm(orderId) {
    $('#cancel_order_id').val(orderId);
    $('#cancelModal').modal('show');
}
</script>

<?php include("./includes/footer.php"); ?>