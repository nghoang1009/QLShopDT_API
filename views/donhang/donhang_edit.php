<?php
/**
 * Sửa Đơn hàng
 */
session_start();
require_once "../../includes/api_helper.php";

requireLogin();
requireRole([1, 2]);

$madh = (int)($_GET['madh'] ?? $_POST['madh'] ?? 0);

// Xử lý POST trước header
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [];
    if (isset($_POST['num_trigia'])) {
        $data['trigia'] = (float)$_POST['num_trigia'];
    }
    if (!empty($_POST['sel_trangthai'])) {
        $data['trangthai'] = $_POST['sel_trangthai'];
    }

    $result = callAPI('PUT', '/api/donhang/' . $madh, $data);

    if ($result && $result['status']) {
        setFlash('success', 'Cập nhật đơn hàng thành công');
        header("Location: donhang.php");
        exit();
    }
    setFlash('error', $result['message'] ?? 'Cập nhật thất bại');
    header("Location: donhang_edit.php?madh=" . $madh);
    exit();
}

// Lấy thông tin đơn hàng
$result_dh = callAPI('GET', '/api/donhang/' . $madh);
if (!($result_dh && $result_dh['status'])) {
    setFlash('error', 'Không tìm thấy đơn hàng');
    header("Location: donhang.php");
    exit();
}
$donhang = $result_dh['data'];

$error = getFlash('error');

$page_title = 'Sửa Đơn hàng #' . $madh;
$active_nav = 'donhang';
$extra_css  = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/donhang.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";

$trangthai_list = ['Chờ xác nhận', 'Đã xác nhận', 'Đang giao', 'Đã giao', 'Đã hủy'];
?>

<!-- Page Header -->
<div class="dh-page-header">
    <div class="dh-page-header-inner">
        <div>
            <h1 class="dh-page-title">Cập nhật đơn hàng</h1>
            <p class="dh-page-subtitle">Chỉnh sửa thông tin đơn hàng <strong style="color:var(--dh-accent)">#<?= e($madh) ?></strong></p>
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

        <!-- Order info (read-only) -->
        <div class="dh-order-info">
            <div class="dh-order-info-id">
                Đơn hàng <span>#<?= e($donhang['madh']) ?></span> — <?= e($donhang['tenkh']) ?>
            </div>
            <div class="dh-order-info-grid">
                <div class="dh-order-info-item">
                    <span class="dh-order-info-label">Địa chỉ</span>
                    <span class="dh-order-info-value"><?= e($donhang['diachi']) ?></span>
                </div>
                <div class="dh-order-info-item">
                    <span class="dh-order-info-label">Số điện thoại</span>
                    <span class="dh-order-info-value"><?= e($donhang['sdt']) ?></span>
                </div>
                <div class="dh-order-info-item">
                    <span class="dh-order-info-label">Ngày đặt</span>
                    <span class="dh-order-info-value"><?= date('d/m/Y', strtotime($donhang['ngaydat'])) ?></span>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="dh-form-card">
            <div class="dh-form-card-header">
                <div class="dh-form-icon"><i class="fas fa-pen"></i></div>
                <h2>Thông tin cập nhật</h2>
            </div>
            <div class="dh-form-body">
                <form method="POST" action="donhang_edit.php?madh=<?= e($madh) ?>">

                    <div class="dh-form-group">
                        <label for="num_trigia" class="dh-label">
                            Tổng tiền (VNĐ) <span class="dh-required">*</span>
                        </label>
                        <input type="number" id="num_trigia" name="num_trigia"
                               value="<?= e($donhang['trigia']) ?>" min="0"
                               class="dh-input" required>
                    </div>

                    <div class="dh-form-group">
                        <label for="sel_trangthai" class="dh-label">
                            Trạng thái <span class="dh-required">*</span>
                        </label>
                        <select id="sel_trangthai" name="sel_trangthai" class="dh-input">
                            <?php foreach ($trangthai_list as $tts): ?>
                                <option value="<?= e($tts) ?>" <?= ($donhang['trangthai'] ?? '') === $tts ? 'selected' : '' ?>>
                                    <?= e($tts) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="dh-form-actions">
                        <button type="submit" class="dh-submit-btn">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                        <a href="donhang_chitiet.php?madh=<?= e($madh) ?>" class="dh-secondary-btn">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
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