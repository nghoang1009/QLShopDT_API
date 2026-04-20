<?php
/**
 * Thêm Nhân viên mới
 */
session_start();
require_once "../../includes/api_helper.php";

requireLogin();
requireRole([1]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tennv    = trim($_POST['txt_tennv']    ?? '');
    $sdt      = trim($_POST['txt_sdt']      ?? '');
    $ns       = trim($_POST['date_ns']      ?? '');
    $diachi   = trim($_POST['txt_diachi']   ?? '');
    $username = trim($_POST['txt_username'] ?? '');
    $password = trim($_POST['txt_password'] ?? '');

    if (empty($tennv)) {
        setFlash('error', 'Tên nhân viên không được để trống');
        header("Location: nhanvien_add.php");
        exit();
    }
    if (empty($username) || empty($password)) {
        setFlash('error', 'Tên đăng nhập và mật khẩu không được để trống');
        header("Location: nhanvien_add.php");
        exit();
    }

    $result = callAPI('POST', '/api/nhanvien', [
        'tennv'    => $tennv,
        'sdt'      => $sdt,
        'ns'       => $ns,
        'diachi'   => $diachi,
        'username' => $username,
        'password' => $password,
    ]);

    if ($result && $result['status']) {
        setFlash('success', 'Thêm nhân viên "' . $tennv . '" thành công');
        header("Location: nhanvien.php");
        exit();
    }
    setFlash('error', $result['message'] ?? 'Thêm nhân viên thất bại');
    header("Location: nhanvien_add.php");
    exit();
}

$error = getFlash('error');

$page_title = 'Thêm Nhân viên';
$active_nav = 'nhanvien';
$extra_css  = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/nhanvien.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";
?>

<main class="container">
    <h1>THÊM NHÂN VIÊN</h1>

    <?php if ($error): ?>
        <div class="dm-alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="nhanvien_add.php" class="dm-form">

        <div class="dm-form-group">
            <label for="txt_tennv" class="dm-label">
                Tên nhân viên <span class="dm-required">*</span>
            </label>
            <input type="text" id="txt_tennv" name="txt_tennv"
                   placeholder="Nhập tên nhân viên"
                   class="dm-input" required>
        </div>

        <div class="dm-form-group">
            <label for="txt_diachi" class="dm-label">Địa chỉ</label>
            <input type="text" id="txt_diachi" name="txt_diachi"
                   placeholder="Nhập địa chỉ"
                   class="dm-input">
        </div>

        <div class="dm-form-group">
            <label for="txt_sdt" class="dm-label">Số điện thoại</label>
            <input type="text" id="txt_sdt" name="txt_sdt"
                   placeholder="Nhập số điện thoại"
                   class="dm-input">
        </div>

        <div class="dm-form-group">
            <label for="date_ns" class="dm-label">Ngày sinh</label>
            <input type="date" id="date_ns" name="date_ns" class="dm-input">
        </div>

        <hr style="margin: 16px 0; border-color: #eee;">
        <p style="font-size: 0.85rem; color: #666; margin-bottom: 8px;">Thông tin tài khoản đăng nhập</p>

        <div class="dm-form-group">
            <label for="txt_username" class="dm-label">
                Tên đăng nhập <span class="dm-required">*</span>
            </label>
            <input type="text" id="txt_username" name="txt_username"
                   placeholder="Nhập tên đăng nhập"
                   class="dm-input" required>
        </div>

        <div class="dm-form-group">
            <label for="txt_password" class="dm-label">
                Mật khẩu <span class="dm-required">*</span>
            </label>
            <input type="password" id="txt_password" name="txt_password"
                   placeholder="Nhập mật khẩu"
                   class="dm-input" required>
        </div>

        <div class="dm-form-actions">
            <button type="submit" class="dm-btn dm-btn-primary">Thêm nhân viên</button>
            <a href="nhanvien.php" class="dm-btn dm-btn-default">Quay lại</a>
        </div>
    </form>
</main>

<?php include "../../includes/footer.php"; ?>
