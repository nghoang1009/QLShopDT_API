<?php
/**
 * Vận chuyển - Thêm mới
 * Render bởi VanChuyenController@create
 * Biến: $orders (array đã lọc), $customers (array)
 */
require_once BASE_PATH . '/includes/api_helper.php';

$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/vanchuyen.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include BASE_PATH . '/includes/header.php';
?>

<main class="container">

    <div class="vc-toolbar">
        <h1>THÊM VẬN CHUYỂN</h1>
        <a href="/QLShopDT_API/vanchuyen" class="vc-back-btn">← Quay lại</a>
    </div>

    <div class="vc-form-wrap">
        <form method="POST" action="/QLShopDT_API/app.php/vanchuyen/store" class="vc-form">
            <?= csrf_field() ?>

            <div class="vc-form-group">
                <label class="vc-form-label" for="madh">
                    Đơn hàng <span class="vc-req">*</span>
                </label>
                <select name="madh" id="madh" class="vc-form-control" required onchange="fillCustomer(this)">
                    <option value="">-- Chọn đơn hàng --</option>
                    <?php foreach ($orders as $order): ?>
                        <option value="<?= e($order['madh']) ?>"
                                data-makh="<?= e($order['makh'] ?? '') ?>">
                            #<?= e($order['madh']) ?>
                            – <?= e($order['tenkh'] ?? 'KH#' . ($order['makh'] ?? '')) ?>
                            (<?= formatMoney($order['trigia'] ?? 0) ?>)
                            – <?= e($order['trangthai'] ?? '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="vc-form-group">
                <label class="vc-form-label" for="makh">
                    Khách hàng <span class="vc-req">*</span>
                </label>
                <select name="makh" id="makh" class="vc-form-control" required>
                    <option value="">-- Chọn khách hàng --</option>
                    <?php foreach ($customers as $kh): ?>
                        <option value="<?= e($kh['makh']) ?>">
                            #<?= e($kh['makh']) ?> – <?= e($kh['tenkh']) ?>
                            <?= !empty($kh['sdt']) ? '(' . e($kh['sdt']) . ')' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="vc-form-group">
                <label class="vc-form-label" for="ngaygiao">
                    Ngày giao dự kiến <span class="vc-req">*</span>
                </label>
                <input type="date" name="ngaygiao" id="ngaygiao"
                       class="vc-form-control"
                       min="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="vc-form-footer">
                <button type="submit" class="vc-btn vc-btn-save">Thêm vận chuyển</button>
                <a href="/QLShopDT_API/vanchuyen" class="vc-btn vc-btn-cancel">Hủy</a>
            </div>
        </form>
    </div>

</main>

<script>
function fillCustomer(sel) {
    const makh = sel.options[sel.selectedIndex].dataset.makh;
    if (!makh) return;
    const khSel = document.getElementById('makh');
    for (let i = 0; i < khSel.options.length; i++) {
        if (khSel.options[i].value == makh) {
            khSel.selectedIndex = i;
            break;
        }
    }
}
</script>

<?php include BASE_PATH . '/includes/footer.php'; ?>
