<?php
/**
 * Thêm Khách hàng mới
 */
session_start();
require_once "../../includes/api_helper.php";

requireLogin();
requireRole([1, 2]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenkh  = trim($_POST['txt_tenkh']  ?? '');
    $diachi = trim($_POST['txt_diachi'] ?? '');
    $sdt    = trim($_POST['txt_sdt']    ?? '');

    if (empty($tenkh)) {
        setFlash('error', 'Tên khách hàng không được để trống');
        header("Location: khachhang_add.php");
        exit();
    }

    $result = callAPI('POST', '/api/khachhang', [
        'tenkh'  => $tenkh,
        'diachi' => $diachi,
        'sdt'    => $sdt,
    ]);

    if ($result && $result['status']) {
        setFlash('success', 'Thêm khách hàng "' . $tenkh . '" thành công');
        header("Location: khachhang.php");
        exit();
    }
    setFlash('error', $result['message'] ?? 'Thêm khách hàng thất bại');
    header("Location: khachhang_add.php");
    exit();
}

$error = getFlash('error');

$page_title = 'Thêm Khách hàng';
$active_nav = 'khachhang';
$extra_css  = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/khachhang.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";
?>

<main class="container">
    <h1>THÊM KHÁCH HÀNG</h1>

    <?php if ($error): ?>
        <div class="dm-alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="khachhang_add.php" class="dm-form">

        <div class="dm-form-group">
            <label for="txt_tenkh" class="dm-label">
                Tên khách hàng <span class="dm-required">*</span>
            </label>
            <input type="text" id="txt_tenkh" name="txt_tenkh"
                   placeholder="Nhập tên khách hàng"
                   class="dm-input" required>
            <small style="color:var(--dm-muted); font-size:12px; margin-top:4px; display:block;">
                Tài khoản sẽ được tạo tự động với mật khẩu mặc định là <strong>123456</strong>
            </small>
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

        <div class="dm-form-actions">
            <button type="submit" class="dm-btn dm-btn-primary">Thêm khách hàng</button>
            <a href="khachhang.php" class="dm-btn dm-btn-default">Quay lại</a>
        </div>
    </form>
</main>

<?php include "../../includes/footer.php"; ?>