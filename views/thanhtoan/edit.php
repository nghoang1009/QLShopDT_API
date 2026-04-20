<?php
/**
 * Sửa Thanh toán
 * Render bởi ThanhToanController@edit
 * Biến: $payment, $paymentMethods, $paymentStatuses
 */
require_once BASE_PATH . '/includes/api_helper.php';

$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/thanhtoan.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include BASE_PATH . '/includes/header.php';

$ngaytt = !empty($payment['ngaythanhtoan'])
    ? date('Y-m-d\TH:i', strtotime($payment['ngaythanhtoan']))
    : date('Y-m-d\TH:i');
?>

<div class="tt-toolbar">
    <h1>SỬA THANH TOÁN #<?= e($payment['matt']) ?></h1>
</div>

<div class="tt-form-wrap">
    <div class="tt-form-card">
        <div class="tt-form-header">Cập nhật thanh toán</div>
        <form method="POST" action="/QLShopDT_API/thanhtoan/update">
            <?= csrf_field() ?>
            <input type="hidden" name="matt" value="<?= e($payment['matt']) ?>">
            <div class="tt-form-body">

                <div class="tt-form-group">
                    <label>Đơn hàng</label>
                    <input type="text" value="#<?= e($payment['madh']) ?> — <?= e($payment['tenkh'] ?? '') ?>" readonly>
                    <input type="hidden" name="madh" value="<?= e($payment['madh']) ?>">
                </div>

                <div class="tt-form-row">
                    <div class="tt-form-group">
                        <label>Phương thức <span class="req">*</span></label>
                        <select name="phuongthuc" required>
                            <?php foreach ($paymentMethods as $m): ?>
                                <option value="<?= e($m) ?>" <?= $payment['phuongthuc'] === $m ? 'selected' : '' ?>>
                                    <?= e($m) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="tt-form-group">
                        <label>Số tiền (đ) <span class="req">*</span></label>
                        <input type="number" name="sotien" value="<?= e($payment['sotien']) ?>"
                               min="0" step="1000" required>
                    </div>
                </div>

                <div class="tt-form-row">
                    <div class="tt-form-group">
                        <label>Trạng thái</label>
                        <select name="trangthai">
                            <?php foreach ($paymentStatuses as $s): ?>
                                <option value="<?= e($s) ?>" <?= $payment['trangthai'] === $s ? 'selected' : '' ?>>
                                    <?= e($s) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="tt-form-group">
                        <label>Ngày thanh toán</label>
                        <input type="datetime-local" name="ngaythanhtoan" value="<?= $ngaytt ?>">
                    </div>
                </div>

                <div class="tt-form-group">
                    <label>Ghi chú</label>
                    <textarea name="ghichu" rows="3"><?= e($payment['ghichu'] ?? '') ?></textarea>
                </div>

            </div>
            <div class="tt-form-footer">
                <a href="/QLShopDT_API/thanhtoan" class="tt-cancel-btn">Hủy</a>
                <button type="submit" class="tt-submit-btn">Cập nhật</button>
            </div>
        </form>
    </div>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
