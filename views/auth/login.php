<?php
/**
 * Đăng nhập
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

        $authResponse = callAPI('POST', '/api/auth/login', [
            'username' => $username,
            'password' => $password
        ]);

        if (!empty($authResponse['status'])) {
            $_SESSION['username'] = $authResponse['user']['tentk'];
            $_SESSION['userid']   = $authResponse['user']['matk'];
            $_SESSION['role']     = $authResponse['user']['role'];
            
            regenerate_csrf();
            
            header("Location: ../trangchu.php");
            exit();
        } else {
            setFlash('error', $authResponse['message'] ?? "Tên đăng nhập hoặc mật khẩu không đúng!");
        }
    }
    header("Location: login.php");
    exit();
}

// Flash messages
$error = getFlash('error');
$success = getFlash('success');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập — PhoneShop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/QLShopDT_API/assets/css/main.css">
    <link rel="stylesheet" href="/QLShopDT_API/assets/css/auth.css?v=<?= time() ?>">
</head>
<body class="auth-page">

<div class="auth-wrapper">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <a href="/QLShopDT_API/views/trangchu.php" class="auth-logo">PHONE<span>SHOP</span></a>
                <h1>Đăng nhập</h1>
                <p>Chào mừng bạn quay trở lại!</p>
            </div>
            
            <div class="auth-body">
                <?php if ($success): ?>
                    <div class="auth-alert auth-alert-success">
                        <i class="fa fa-check-circle"></i>
                        <?= e($success) ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="auth-alert auth-alert-error">
                        <i class="fa fa-exclamation-circle"></i>
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <?= csrf_field() ?>
                    
                    <div class="auth-form-group">
                        <label class="auth-label">Tên đăng nhập</label>
                        <div class="auth-input-wrapper">
                            <input type="text" name="username" class="auth-input" 
                                   placeholder="Nhập tên đăng nhập" required autofocus>
                            <i class="fa fa-user"></i>
                        </div>
                    </div>
                    
                    <div class="auth-form-group">
                        <label class="auth-label">Mật khẩu</label>
                        <div class="auth-input-wrapper">
                            <input type="password" name="password" id="password" class="auth-input" 
                                   placeholder="Nhập mật khẩu" required>
                            <i class="fa fa-lock"></i>
                            <button type="button" class="auth-password-toggle" onclick="togglePassword('password', this)">
                                <i class="fa fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <button type="submit" class="auth-btn auth-btn-primary">
                        <i class="fa fa-sign-in-alt"></i>
                        Đăng nhập
                    </button>
                </form>
                
                <div class="auth-footer">
                    <p>Chưa có tài khoản? <a href="register.php">Đăng ký ngay</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function togglePassword(inputId, btn) {
    const input = document.getElementById(inputId);
    const icon = btn.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>
