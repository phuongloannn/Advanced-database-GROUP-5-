<?php
session_start();
include(__DIR__ . "/../config/dbcon.php"); // db connection should define $con and ENCRYPTION_KEY (recommended)
include(__DIR__ . "/myfunctions.php");
include(__DIR__ . "/security.php"); // include the encryption helpers

function redirect($url, $message) {
    $_SESSION['message'] = $message;
    header('Location: ' . $url);
    exit();
}

/**
 * Helper: validate VN phone (returns boolean)
 */
function isValidVNPhone($phone) {
    if ($phone === null || $phone === '') return false;
    // Accept digits only (no spaces), typical patterns 03x,05x,07x,08x,09x and country code 84 optional
    return preg_match("/^(84|0[3|5|7|8|9])[0-9]{8}$/", $phone);
}

/**
 * Helper: safe fetch associative from mysqli_result
 */
function fetch_assoc_safe($result) {
    return mysqli_fetch_assoc($result);
}

if(isset($_POST['register-btn']))
{
    // Use prepared statement and encrypt phone/address fields
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $cpassword = $_POST['cpassword'] ?? '';

    // Basic validation
    if (empty($name) || empty($email) || empty($password) || empty($cpassword)) {
        redirect("../register.php", "Vui lòng điền đầy đủ thông tin bắt buộc");
    }

    if ($password !== $cpassword) {
        redirect("../register.php", "Mật khẩu không khớp");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirect("../register.php", "Địa chỉ email không hợp lệ");
    }

    // Check if email exists (prepared)
    $check_email_query = "SELECT id FROM users WHERE email = ?";
    $stmt = mysqli_prepare($con, $check_email_query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    $exists = mysqli_stmt_num_rows($stmt) > 0;
    mysqli_stmt_close($stmt);

    if ($exists) {
        redirect("../register.php", "Email đã được sử dụng. Vui lòng sử dụng email khác");
    }

    // Hash password
    $pass_hash = password_hash($password, PASSWORD_DEFAULT);

    // Encrypt sensitive fields (phone, address, street_address, postal_code)
    $enc_phone = encryptData($phone);
    // For registration we may not have address fields; set to null if empty
    $enc_address = null;
    $enc_street = null;
    $enc_postal = null;

    // Insert new user
    $insert_query = "INSERT INTO users (name, email, phone, address, street_address, postal_code, password, role_as, creat_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    $role_as = 0;
    $stmt = mysqli_prepare($con, $insert_query);
    mysqli_stmt_bind_param($stmt, "ssssssii",
        $name,
        $email,
        $enc_phone,
        $enc_address,
        $enc_street,
        $enc_postal,
        $pass_hash,
        $role_as
    );
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Đăng ký tài khoản thành công";
        header('Location: ../login.php');
        exit();
    } else {
        redirect("../register.php", "Đã xảy ra lỗi khi đăng ký");
    }
    mysqli_stmt_close($stmt);

}
else if(isset($_POST['login_btn']))
{
    // Use prepared statement for login SELECT
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        redirect("../login.php", "Vui lòng nhập email và mật khẩu");
    }

    $login_query = "SELECT id, name, email, password, role_as FROM users WHERE email = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $login_query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $verify = password_verify($password, $row['password']);
        if ($verify) {
            $_SESSION['auth'] = true;

            $userid = $row['id'];
            $username = $row['name'];
            $useremail = $row['email'];
            $role_as = $row['role_as'];

            $_SESSION['auth_user'] = [
                'id' => $userid,
                'name' => $username,
                'email' => $useremail
            ];

            $_SESSION['role_as'] = $role_as;
            if($role_as == 1) {
                redirect("../admin/index.php", "Welcome to ADMIN");
            } else {
                redirect("../index.php", "Đăng nhập thành công");
            }
        } else {
            redirect("../login.php", "Mật khẩu không đúng");
        }
    } else {
        redirect("../login.php", "Tài khoản email không tồn tại");
    }

    mysqli_stmt_close($stmt);
}
else if(isset($_POST['update_user_btn']))
{
    // Admin / user update (from profile page)
    // Get current user id from session
    if (!isset($_SESSION['auth_user']['id'])) {
        redirect("../login.php", "Vui lòng đăng nhập");
    }
    $id = $_SESSION['auth_user']['id'];

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $street_address = trim($_POST['street_address'] ?? '');
    $postal_code = trim($_POST['postal_code'] ?? '');
    $password = $_POST['password'] ?? '';
    $cpassword = $_POST['cpassword'] ?? '';

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        redirect("../user-profile.php", "Địa chỉ email không hợp lệ");
    }

    // Validate phone if provided
    if (!empty($phone) && !isValidVNPhone($phone)) {
        redirect("../user-profile.php", "Số điện thoại không hợp lệ");
    }

    // If password empty -> update other fields only
    if (empty($password)) {
        $enc_phone = $phone === '' ? null : encryptData($phone);
        $enc_address = $address === '' ? null : encryptData($address);
        $enc_street = $street_address === '' ? null : encryptData($street_address);
        $enc_postal = $postal_code === '' ? null : encryptData($postal_code);

        $update_query = "UPDATE users SET name = ?, email = ?, phone = ?, address = ?, street_address = ?, postal_code = ?, updated_at = NOW() WHERE id = ?";
        $stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt, "ssssssi",
            $name,
            $email,
            $enc_phone,
            $enc_address,
            $enc_street,
            $enc_postal,
            $id
        );
        if (mysqli_stmt_execute($stmt)) {
            // Update session values
            $_SESSION['auth_user']['name'] = $name;
            $_SESSION['auth_user']['email'] = $email;
            // Do not store decrypted phone in session automatically; if needed decrypt and set
            $_SESSION['message'] = "Cập nhật thông tin thành công";
            header('Location: ../user-profile.php');
            exit();
        } else {
            redirect("../user-profile.php", "Xảy ra lỗi, vui lòng cập nhật lại");
        }
        mysqli_stmt_close($stmt);
    } else {
        // Update including password
        if ($password !== $cpassword) {
            redirect("../user-profile.php", "Mật khẩu không khớp");
        }
        if (strlen($password) < 6) {
            redirect("../user-profile.php", "Mật khẩu phải có ít nhất 6 ký tự!");
        }
        // optionally add stronger rules...
        $p_hash = password_hash($password, PASSWORD_DEFAULT);

        $enc_phone = $phone === '' ? null : encryptData($phone);
        $enc_address = $address === '' ? null : encryptData($address);
        $enc_street = $street_address === '' ? null : encryptData($street_address);
        $enc_postal = $postal_code === '' ? null : encryptData($postal_code);

        $update_query = "UPDATE users SET name = ?, email = ?, phone = ?, address = ?, street_address = ?, postal_code = ?, password = ?, updated_at = NOW() WHERE id = ?";
        $stmt = mysqli_prepare($con, $update_query);
        mysqli_stmt_bind_param($stmt, "sssssssi",
            $name,
            $email,
            $enc_phone,
            $enc_address,
            $enc_street,
            $enc_postal,
            $p_hash,
            $id
        );
        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['auth_user']['name'] = $name;
            $_SESSION['auth_user']['email'] = $email;
            $_SESSION['message'] = "Cập nhật thông tin thành công";
            header('Location: ../user-profile.php');
            exit();
        } else {
            redirect("../user-profile.php", "Xảy ra lỗi, vui lòng cập nhật lại");
        }
        mysqli_stmt_close($stmt);
    }
}
else if(isset($_POST['update_profile_btn']))
{
    // This branch expects user to provide current password for verifying identity
    $userId = intval($_POST['userId'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $current_password = $_POST['current_password'] ?? '';

    if ($userId <= 0) {
        redirect("../update-profile.php", "User invalid");
    }

    // Fetch current password hash
    $check_password = "SELECT password FROM users WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $check_password);
    mysqli_stmt_bind_param($stmt, "i", $userId);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    $user_data = mysqli_fetch_assoc($res);
    mysqli_stmt_close($stmt);

    if (!$user_data || !password_verify($current_password, $user_data['password'])) {
        redirect("../update-profile.php", "Mật khẩu hiện tại không đúng");
    }

    // Check email unique (except this user)
    $check_email = "SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1";
    $stmt = mysqli_prepare($con, $check_email);
    mysqli_stmt_bind_param($stmt, "si", $email, $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_close($stmt);
        redirect("../update-profile.php", "Email đã được sử dụng bởi tài khoản khác");
    }
    mysqli_stmt_close($stmt);

    // Validate phone format
    if (!empty($phone) && !isValidVNPhone($phone)) {
        redirect("../update-profile.php", "Số điện thoại không hợp lệ");
    }

    // Encrypt fields for storage
    $enc_phone = $phone === '' ? null : encryptData($phone);
    $enc_address = $address === '' ? null : encryptData($address);

    $update_query = "UPDATE users SET name = ?, email = ?, phone = ?, address = ?, updated_at = NOW() WHERE id = ?";
    $stmt = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($stmt, "ssssi",
        $name,
        $email,
        $enc_phone,
        $enc_address,
        $userId
    );
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['auth_user']['name'] = $name;
        $_SESSION['auth_user']['email'] = $email;
        // optional: set decrypted phone back to session if you want to show it
        $_SESSION['message'] = "Cập nhật thông tin thành công";
        header('Location: ../user-profile.php');
        exit();
    } else {
        redirect("../update-profile.php", "Có lỗi xảy ra khi cập nhật thông tin");
    }
    mysqli_stmt_close($stmt);
}
else if(isset($_POST['change_password_btn'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_id = $_SESSION['auth_user']['id'] ?? 0;

    if ($user_id <= 0) {
        redirect("../login.php", "Vui lòng đăng nhập");
    }

    // Check current password
    $check_password_query = "SELECT password FROM users WHERE id = ? LIMIT 1";
    $stmt = mysqli_prepare($con, $check_password_query);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $check_password_result = mysqli_stmt_get_result($stmt);
    $user_data = mysqli_fetch_assoc($check_password_result);
    mysqli_stmt_close($stmt);

    if (!password_verify($current_password, $user_data['password'])) {
        redirect("../user-profile.php", "Mật khẩu hiện tại không đúng!");
    }

    if ($new_password !== $confirm_password) {
        redirect("../user-profile.php", "Mật khẩu mới và xác nhận mật khẩu không khớp!");
    }
    if (strlen($new_password) < 6) {
        redirect("../user-profile.php", "Mật khẩu phải có ít nhất 6 ký tự!");
    }
    if (!preg_match("/\d/", $new_password)) {
        redirect("../user-profile.php", "Mật khẩu phải chứa ít nhất 1 số!");
    }
    if (!preg_match("/[!@#$%^&*(),.?\":{}|<>]/", $new_password)) {
        redirect("../user-profile.php", "Mật khẩu phải chứa ít nhất 1 ký tự đặc biệt!");
    }

    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $update_password_query = "UPDATE users SET password = ?, updated_at = NOW() WHERE id = ?";
    $stmt = mysqli_prepare($con, $update_password_query);
    mysqli_stmt_bind_param($stmt, "si", $hashed_password, $user_id);
    if (mysqli_stmt_execute($stmt)) {
        $_SESSION['message'] = "Đổi mật khẩu thành công!";
        header('Location: ../user-profile.php');
        exit();
    } else {
        $_SESSION['message'] = "Đã xảy ra lỗi khi cập nhật mật khẩu!";
        header('Location: ../user-profile.php');
        exit();
    }
    mysqli_stmt_close($stmt);
}
?>
