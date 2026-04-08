<?php
session_start();

$page_title = 'Đăng ký';
$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/register.css">';

require_once "../includes/api_helper.php";
include "../includes/header.php";

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $name = trim($_POST['name']);
    $address = trim($_POST['address']);
    $phone = trim($_POST['phone']);
    
    $registerData = [
        'username' => $username,
        'password' => $password,
        'confirm_password' => $confirm,
        'name' => $name,
        'address' => $address,
        'phone' => $phone
    ];
    
    $registerResponse = callRegisterAPI($registerData);
    
    if (!empty($registerResponse['status'])) {
        $success = $registerResponse['message'] . " Đang chuyển đến trang đăng nhập...";
        echo "<script>setTimeout(function(){ window.location.href='login.php'; }, 2000);</script>";
    } else {
        $error = $registerResponse['message'] ?? "Đăng ký thất bại!";
    }
}
?>

<div class="register-container">
    <h2><i class="fa fa-user-plus"></i> ĐĂNG KÝ TÀI KHOẢN</h2>
    
    <?php if ($error): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="success-msg"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST" action="">
        <div class="form-group">
            <label><i class="fa fa-user"></i> Tên đăng nhập *</label>
            <input type="text" name="username" required>
        </div>
        
        <div class="form-group">
            <label><i class="fa fa-lock"></i> Mật khẩu *</label>
            <input type="password" name="password" required>
        </div>
        
        <div class="form-group">
            <label><i class="fa fa-lock"></i> Xác nhận mật khẩu *</label>
            <input type="password" name="confirm_password" required>
        </div>
        
        <div class="form-group">
            <label><i class="fa fa-id-card"></i> Họ và tên *</label>
            <input type="text" name="name" required>
        </div>
        
        <div class="form-group">
            <label><i class="fa fa-map-marker-alt"></i> Địa chỉ</label>
            <input type="text" name="address">
        </div>
        
        <div class="form-group">
            <label><i class="fa fa-phone"></i> Số điện thoại</label>
            <input type="tel" name="phone">
        </div>
        
        <button type="submit" class="btn-register">
            <i class="fa fa-user-plus"></i> Đăng ký
        </button>
    </form>
    
    <div class="login-link">
        Đã có tài khoán? <a href="login.php">Đăng nhập</a>
    </div>
</div>

</body>
</html>
