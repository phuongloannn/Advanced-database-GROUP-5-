<?php
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Dùng thông tin trong email
$vnp_TmnCode = "9YEK3USJ"; // Terminal ID (Mã website)
$vnp_HashSecret = "KPRK1L633KL7EGSXW0DHYUGW1H9BD7N7"; // Secret Key

// URL thanh toán môi trường Sandbox
$vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";

// URL trả về kết quả sau khi thanh toán
$vnp_Returnurl = "http://localhost/vnpay_php/vnpay_return.php";

// (nếu có cần thì thêm API check trạng thái)
$vnp_apiUrl = "https://sandbox.vnpayment.vn/merchant_webapi/api/transaction";

$startTime = date("YmdHis");
$expire = date('YmdHis', strtotime('+15 minutes', strtotime($startTime)));
?>
