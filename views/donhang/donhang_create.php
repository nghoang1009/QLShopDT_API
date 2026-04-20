<?php
/**
 * Tạo Đơn hàng mới
 */
session_start();
require_once "../../includes/api_helper.php";

requireLogin();
requireRole([1, 2]);

// Xử lý POST trước header
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $makh   = (int)$_POST['txt_makh'];
    $trigia = (float)$_POST['num_trigia'];

    if (!$makh || $trigia < 0) {
        setFlash('error', 'Vui lòng nhập đầy đủ thông tin hợp lệ');
        header("Location: donhang_create.php");
        exit();
    }

    $data = [
        'makh'   => $makh,
        'trigia' => $trigia,
    ];

    $result = callAPI('POST', '/api/donhang', $data);

    if ($result && $result['status']) {
        setFlash('success', 'Tạo đơn hàng thành công');
        header("Location: donhang.php");
        exit();
    }
    setFlash('error', $result['message'] ?? 'Tạo đơn hàng thất bại');
    header("Location: donhang_create.php");
    exit();
}

$error = getFlash('error');

$page_title = 'Tạo Đơn hàng mới';
$active_nav = 'donhang';
$extra_css  = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/donhang.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";
?>

<!-- Page Header -->
<div class="dh-page-header">
    <div class="dh-page-header-inner">
        <div>
            <h1 class="dh-page-title">Tạo đơn hàng mới</h1>
            <p class="dh-page-subtitle">Thêm đơn hàng vào hệ thống</p>
        </div>
        <a href="donhang.php" class="dh-cancel-btn">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
    </div>
</div>

<main class="container">
    <div class="dh-form-wrap">

        <?php if ($error): ?>
            <div class="dh-alert dh-alert-error">
                <i class="fas fa-exclamation-circle"></i> <?= e($error) ?>
            </div>
        <?php endif; ?>

        <div class="dh-form-card">
            <div class="dh-form-card-header">
                <div class="dh-form-icon"><i class="fas fa-receipt"></i></div>
                <h2>Thông tin đơn hàng</h2>
            </div>
            <div class="dh-form-body">
                <form method="POST" action="donhang_create.php">

                    <div class="dh-form-group">
                        <label for="txt_makh" class="dh-label">
                            Mã khách hàng <span class="dh-required">*</span>
                        </label>
                        <input type="number" id="txt_makh" name="txt_makh"
                               placeholder="Nhập mã khách hàng"
                               class="dh-input" min="1" required>
                    </div>

                    <div class="dh-form-group">
                        <label for="num_trigia" class="dh-label">
                            Tổng tiền (VNĐ) <span class="dh-required">*</span>
                        </label>
                        <input type="number" id="num_trigia" name="num_trigia"
                               placeholder="0" class="dh-input" min="0" value="0" required>
                    </div>

                    <div class="dh-form-actions">
                        <button type="submit" class="dh-submit-btn">
                            <i class="fas fa-plus"></i> Tạo đơn hàng
                        </button>
                        <a href="donhang.php" class="dh-cancel-btn">
                            <i class="fas fa-times"></i> Hủy
                        </a>
                    </div>
                </form>
            </div>
        </div>

    </div>
</main>

<?php include "../../includes/footer.php"; ?>