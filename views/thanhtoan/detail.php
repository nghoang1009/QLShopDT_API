<?php
/**
 * Chi tiết Thanh toán
 * Render bởi ThanhToanController@show
 * Biến: $payment, $order, $orderDetails, $role
 */
require_once BASE_PATH . '/includes/api_helper.php';

$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/thanhtoan.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include BASE_PATH . '/includes/header.php';

$statusMap = [
    'Đã thanh toán' => 'tt-badge-green',
    'Chờ xác nhận'  => 'tt-badge-yellow',
    'Đã hủy'        => 'tt-badge-red',
    'Hoàn tiền'     => 'tt-badge-blue',
];
$badgeClass = $statusMap[$payment['trangthai']] ?? 'tt-badge-gray';
?>

<div class="tt-toolbar">
    <h1>CHI TIẾT THANH TOÁN #<?= e($payment['matt']) ?></h1>
    <?php if (in_array($role, [1, 2])): ?>
    <div style="display:flex;gap:10px">
        <a href="/QLShopDT_API/thanhtoan/edit/<?= $payment['matt'] ?>" class="tt-add-btn" style="background:linear-gradient(135deg,#f59e0b,#d97706)">Sửa</a>
        <a href="/QLShopDT_API/thanhtoan" class="tt-cancel-btn">← Danh sách</a>
    </div>
    <?php endif; ?>
</div>

<div class="tt-detail-wrap">

    <!-- Thông tin thanh toán -->
    <div class="tt-detail-card">
        <div class="tt-detail-header">
            <span>Thông tin thanh toán</span>
            <span class="tt-badge <?= $badgeClass ?>"><?= e($payment['trangthai']) ?></span>
        </div>
        <div class="tt-info-grid">
            <div class="tt-info-item">
                <div class="tt-info-label">Mã thanh toán</div>
                <div class="tt-info-value">#<?= e($payment['matt']) ?></div>
            </div>
            <div class="tt-info-item">
                <div class="tt-info-label">Mã đơn hàng</div>
                <div class="tt-info-value">#<?= e($payment['madh']) ?></div>
            </div>
            <div class="tt-info-item">
                <div class="tt-info-label">Phương thức</div>
                <div class="tt-info-value"><?= e($payment['phuongthuc']) ?></div>
            </div>
            <div class="tt-info-item">
                <div class="tt-info-label">Ngày thanh toán</div>
                <div class="tt-info-value"><?= date('d/m/Y H:i', strtotime($payment['ngaythanhtoan'])) ?></div>
            </div>
            <div class="tt-info-item">
                <div class="tt-info-label">Số tiền</div>
                <div class="tt-info-value accent"><?= formatMoney($payment['sotien']) ?></div>
            </div>
            <div class="tt-info-item">
                <div class="tt-info-label">Ghi chú</div>
                <div class="tt-info-value"><?= e($payment['ghichu'] ?? '—') ?></div>
            </div>
        </div>
    </div>

    <!-- Thông tin khách hàng -->
    <div class="tt-detail-card">
        <div class="tt-detail-header"><span>Thông tin khách hàng</span></div>
        <div class="tt-info-grid">
            <div class="tt-info-item">
                <div class="tt-info-label">Tên khách hàng</div>
                <div class="tt-info-value"><?= e($payment['tenkh'] ?? '—') ?></div>
            </div>
            <div class="tt-info-item">
                <div class="tt-info-label">Số điện thoại</div>
                <div class="tt-info-value"><?= e($payment['sdt'] ?? '—') ?></div>
            </div>
            <div class="tt-info-item" style="grid-column: 1 / -1; border-right: none;">
                <div class="tt-info-label">Địa chỉ</div>
                <div class="tt-info-value"><?= e($payment['diachi'] ?? '—') ?></div>
            </div>
        </div>
    </div>

    <!-- Chi tiết sản phẩm trong đơn hàng -->
    <?php if (!empty($orderDetails)): ?>
    <div class="tt-detail-card">
        <div class="tt-detail-header"><span>Sản phẩm trong đơn #<?= e($payment['madh']) ?></span></div>
        <table class="tt-items-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Hãng</th>
                    <th style="text-align:right">Đơn giá</th>
                    <th style="text-align:center">SL</th>
                    <th style="text-align:right">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($orderDetails as $item): ?>
                <tr>
                    <td style="font-weight:600"><?= e($item['tensp']) ?></td>
                    <td style="color:var(--tt-muted);font-size:.82rem"><?= e($item['hang']) ?></td>
                    <td style="text-align:right"><?= formatMoney($item['gia']) ?></td>
                    <td style="text-align:center;font-weight:700"><?= e($item['sl']) ?></td>
                    <td style="text-align:right;color:var(--tt-accent);font-weight:600">
                        <?= formatMoney($item['gia'] * $item['sl']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="tt-items-total">
            Tổng cộng: <?= formatMoney(array_sum(array_map(fn($i) => $i['gia'] * $i['sl'], $orderDetails))) ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
