<?php
session_start();
require_once '../includes/db.php';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    
    if ($email === '') {
        $message = "Vui lòng nhập email!";
    } else {
        // Kiểm tra email có tồn tại không
        $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Tạo token reset password
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Lưu token vào database
            $stmt = $conn->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $user['id'], $token, $expires);
            
            if ($stmt->execute()) {
                // Gửi email reset password (giả lập)
                $reset_link = "http://localhost/fashion_shop/auth/reset-password.php?token=" . $token;
                $success = true;
                $message = "Link đặt lại mật khẩu đã được gửi đến email của bạn.";
                
                // Thêm nút reset password với style phù hợp
                $message .= "<div class='mt-3 text-center'><a href='$reset_link' class='btn-submit' style='display: inline-block; text-decoration: none; padding: 10px 20px;'>Đặt lại mật khẩu</a></div>";
            } else {
                $message = "Có lỗi xảy ra, vui lòng thử lại sau!";
            }
        } else {
            $message = "Email không tồn tại trong hệ thống!";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên mật khẩu - Q-FASHION</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: url('https://mms.img.susercontent.com/361b3edc5766004fbb9d60e6943959a1@resize_bs700x700') no-repeat center center/cover;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1;
        }

        .container {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }

        .forgot-password-container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-container h1 {
            color: #fff;
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .logo-container p {
            color: #fff;
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .form-title {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }

        .btn-submit {
            background-color: #e60000;
            color: white;
            border: none;
            width: 100%;
            padding: 12px;
            font-size: 1.1rem;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-submit:hover {
            background-color: #cc0000;
        }

        .back-to-login {
            text-align: center;
            margin-top: 20px;
        }

        .back-to-login a {
            color: #e60000;
            text-decoration: none;
        }

        .back-to-login a:hover {
            text-decoration: underline;
        }

        .alert {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <h1>Q-FASHION</h1>
            <p>Thời trang cho mọi người</p>
        </div>
        
        <div class="forgot-password-container">
            <h2 class="form-title">Quên mật khẩu</h2>
            
            <?php if ($message): ?>
                <div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>
            
            <form method="post" action="">
                <div class="mb-4">
                    <label for="email" class="form-label">Email của bạn</label>
                    <input type="email" class="form-control" id="email" name="email" required 
                           placeholder="Nhập email đã đăng ký">
                </div>
                
                <button type="submit" class="btn-submit">Gửi link đặt lại mật khẩu</button>
            </form>
            
            <div class="back-to-login">
                <a href="login.php">Quay lại đăng nhập</a>
            </div>
        </div>
    </div>
</body>
</html> 