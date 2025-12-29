<?php
session_start();
include('./config/dbcon.php');

if (!isset($_SESSION['auth'])) {
    $_SESSION['message'] = "Vui lòng đăng nhập để thay đổi mật khẩu";
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['auth_user']['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $old_password = $_POST['old_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Kiểm tra mật khẩu cũ
    $check_password = mysqli_query($con, "SELECT password FROM users WHERE id='$userId'");
    $user = mysqli_fetch_assoc($check_password);
    
    if (!password_verify($old_password, $user['password'])) {
        $_SESSION['message'] = "Mật khẩu hiện tại không đúng";
        header('Location: change-password.php');
        exit();
    }
    
    // Kiểm tra điều kiện mật khẩu mới
    if (strlen($new_password) < 8) {
        $_SESSION['message'] = "Mật khẩu mới phải có ít nhất 8 ký tự";
        header('Location: change-password.php');
        exit();
    }
    
    if (!preg_match("/[A-Z]/", $new_password)) {
        $_SESSION['message'] = "Mật khẩu mới phải chứa ít nhất 1 chữ in hoa";
        header('Location: change-password.php');
        exit();
    }
    
    if (!preg_match("/[a-z]/", $new_password)) {
        $_SESSION['message'] = "Mật khẩu mới phải chứa ít nhất 1 chữ thường";
        header('Location: change-password.php');
        exit();
    }
    
    if (!preg_match("/[0-9]/", $new_password)) {
        $_SESSION['message'] = "Mật khẩu mới phải chứa ít nhất 1 số";
        header('Location: change-password.php');
        exit();
    }
    
    if (!preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $new_password)) {
        $_SESSION['message'] = "Mật khẩu mới phải chứa ít nhất 1 ký tự đặc biệt";
        header('Location: change-password.php');
        exit();
    }
    
    if ($new_password !== $confirm_password) {
        $_SESSION['message'] = "Mật khẩu mới không khớp";
        header('Location: change-password.php');
        exit();
    }
    
    // Cập nhật mật khẩu mới
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $update_query = "UPDATE users SET password='$hashed_password' WHERE id='$userId'";
    
    if (mysqli_query($con, $update_query)) {
        $_SESSION['message'] = "Đổi mật khẩu thành công";
        header('Location: user-profile.php');
        exit();
    } else {
        $_SESSION['message'] = "Đã xảy ra lỗi khi cập nhật mật khẩu";
        header('Location: change-password.php');
        exit();
    }
}

include('includes/header.php');
?>

<style>
    .password-container {
        padding: 30px 0;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 80vh;
    }
    
    .password-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
    }
    
    .password-header {
        background: linear-gradient(45deg, #2196F3, #3F51B5);
        color: white;
        border-radius: 15px 15px 0 0;
        padding: 20px;
    }
    
    .password-requirements {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .requirement-item {
        color: #6c757d;
        margin-bottom: 5px;
        font-size: 14px;
    }
    
    .requirement-item i {
        margin-right: 5px;
    }
    
    .form-control {
        border-radius: 8px;
        padding: 12px;
        border: 1px solid #ced4da;
    }
    
    .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
        border-color: #2196F3;
    }
    
    .btn-change-password {
        background: linear-gradient(45deg, #2196F3, #3F51B5);
        border: none;
        border-radius: 8px;
        padding: 12px 25px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-change-password:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(33, 150, 243, 0.3);
    }
    
    .password-strength {
        height: 5px;
        border-radius: 5px;
        margin-top: 5px;
        transition: all 0.3s ease;
    }
</style>

<div class="py-3 bg-primary">
    <div class="container">
        <h6 class="text-white">
            <a href="index.php" class="text-white text-decoration-none">Trang chủ</a> / 
            <a href="user-profile.php" class="text-white text-decoration-none">Thông tin cá nhân</a> / 
            <span class="text-white-50">Đổi mật khẩu</span>
        </h6>
    </div>
</div>

<div class="password-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="password-card card">
                    <div class="password-header">
                        <h4 class="mb-0"><i class="fas fa-key me-2"></i>Đổi mật khẩu</h4>
                    </div>
                    <div class="card-body p-4">
                        <?php
                        if(isset($_SESSION['message'])) {
                            ?>
                            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                                <?= $_SESSION['message']; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            <?php
                            unset($_SESSION['message']);
                        }
                        ?>
                        
                        <div class="password-requirements mb-4">
                            <h6 class="mb-3">Yêu cầu mật khẩu:</h6>
                            <div class="requirement-item">
                                <i class="fas fa-check-circle"></i> Ít nhất 8 ký tự
                            </div>
                            <div class="requirement-item">
                                <i class="fas fa-check-circle"></i> Ít nhất 1 chữ in hoa
                            </div>
                            <div class="requirement-item">
                                <i class="fas fa-check-circle"></i> Ít nhất 1 chữ thường
                            </div>
                            <div class="requirement-item">
                                <i class="fas fa-check-circle"></i> Ít nhất 1 số
                            </div>
                            <div class="requirement-item">
                                <i class="fas fa-check-circle"></i> Ít nhất 1 ký tự đặc biệt (!@#$%^&*(),.?":{}|<>)
                            </div>
                        </div>

                        <form action="" method="POST">
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu hiện tại</label>
                                <input type="password" name="old_password" class="form-control" required>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Mật khẩu mới</label>
                                <input type="password" name="new_password" id="new_password" class="form-control" required>
                                <div class="password-strength" id="password-strength"></div>
                            </div>
                            
                            <div class="mb-4">
                                <label class="form-label">Xác nhận mật khẩu mới</label>
                                <input type="password" name="confirm_password" class="form-control" required>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-change-password text-white">
                                    <i class="fas fa-save me-2"></i>Cập nhật mật khẩu
                                </button>
                                <a href="user-profile.php" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('new_password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.getElementById('password-strength');
    let strength = 0;
    
    // Kiểm tra độ mạnh của mật khẩu
    if(password.length >= 8) strength++;
    if(password.match(/[A-Z]/)) strength++;
    if(password.match(/[a-z]/)) strength++;
    if(password.match(/[0-9]/)) strength++;
    if(password.match(/[!@#$%^&*(),.?":{}|<>]/)) strength++;
    
    // Cập nhật thanh độ mạnh
    switch(strength) {
        case 0:
            strengthBar.style.width = '0%';
            strengthBar.style.backgroundColor = '#dc3545';
            break;
        case 1:
            strengthBar.style.width = '20%';
            strengthBar.style.backgroundColor = '#dc3545';
            break;
        case 2:
            strengthBar.style.width = '40%';
            strengthBar.style.backgroundColor = '#ffc107';
            break;
        case 3:
            strengthBar.style.width = '60%';
            strengthBar.style.backgroundColor = '#ffc107';
            break;
        case 4:
            strengthBar.style.width = '80%';
            strengthBar.style.backgroundColor = '#28a745';
            break;
        case 5:
            strengthBar.style.width = '100%';
            strengthBar.style.backgroundColor = '#28a745';
            break;
    }
});
</script>

<?php include('includes/footer.php'); ?> 