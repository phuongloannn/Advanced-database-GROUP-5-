<?php
session_start();
require_once '../includes/db.php';

$message = '';
$success = false;
$valid_token = false;
$token = $_GET['token'] ?? '';

if (empty($token)) {
    $message = "Token không hợp lệ!";
} else {
    // Kiểm tra token có hợp lệ không
    $stmt = $conn->prepare("SELECT user_id, expires_at FROM password_resets WHERE token = ? AND used = 0");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $reset = $result->fetch_assoc();
        if (strtotime($reset['expires_at']) > time()) {
            $valid_token = true;
        } else {
            $message = "Link đặt lại mật khẩu đã hết hạn!";
        }
    } else {
        $message = "Token không hợp lệ hoặc đã được sử dụng!";
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (strlen($password) < 6) {
        $message = "Mật khẩu phải có ít nhất 6 ký tự!";
    } elseif ($password !== $confirm_password) {
        $message = "Xác nhận mật khẩu không khớp!";
    } else {
        // Cập nhật mật khẩu mới
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $hashed_password, $reset['user_id']);
        
        if ($stmt->execute()) {
            // Đánh dấu token đã được sử dụng
            $conn->query("UPDATE password_resets SET used = 1 WHERE token = '$token'");
            
            $success = true;
            $message = "Đặt lại mật khẩu thành công! Bạn có thể đăng nhập với mật khẩu mới.";
        } else {
            $message = "Có lỗi xảy ra, vui lòng thử lại sau!";
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
    <title>Đặt lại mật khẩu - Q-FASHION</title>
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

        .reset-password-container {
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
        
        <div class="reset-password-container">
            <h2 class="form-title">Đặt lại mật khẩu</h2>
            
            <?php if ($message): ?>
                <div class="alert <?= $success ? 'alert-success' : 'alert-danger' ?>">
                    <?= $message ?>
                </div>
                <?php if ($success): ?>
                    <div class="back-to-login">
                        <a href="login.php">Đăng nhập ngay</a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php if ($valid_token && !$success): ?>
                <form method="post" action="">
                    <div class="mb-3">
                        <label for="password" class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control" id="password" name="password" required 
                               minlength="6" placeholder="Nhập mật khẩu mới">
                    </div>
                    
                    <div class="mb-4">
                        <label for="confirm_password" class="form-label">Xác nhận mật khẩu</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               required minlength="6" placeholder="Nhập lại mật khẩu mới">
                    </div>
                    
                    <button type="submit" class="btn-submit">Đặt lại mật khẩu</button>
                </form>
            <?php endif; ?>
            
            <?php if (!$valid_token && !$success): ?>
                <div class="back-to-login">
                    <a href="forgot-password.php">Yêu cầu link đặt lại mật khẩu mới</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 