<?php
/**
 * Thanh toán nhanh từ đơn hàng
 * Render bởi ThanhToanController@quickPay
 * Biến: $order, $orderDetails, $paymentMethods
 */
require_once BASE_PATH . '/includes/api_helper.php';

$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/thanhtoan.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include BASE_PATH . '/includes/header.php';

$methodIcons = [
    'Tiền mặt'      => '💵',
    'Chuyển khoản'  => '🏦',
    'Thẻ tín dụng'  => '💳',
    'Ví điện tử'    => '📱',
    'COD'           => '📦',
];
?>

<div class="tt-toolbar">
    <h1>THANH TOÁN ĐƠN HÀNG #<?= e($order['madh']) ?></h1>
</div>

<div class="tt-quickpay-wrap">

    <!-- Tóm tắt đơn hàng -->
    <div class="tt-detail-card">
        <div class="tt-detail-header"><span>Tóm tắt đơn hàng</span></div>
        <?php if (!empty($orderDetails)): ?>
        <table class="tt-items-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th style="text-align:right">Đơn giá</th>
                    <th style="text-align:center">SL</th>
                    <th style="text-align:right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($orderDetails as $item): ?>
                <tr>
                    <td>
                        <div style="font-weight:600"><?= e($item['tensp']) ?></div>
                        <div style="font-size:.75rem;color:var(--tt-muted)"><?= e($item['hang']) ?></div>
                    </td>
                    <td style="text-align:right"><?= formatMoney($item['gia']) ?></td>
                    <td style="text-align:center;font-weight:700"><?= e($item['sl']) ?></td>
                    <td style="text-align:right;font-weight:600"><?= formatMoney($item['gia'] * $item['sl']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="tt-items-total">
            Tổng thanh toán: <span style="font-size:1.15rem"><?= formatMoney($order['trigia']) ?></span>
        </div>
        <?php else: ?>
            <div style="padding:20px;color:var(--tt-muted)">Không có thông tin sản phẩm</div>
        <?php endif; ?>
    </div>

    <!-- Form chọn phương thức -->
    <div class="tt-detail-card">
        <div class="tt-detail-header"><span>Chọn phương thức thanh toán</span></div>
        <form method="POST" action="/QLShopDT_API/thanhtoan/process-quick-pay">
            <?= csrf_field() ?>
            <input type="hidden" name="madh" value="<?= e($order['madh']) ?>">
            <div style="padding:24px">
                <div class="tt-method-grid">
                    <?php foreach ($paymentMethods as $i => $m): ?>
                        <label class="tt-method-label">
                            <input type="radio" name="phuongthuc" value="<?= e($m) ?>"
                                   <?= $i === 0 ? 'checked' : '' ?> required>
                            <div class="tt-method-box">
                                <span class="icon"><?= $methodIcons[$m] ?? '💰' ?></span>
                                <?= e($m) ?>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="tt-form-footer">
                <a href="/QLShopDT_API/donhang/detail/<?= e($order['madh']) ?>" class="tt-cancel-btn">← Quay lại</a>
                <button type="submit" class="tt-submit-btn">Xác nhận thanh toán <?= formatMoney($order['trigia']) ?></button>
            </div>
        </form>
    </div>

</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
