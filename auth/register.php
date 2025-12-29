<?php
require_once '../includes/db.php';
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $phone    = trim($_POST['phone'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm'] ?? '');

    if ($name === '' || $email === '' || $phone === '' || $password === '' || $confirm === '') {
        $message = "Vui lòng nhập đầy đủ thông tin.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Email không hợp lệ.";
    } elseif (!preg_match('/^[0-9]{10,11}$/', $phone)) {
        $message = "Số điện thoại không hợp lệ (10-11 số).";
    } elseif ($password !== $confirm) {
        $message = "Mật khẩu xác nhận không khớp.";
    } elseif (strlen($password) <= 6) {
        $message = "Mật khẩu phải nhiều hơn 6 ký tự.";
    } else {
        // Kiểm tra email đã tồn tại chưa
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $message = "Email đã được đăng ký.";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $role_as = 0; // Mặc định là user
            $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role_as) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $name, $email, $phone, $hash, $role_as);
            if ($stmt->execute()) {
                session_start();
                $_SESSION['username'] = $name;
                $_SESSION['email'] = $email;
                $_SESSION['role_as'] = 0;
                header('Location: ../index.php');
                exit();
            } else {
                $message = "Đăng ký thất bại. Vui lòng thử lại.";
            }
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
    <title>Đăng ký - Q-FASHION</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/themes/default.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
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
            max-width: 900px;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .logo-container {
            text-align: left;
            color: #fff;
            padding: 20px;
        }

        .logo-container h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .logo-container p {
            font-size: 1.1rem;
            opacity: 0.9;
        }

        .register-container {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            padding: 30px;
            width: 450px;
            display: flex;
            flex-direction: column;
        }

        .register-header {
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
            color: #e60000;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }

        .form-group i {
            position: absolute;
            left: 15px;
            top: 45px;
            color: #666;
        }

        .form-control {
            width: 100%;
            padding: 12px 15px 12px 40px;
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

        .btn-register {
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

        .btn-register:hover {
            background-color: #c50000;
        }

        .login-link {
            text-align: center;
            margin-top: 20px;
            font-size: 0.95rem;
            color: #666;
        }

        .login-link a {
            color: #e60000;
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
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
            .container {
                flex-direction: column;
                max-width: 90%;
            }

            .logo-container {
                text-align: center;
                margin-bottom: 20px;
            }

            .register-container {
                width: 100%;
                margin-top: 20px;
            }
        }

        @media (max-width: 576px) {
            .logo-container h1 {
                font-size: 1.8rem;
            }

            .register-header {
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
            <p>Kênh mua sắm Online của Q-FASHION</p>
        </div>
        <div class="register-container">
            <div class="register-header">
                Đăng ký
            </div>
            <div class="register-body">
                <?php if ($message): ?>
                    <div class='error'><?= $message ?></div>
                <?php endif; ?>
                <form action="" method="POST" id="register-account" autocomplete="off">
                    <div class="form-group">
                        <label for="name">Họ tên <span style="color: #e60000;">*</span></label>
                        <i class="fas fa-user"></i>
                        <input type="text" required name="name" class="form-control" id="name" placeholder="Nhập họ tên của bạn" value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email <span style="color: #e60000;">*</span></label>
                        <i class="fas fa-envelope"></i>
                        <input type="email" required name="email" class="form-control" id="InputEmail" placeholder="Nhập Email của bạn" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="phone">SĐT <span style="color: #e60000;">*</span></label>
                        <i class="fas fa-phone"></i>
                        <input type="number" required name="phone" class="form-control" id="InputPhone" placeholder="Nhập số điện thoại của bạn" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="password">Mật khẩu <span style="color: #e60000;">*</span></label>
                        <i class="fas fa-lock"></i>
                        <input type="password" required name="password" id="InputPassword1" class="form-control" placeholder="Nhập mật khẩu">
                    </div>
                    <div class="form-group">
                        <label for="confirm">Xác nhận mật khẩu <span style="color: #e60000;">*</span></label>
                        <i class="fas fa-lock"></i>
                        <input type="password" required name="confirm" id="InputPassword2" class="form-control" placeholder="Xác nhận mật khẩu">
                    </div>
                    <button type="submit" class="btn-register">Đăng Ký</button>
                </form>
                <div class="login-link">
                    Quý khách đã có tài khoản? <a href="login.php">Đăng nhập</a>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Validate email
        const validateEmail = (email) => {
            return String(email)
                .toLowerCase()
                .match(
                    /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                );
        };
        // Validate phone
        const validatePhone = (phone) => {
            return String(phone).match(/^[0-9]{10,11}$/);
        };
        // Validate form
        document.getElementById("register-account").addEventListener('submit', function(e) {
            let email = document.getElementById("InputEmail").value;
            let phone = document.getElementById("InputPhone").value;
            let password1 = document.getElementById("InputPassword1").value;
            let password2 = document.getElementById("InputPassword2").value;

            if (!validateEmail(email)) {
                alertify.set('notifier', 'position', 'top-right');
                alertify.success('Lỗi email không hợp lệ');
                e.preventDefault();
            } else if (!validatePhone(phone)) {
                alertify.set('notifier', 'position', 'top-right');
                alertify.success('Số điện thoại không hợp lệ (10-11 số)');
                e.preventDefault();
            } else if (password1 !== password2) {
                alertify.set('notifier', 'position', 'top-right');
                alertify.success('Mật khẩu chưa khớp');
                e.preventDefault();
            } else if (password1.length <= 6) {
                alertify.set('notifier', 'position', 'top-right');
                alertify.success('Vui lòng nhập mật khẩu nhiều hơn 6 ký tự');
                e.preventDefault();
            }
        });
        // Hiệu ứng khi focus input
        document.querySelectorAll('.form-control').forEach(input => {
            input.addEventListener('focus', function() {
                this.style.borderColor = '#e60000';
            });
            input.addEventListener('blur', function() {
                this.style.borderColor = '#ddd';
            });
        });
    </script>
</body>

</html>