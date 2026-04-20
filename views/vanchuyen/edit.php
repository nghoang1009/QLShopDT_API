<?php
/**
 * Vận chuyển - Sửa
 * Render bởi VanChuyenController@edit
 * Biến: $shipping, $orders (array), $customers (array)
 */
require_once BASE_PATH . '/includes/api_helper.php';

$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/vanchuyen.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include BASE_PATH . '/includes/header.php';
?>

<main class="container">

    <div class="vc-toolbar">
        <h1>SỬA VẬN CHUYỂN #<?= e($shipping['mavc']) ?></h1>
        <a href="/QLShopDT_API/vanchuyen/detail/<?= e($shipping['mavc']) ?>" class="vc-back-btn">← Quay lại</a>
    </div>

    <div class="vc-form-wrap">
        <form method="POST" action="/QLShopDT_API/vanchuyen/update" class="vc-form">
            <?= csrf_field() ?>
            <input type="hidden" name="mavc" value="<?= e($shipping['mavc']) ?>">

            <!-- Đơn hàng: read-only -->
            <div class="vc-form-group">
                <label class="vc-form-label">Đơn hàng</label>
                <input type="text" class="vc-form-control"
                       value="#<?= e($shipping['madh']) ?> – <?= e($shipping['tenkh'] ?? '') ?>" readonly>
                <input type="hidden" name="madh" value="<?= e($shipping['madh']) ?>">
            </div>

            <!-- Khách hàng -->
            <div class="vc-form-group">
                <label class="vc-form-label" for="makh">
                    Khách hàng <span class="vc-req">*</span>
                </label>
                <select name="makh" id="makh" class="vc-form-control" required>
                    <option value="">-- Chọn khách hàng --</option>
                    <?php foreach ($customers as $kh): ?>
                        <option value="<?= e($kh['makh']) ?>"
                            <?= ((string)$kh['makh'] === (string)$shipping['makh']) ? 'selected' : '' ?>>
                            #<?= e($kh['makh']) ?> – <?= e($kh['tenkh']) ?>
                            <?= !empty($kh['sdt']) ? '(' . e($kh['sdt']) . ')' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Ngày giao -->
            <div class="vc-form-group">
                <label class="vc-form-label" for="ngaygiao">
                    Ngày giao dự kiến <span class="vc-req">*</span>
                </label>
                <input type="date" name="ngaygiao" id="ngaygiao"
                       class="vc-form-control"
                       value="<?= e($shipping['ngaygiao'] ?? '') ?>" required>
            </div>

            <div class="vc-form-footer">
                <button type="submit" class="vc-btn vc-btn-save">Lưu thay đổi</button>
                <a href="/QLShopDT_API/vanchuyen/detail/<?= e($shipping['mavc']) ?>" class="vc-btn vc-btn-cancel">Hủy</a>
            </div>
        </form>
    </div>

</main>

<?php include BASE_PATH . '/includes/footer.php'; ?>
