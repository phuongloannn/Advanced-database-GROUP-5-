<?php
session_start();
include("./config/dbcon.php");

// Kiểm tra đăng nhập và xử lý form trước khi có bất kỳ output nào
if (!isset($_SESSION['auth'])) {
    $_SESSION['message'] = "Vui lòng đăng nhập để cập nhật thông tin";
    header('Location: auth/login.php');
    exit();
}

$userId = $_SESSION['auth_user']['id'];
$query = "SELECT 
    u.id, 
    u.name, 
    u.email, 
    u.phone, 
    u.avatar,
    u.province_id,
    u.district_id, 
    u.street_address,
    p.name as province_name, 
    d.name as district_name
    FROM users u 
    LEFT JOIN provinces p ON u.province_id = p.id 
    LEFT JOIN districts d ON u.district_id = d.id 
    WHERE u.id='$userId'";

$result = mysqli_query($con, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($con));
}

$userData = mysqli_fetch_assoc($result);
$userName = $userData['name'] ?? '';
$userEmail = $userData['email'] ?? '';
$userPhone = $userData['phone'] ?? '';
$userAvatar = $userData['avatar'] ?? 'assets/images/default-avatar.png';
$userProvinceId = $userData['province_id'] ?? '';
$userDistrictId = $userData['district_id'] ?? '';
$userStreetAddress = $userData['street_address'] ?? '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $current_password = $_POST['current_password'];
    $province_id = mysqli_real_escape_string($con, $_POST['province_id']);
    $district_id = mysqli_real_escape_string($con, $_POST['district_id']);
    $street_address = mysqli_real_escape_string($con, $_POST['street_address']);
    
    // Verify current password
    $check_password = mysqli_query($con, "SELECT id, password FROM users WHERE id='$userId'");
    if (!$check_password) {
        $_SESSION['message'] = "Lỗi truy vấn: " . mysqli_error($con);
        header('Location: update-profile.php');
        exit();
    }
    
    $row = mysqli_fetch_assoc($check_password);
    if (!$row) {
        $_SESSION['message'] = "Không tìm thấy thông tin người dùng";
        header('Location: update-profile.php');
        exit();
    }
    
    if (empty($row['password'])) {
        $_SESSION['message'] = "Lỗi: Mật khẩu trong database trống";
        header('Location: update-profile.php');
        exit();
    }
    
    // Debug password verification
    error_log("User ID: " . $userId);
    error_log("Password verification - Input length: " . strlen($current_password));
    error_log("Password verification - Hash length: " . strlen($row['password']));
    error_log("Password verification - Hash from DB: " . $row['password']);
    
    $is_password_correct = password_verify($current_password, $row['password']);
    error_log("Password verification result: " . ($is_password_correct ? "true" : "false"));
    
    if($is_password_correct) {
        // Handle image upload
        $avatar_path = '';
        if(isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            $allowed = ['jpg', 'jpeg', 'png', 'gif'];
            $filename = $_FILES['avatar']['name'];
            $filetype = pathinfo($filename, PATHINFO_EXTENSION);
            
            if(in_array(strtolower($filetype), $allowed)) {
                $new_filename = time() . '_' . $filename;
                $upload_path = 'uploads/avatars/' . $new_filename;
                
                if(move_uploaded_file($_FILES['avatar']['tmp_name'], $upload_path)) {
                    $avatar_path = $upload_path;
                }
            }
        }
        
        // Update query with avatar if uploaded
        $update_query = "UPDATE users SET 
            name='$name', 
            phone='$phone', 
            email='$email',
            province_id=" . ($province_id ? "'$province_id'" : "NULL") . ",
            district_id=" . ($district_id ? "'$district_id'" : "NULL") . ",
            street_address=" . ($street_address ? "'$street_address'" : "NULL");
            
        if($avatar_path != '') {
            $update_query .= ", avatar='$avatar_path'";
        }
        
        $update_query .= " WHERE id='$userId'";
        
        error_log("Update query: " . $update_query);
        $update_query_run = mysqli_query($con, $update_query);
        
       
        if($update_query_run) {
            $_SESSION['message'] = "Thông tin đã được cập nhật thành công";
            header('Location: user-profile.php');
            exit();
        } else {
            $_SESSION['message'] = "Lỗi khi cập nhật: " . mysqli_error($con);
            header('Location: update-profile.php');
            exit();
        }
    } else {
        $_SESSION['message'] = "Mật khẩu hiện tại không đúng";
        header('Location: update-profile.php');
        exit();
    }
}

// Include header sau khi xử lý logic
include("./includes/header.php");
?>

<style>
    .update-profile-container {
        padding: 30px 0;
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 80vh;
    }

    .profile-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 0 20px rgba(0,0,0,0.1);
        overflow: hidden;
    }

    .profile-header {
        background: linear-gradient(45deg, #2196F3, #3F51B5);
        color: white;
        padding: 20px;
        border-radius: 15px 15px 0 0;
    }

    .avatar-upload {
        position: relative;
        max-width: 180px;
        margin: 20px auto;
    }

    .avatar-preview {
        width: 180px;
        height: 180px;
        position: relative;
        border-radius: 50%;
        overflow: hidden;
        background: #fff;
        padding: 5px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }

    .avatar-preview img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid white;
    }

    .avatar-upload .avatar-edit {
        position: absolute;
        right: 10px;
        bottom: 5px;
        z-index: 1;
    }

    .avatar-upload .avatar-edit input {
        display: none;
    }

    .avatar-upload .avatar-edit label {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: #2196F3;
        border: 2px solid white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        cursor: pointer;
        color: white;
        transition: all 0.3s ease;
    }

    .avatar-upload .avatar-edit label:hover {
        background: #1976D2;
        transform: scale(1.1);
    }

    .form-section {
        background: white;
        padding: 30px;
        border-radius: 15px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        color: #2c3e50;
        font-weight: 500;
        margin-bottom: 8px;
    }

    .form-control {
        border-radius: 8px;
        padding: 12px;
        border: 1px solid #ced4da;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #2196F3;
        box-shadow: 0 0 0 0.2rem rgba(33, 150, 243, 0.25);
    }

    .form-select {
        border-radius: 8px;
        height: 45px;
    }

    .btn-update-profile {
        background: linear-gradient(45deg, #2196F3, #3F51B5);
        border: none;
        border-radius: 8px;
        padding: 12px 25px;
        font-weight: 500;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-update-profile:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(33, 150, 243, 0.3);
    }

    .btn-cancel {
        background: #6c757d;
        border: none;
        border-radius: 8px;
        padding: 12px 25px;
        font-weight: 500;
        color: white;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }

    .section-title {
        color: #2c3e50;
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #eee;
    }

    .alert {
        border-radius: 10px;
        border: none;
    }

    @media (max-width: 768px) {
        .form-section {
            padding: 20px;
        }
        
        .avatar-preview {
            width: 150px;
            height: 150px;
        }
    }
</style>

<div class="py-3 bg-primary">
    <div class="container">
        <h6 class="text-white">
            <a href="index.php" class="text-white text-decoration-none">Trang chủ</a> / 
            <a href="user-profile.php" class="text-white text-decoration-none">Thông tin cá nhân</a> / 
            <span class="text-white-50">Cập nhật thông tin</span>
        </h6>
    </div>
</div>

<div class="update-profile-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="profile-card">
                    <div class="profile-header">
                        <h4 class="mb-0"><i class="fas fa-user-edit me-2"></i>Cập nhật thông tin cá nhân</h4>
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

                        <form action="" method="POST" enctype="multipart/form-data">
                            <input type="hidden" name="userId" value="<?= $userId ?>">
                            
                            <div class="text-center">
                                <div class="avatar-upload">
                                    <div class="avatar-preview">
                                        <img src="<?= $userAvatar ?>" 
                                             id="avatarPreview" alt="Profile Picture">
                                    </div>
                                    <div class="avatar-edit">
                                        <input type="file" id="avatar" name="avatar" accept="image/*" onchange="previewImage(this)">
                                        <label for="avatar">
                                            <i class="fas fa-camera"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-section mt-4">
                                <h5 class="section-title">Thông tin cơ bản</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Họ và tên</label>
                                            <input type="text" name="name" class="form-control" 
                                                   value="<?= htmlspecialchars($userName) ?>" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" 
                                                value="<?php echo htmlspecialchars($userEmail); ?>" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Số điện thoại</label>
                                    <input type="tel" name="phone" class="form-control" 
                                        value="<?php echo htmlspecialchars($userPhone); ?>" required>
                                </div>
                            </div>

                            <div class="form-section mt-4">
                                <h5 class="section-title">Địa chỉ</h5>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Tỉnh/Thành phố</label>
                                            <select name="province_id" id="province" class="form-select" required>
                                                <option value="">Chọn Tỉnh/Thành phố</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Quận/Huyện</label>
                                            <select name="district_id" id="district" class="form-select" required>
                                                <option value="">Chọn Quận/Huyện</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="form-label">Địa chỉ chi tiết</label>
                                    <textarea name="street_address" class="form-control" rows="3" 
                                              placeholder="Số nhà, tên đường..." required><?= htmlspecialchars($userData['street_address'] ?? '') ?></textarea>
                                </div>
                            </div>

                            <div class="form-section mt-4">
                                <h5 class="section-title">Xác nhận thay đổi</h5>
                                <div class="form-group">
                                    <label class="form-label">Mật khẩu hiện tại</label>
                                    <input type="password" name="current_password" class="form-control" 
                                           placeholder="Nhập mật khẩu hiện tại để xác nhận thay đổi" required>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end gap-2 mt-4">
                                <a href="user-profile.php" class="btn btn-cancel">
                                    <i class="fas fa-times me-2"></i>Hủy
                                </a>
                                <button type="submit" name="update_profile_btn" class="btn btn-update-profile">
                                    <i class="fas fa-save me-2"></i>Cập nhật thông tin
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Load provinces on page load
$(document).ready(function() {
    console.log('Document ready, loading provinces...');
    loadProvinces();
    
    // Set initial values if they exist
    const currentProvince = '<?= $userData['province_id'] ?? '' ?>';
    const currentDistrict = '<?= $userData['district_id'] ?? '' ?>';
    
    console.log('Current values:', { currentProvince, currentDistrict });
    
    if(currentProvince) {
        setTimeout(() => {
            $('#province').val(currentProvince).trigger('change');
            if(currentDistrict) {
                setTimeout(() => {
                    $('#district').val(currentDistrict);
                }, 500);
            }
        }, 500);
    }
});

function loadProvinces() {
    console.log('Loading provinces...');
    $.ajax({
        url: 'functions/get-locations.php',
        type: 'GET',
        data: { type: 'provinces' },
        dataType: 'json',
        success: function(response) {
            console.log('Provinces response:', response);
            if(response.status === 'success') {
                let options = '<option value="">Chọn Tỉnh/Thành phố</option>';
                response.data.forEach(function(province) {
                    options += `<option value="${province.id}">${province.name}</option>`;
                });
                $('#province').html(options);
            } else {
                console.error('Error loading provinces:', response.message);
                alert(response.message || 'Không thể tải danh sách tỉnh thành');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX error:', error);
            alert('Có lỗi khi tải danh sách tỉnh thành');
        }
    });
}

$('#province').change(function() {
    const provinceId = $(this).val();
    console.log('Province changed:', provinceId);
    $('#district').html('<option value="">Chọn Quận/Huyện</option>');
    
    if(provinceId) {
        $.ajax({
            url: 'functions/get-locations.php',
            type: 'GET',
            data: { 
                type: 'districts',
                province_id: provinceId 
            },
            dataType: 'json',
            success: function(response) {
                console.log('Districts response:', response);
                if(response.status === 'success') {
                    let options = '<option value="">Chọn Quận/Huyện</option>';
                    response.data.forEach(function(district) {
                        options += `<option value="${district.id}">${district.name}</option>`;
                    });
                    $('#district').html(options);
                } else {
                    console.error('Error loading districts:', response.message);
                    alert(response.message || 'Không thể tải danh sách quận huyện');
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX error:', error);
                alert('Có lỗi khi tải danh sách quận huyện');
            }
        });
    }
});
</script>

<?php include('includes/footer.php'); ?> 