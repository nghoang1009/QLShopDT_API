<?php
/**
 * Sửa Khách hàng
 */
session_start();
require_once "../../includes/api_helper.php";

requireLogin();
requireRole([1, 2]);

$makh = (int)($_GET['makh'] ?? $_POST['makh'] ?? 0);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenkh  = trim($_POST['txt_tenkh']  ?? '');
    $diachi = trim($_POST['txt_diachi'] ?? '');
    $sdt    = trim($_POST['txt_sdt']    ?? '');

    if (empty($tenkh)) {
        setFlash('error', 'Tên khách hàng không được để trống');
        header("Location: khachhang_edit.php?makh=" . $makh);
        exit();
    }

    $result = callAPI('PUT', '/api/khachhang/' . $makh, [
        'tenkh'  => $tenkh,
        'diachi' => $diachi,
        'sdt'    => $sdt,
    ]);

    if ($result && $result['status']) {
        setFlash('success', 'Cập nhật khách hàng thành công');
        header("Location: khachhang.php");
        exit();
    }
    setFlash('error', $result['message'] ?? 'Cập nhật thất bại');
    header("Location: khachhang_edit.php?makh=" . $makh);
    exit();
}

$result_kh = callAPI('GET', '/api/khachhang/' . $makh);
if (!($result_kh && $result_kh['status'])) {
    setFlash('error', 'Không tìm thấy khách hàng');
    header("Location: khachhang.php");
    exit();
}
$kh = $result_kh['data'];

$error = getFlash('error');

$page_title = 'Sửa Khách hàng';
$active_nav = 'khachhang';
$extra_css  = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/khachhang.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";
?>

<main class="container">
    <h1>SỬA KHÁCH HÀNG</h1>

    <?php if ($error): ?>
        <div class="dm-alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="khachhang_edit.php?makh=<?= e($makh) ?>" class="dm-form">

        <div class="dm-id-badge">
            <small>Mã khách hàng</small>
            <strong>#<?= e($kh['makh']) ?> — <?= e($kh['tentk'] ?? '') ?></strong>
        </div>

        <div class="dm-form-group">
            <label for="txt_tenkh" class="dm-label">
                Tên khách hàng <span class="dm-required">*</span>
            </label>
            <input type="text" id="txt_tenkh" name="txt_tenkh"
                   value="<?= e($kh['tenkh']) ?>"
                   class="dm-input" required>
        </div>

        <div class="dm-form-group">
            <label for="txt_diachi" class="dm-label">Địa chỉ</label>
            <input type="text" id="txt_diachi" name="txt_diachi"
                   value="<?= e($kh['diachi']) ?>"
                   class="dm-input">
        </div>

        <div class="dm-form-group">
            <label for="txt_sdt" class="dm-label">Số điện thoại</label>
            <input type="text" id="txt_sdt" name="txt_sdt"
                   value="<?= e($kh['sdt']) ?>"
                   class="dm-input">
        </div>

        <div class="dm-form-actions">
            <button type="submit" class="dm-btn dm-btn-primary">Cập nhật</button>
            <a href="khachhang.php" class="dm-btn dm-btn-default">Quay lại</a>
        </div>
    </form>
</main>

<?php include "../../includes/footer.php"; ?>