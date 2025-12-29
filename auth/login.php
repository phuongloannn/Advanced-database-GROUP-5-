<?php
session_start();
require_once '../includes/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $message = "Vui lòng nhập đầy đủ email và mật khẩu!";
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, password, role_as FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $name, $user_email, $hash, $role_as);
            $stmt->fetch();
            if (password_verify($password, $hash)) {
                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $name;
                $_SESSION['email'] = $user_email;
                $_SESSION['role_as'] = $role_as;
                $_SESSION['auth'] = true;
                $_SESSION['auth_user'] = [
                    'name' => $name,
                    'email' => $user_email,
                    'role_as' => $role_as,
                    'id' => $id // nếu có
                ];
                if ($role_as == 1) {
                    header('Location: ../admin/admin.php');
                } elseif ($role_as == 2) {
                    header('Location: ../staff/dashboard.php');
                } else {
                    header('Location: ../index.php'); // Đúng trang index.php cho khách hàng
                }
                exit();
            } else {
                $message = "Mật khẩu không đúng!";
            }
        } else {
            $message = "Email không tồn tại!";
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
    <title>Đăng nhập - Q-FASHION</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

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
            max-width: 1200px;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .logo-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-container h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #fff;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .logo-container p {
            font-size: 1.1rem;
            color: #fff;
            opacity: 0.9;
        }

        .login-container {
            display: flex;
            flex-direction: column;
            width: 100%;
            max-width: 450px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }

        .login-header {
            background-color: #e60000;
            color: white;
            padding: 20px;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .login-body {
            padding: 30px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-control:focus {
            border-color: #e60000;
            outline: none;
            box-shadow: 0 0 0 2px rgba(230, 0, 0, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 12px;
            background-color: #e60000;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-login:hover {
            background-color: #c50000;
        }

        .forgot-password {
            text-align: right;
            margin-top: 10px;
        }

        .forgot-password a {
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .forgot-password a:hover {
            color: #e60000;
            text-decoration: underline;
        }

        .register-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.95rem;
            color: #666;
        }

        .register-link a {
            color: #e60000;
            text-decoration: none;
            font-weight: 500;
        }

        .register-link a:hover {
            text-decoration: underline;
        }

        .error {
            color: #e60000;
            background-color: #ffebee;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 0.95rem;
            animation: shake 0.3s ease-in-out;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-5px);
            }

            75% {
                transform: translateX(5px);
            }
        }

        @media (max-width: 768px) {
            .logo-container h1 {
                font-size: 2rem;
            }

            .login-container {
                max-width: 90%;
            }

            .login-body {
                padding: 20px;
            }
        }

        @media (max-width: 576px) {
            .logo-container h1 {
                font-size: 1.8rem;
            }

            .login-header {
                font-size: 1.3rem;
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="logo-container">
            <h1>Q-FASHION</h1>
            <p>Kênh mua sắm Online của Q-Fashion</p>
        </div>

        <div class="login-container">
            <div class="login-header">
                Đăng nhập
            </div>

            <div class="login-body">
                <?php if ($message): ?>
                    <div class='error'><?= $message ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="email">Email <span style="color: #e60000;">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Nhập email" required>
                    </div>

                    <div class="form-group">
                        <label for="password">Mật khẩu <span style="color: #e60000;">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Nhập mật khẩu" required>
                    </div>

                    <div class="forgot-password">
                        <a href="forgot-password.php">Quên mật khẩu?</a>
                    </div>

                    <button type="submit" class="btn-login">Đăng nhập</button>
                </form>

                <div class="register-link">
                    Quý khách chưa có tài khoản? <a href="register.php">Đăng ký</a>
                </div>
            </div>
        </div>
    </div>
</body>

</html>