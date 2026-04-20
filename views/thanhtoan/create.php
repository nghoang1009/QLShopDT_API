<?php
/**
 * Thêm Thanh toán
 * Render bởi ThanhToanController@create
 * Biến: $unpaidOrders, $paymentMethods
 */
require_once BASE_PATH . '/includes/api_helper.php';

$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/thanhtoan.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include BASE_PATH . '/includes/header.php';
?>

<div class="tt-toolbar">
    <h1>THÊM THANH TOÁN</h1>
</div>

<div class="tt-form-wrap">
    <div class="tt-form-card">
        <div class="tt-form-header">Thông tin thanh toán</div>
        <form method="POST" action="/QLShopDT_API/thanhtoan/store">
            <?= csrf_field() ?>
            <div class="tt-form-body">

                <div class="tt-form-group">
                    <label>Đơn hàng <span class="req">*</span></label>
                    <select name="madh" id="sel_madh" required onchange="fillAmount(this)">
                        <option value="">-- Chọn đơn hàng chưa thanh toán --</option>
                        <?php foreach ($unpaidOrders as $dh): ?>
                            <option value="<?= e($dh['madh']) ?>" data-trigia="<?= e($dh['trigia']) ?>">
                                #<?= e($dh['madh']) ?> — <?= e($dh['tenkh']) ?> — <?= formatMoney($dh['trigia']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="tt-form-row">
                    <div class="tt-form-group">
                        <label>Phương thức <span class="req">*</span></label>
                        <select name="phuongthuc" required>
                            <option value="">-- Chọn --</option>
                            <?php foreach ($paymentMethods as $m): ?>
                                <option value="<?= e($m) ?>"><?= e($m) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="tt-form-group">
                        <label>Số tiền (đ) <span class="req">*</span></label>
                        <input type="number" name="sotien" id="inp_sotien" min="0" step="1000" required>
                    </div>
                </div>

                <div class="tt-form-row">
                    <div class="tt-form-group">
                        <label>Trạng thái</label>
                        <select name="trangthai">
                            <option value="Chờ xác nhận">Chờ xác nhận</option>
                            <option value="Đã thanh toán">Đã thanh toán</option>
                            <option value="Đã hủy">Đã hủy</option>
                            <option value="Hoàn tiền">Hoàn tiền</option>
                        </select>
                    </div>
                    <div class="tt-form-group">
                        <label>Ngày thanh toán</label>
                        <input type="datetime-local" name="ngaythanhtoan"
                               value="<?= date('Y-m-d\TH:i') ?>">
                    </div>
                </div>

                <div class="tt-form-group">
                    <label>Ghi chú</label>
                    <textarea name="ghichu" rows="3" placeholder="Ghi chú (không bắt buộc)"></textarea>
                </div>

            </div>
            <div class="tt-form-footer">
                <a href="/QLShopDT_API/thanhtoan" class="tt-cancel-btn">Hủy</a>
                <button type="submit" class="tt-submit-btn">Lưu thanh toán</button>
            </div>
        </form>
    </div>
</div>

<script>
function fillAmount(sel) {
    const opt = sel.options[sel.selectedIndex];
    const trigia = opt.getAttribute('data-trigia');
    document.getElementById('inp_sotien').value = trigia || '';
}
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>
