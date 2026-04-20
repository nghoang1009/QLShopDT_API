<?php
/**
 * Đăng ký tài khoản
 */
session_start();
require_once "../../includes/api_helper.php";

// Nếu đã đăng nhập rồi thì chuyển về trang chủ
if (isset($_SESSION['username'])) {
    header("Location: ../trangchu.php");
    exit();
}

// Xử lý POST trước output
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf()) {
        setFlash('error', 'Phiên làm việc hết hạn. Vui lòng thử lại.');
    } else {
        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm  = $_POST['confirm_password'] ?? '';
        $name     = trim($_POST['name'] ?? '');
        $address  = trim($_POST['address'] ?? '');
        $phone    = trim($_POST['phone'] ?? '');
        
        // Validate
        if (strlen($password) < 6) {
            setFlash('error', 'Mật khẩu phải có ít nhất 6 ký tự');
        } elseif ($password !== $confirm) {
            setFlash('error', 'Mật khẩu xác nhận không khớp');
        } else {
            $registerData = [
                'username' => $username,
                'password' => $password,
                'confirm_password' => $confirm,
                'name' => $name,
                'address' => $address,
                'phone' => $phone
            ];
            
            $registerResponse = callAPI('POST', '/api/auth/register', $registerData);
            
            if (!empty($registerResponse['status'])) {
                setFlash('success', $registerResponse['message'] ?? 'Đăng ký thành công! Vui lòng đăng nhập.');
                header("Location: login.php");
                exit();
            } else {
                setFlash('error', $registerResponse['message'] ?? "Đăng ký thất bại!");
            }
        }
    }
    header("Location: register.php");
    exit();
}

// Flash messages
$error = getFlash('error');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký — PhoneShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/QLShopDT_API/assets/css/main.css">
    <link rel="stylesheet" href="/QLShopDT_API/assets/css/auth.css?v=<?= time() ?>">
</head>
<body class="auth-page">

<div class="auth-wrapper">
    <div class="auth-container auth-register">
        <div class="auth-card">
            <div class="auth-header">
                <a href="/QLShopDT_API/views/trangchu.php" class="auth-logo">PHONE<span>SHOP</span></a>
                <h1>Đăng ký tài khoản</h1>
                <p>Tạo tài khoản để mua sắm dễ dàng hơn</p>
            </div>
            
            <div class="auth-body">
                <?php if ($error): ?>
                    <div class="auth-alert auth-alert-error">
                        <i class="fa fa-exclamation-circle"></i>
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <?= csrf_field() ?>
                    
                    <div class="auth-form-group">
                        <label class="auth-label">
                            Tên đăng nhập <span class="auth-required">*</span>
                        </label>
                        <div class="auth-input-wrapper">
                            <input type="text" name="username" class="auth-input" 
                                   placeholder="Nhập tên đăng nhập" required autofocus>
                            <i class="fa fa-user"></i>
                        </div>
                    </div>
                    
                    <div class="auth-form-row">
                        <div class="auth-form-group">
                            <label class="auth-label">
                                Mật khẩu <span class="auth-required">*</span>
                            </label>
                            <div class="auth-input-wrapper">
                                <input type="password" name="password" id="password" class="auth-input" 
                                       placeholder="Ít nhất 6 ký tự" minlength="6" required>
                                <i class="fa fa-lock"></i>
                            </div>
                        </div>
                        
                        <div class="auth-form-group">
                            <label class="auth-label">
                                Xác nhận <span class="auth-required">*</span>
                            </label>
                            <div class="auth-input-wrapper">
                                <input type="password" name="confirm_password" id="confirm_password" class="auth-input" 
                                       placeholder="Nhập lại mật khẩu" required>
                                <i class="fa fa-lock"></i>
                            </div>
                        </div>
                    </div>
                    
                    <div class="auth-form-group">
                        <label class="auth-label">
                            Họ và tên <span class="auth-required">*</span>
                        </label>
                        <div class="auth-input-wrapper">
                            <input type="text" name="name" class="auth-input" 
                                   placeholder="Nhập họ và tên đầy đủ" required>
                            <i class="fa fa-id-card"></i>
                        </div>
                    </div>
                    
                    <div class="auth-form-row">
                        <div class="auth-form-group">
                            <label class="auth-label">Địa chỉ</label>
                            <div class="auth-input-wrapper">
                                <input type="text" name="address" class="auth-input" 
                                       placeholder="Địa chỉ nhận hàng">
                                <i class="fa fa-map-marker-alt"></i>
                            </div>
                        </div>
                        
                        <div class="auth-form-group">
                            <label class="auth-label">Số điện thoại</label>
                            <div class="auth-input-wrapper">
                                <input type="tel" name="phone" class="auth-input" 
                                       placeholder="Số điện thoại liên hệ">
                                <i class="fa fa-phone"></i>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="auth-btn auth-btn-primary">
                        <i class="fa fa-user-plus"></i>
                        Đăng ký
                    </button>
                </form>
                
                <div class="auth-footer">
                    <p>Đã có tài khoản? <a href="login.php">Đăng nhập ngay</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
