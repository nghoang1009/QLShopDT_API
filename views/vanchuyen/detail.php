<?php
/**
 * Vận chuyển - Chi tiết
 * Render bởi VanChuyenController@show
 * Biến: $shipping, $orderDetails
 */
require_once BASE_PATH . '/includes/api_helper.php';

$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/vanchuyen.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include BASE_PATH . '/includes/header.php';
?>

<main class="container">

    <div class="vc-toolbar">
        <h1>CHI TIẾT VẬN CHUYỂN #<?= e($shipping['mavc']) ?></h1>
        <a href="/QLShopDT_API/vanchuyen" class="vc-back-btn">← Quay lại</a>
    </div>

    <div class="vc-detail-grid">

        <!-- Thông tin vận chuyển -->
        <div class="vc-card">
            <h2 class="vc-card-title">Thông tin vận chuyển</h2>
            <div class="vc-card-body">
                <div class="vc-row">
                    <span class="vc-label">Mã vận chuyển</span>
                    <span class="vc-value vc-col-id">#<?= e($shipping['mavc']) ?></span>
                </div>
                <div class="vc-row">
                    <span class="vc-label">Đơn hàng</span>
                    <span class="vc-value">
                        <a href="/QLShopDT_API/donhang/detail/<?= e($shipping['madh']) ?>" class="vc-link">#<?= e($shipping['madh']) ?></a>
                    </span>
                </div>
                <div class="vc-row">
                    <span class="vc-label">Ngày đặt</span>
                    <span class="vc-value"><?= $shipping['ngaydat'] ? date('d/m/Y', strtotime($shipping['ngaydat'])) : '—' ?></span>
                </div>
                <div class="vc-row">
                    <span class="vc-label">Ngày giao</span>
                    <span class="vc-value vc-col-deliver">
                        <?= $shipping['ngaygiao'] ? date('d/m/Y', strtotime($shipping['ngaygiao'])) : '<em>Chưa xác định</em>' ?>
                    </span>
                </div>
                <div class="vc-row">
                    <span class="vc-label">Tổng tiền</span>
                    <span class="vc-value vc-col-amount"><?= formatMoney($shipping['trigia'] ?? 0) ?></span>
                </div>
            </div>
            <div class="vc-card-footer">
                <a href="/QLShopDT_API/vanchuyen/edit/<?= e($shipping['mavc']) ?>" class="vc-btn vc-btn-edit">Sửa</a>
                <a href="/QLShopDT_API/vanchuyen/confirm/<?= e($shipping['mavc']) ?>"
                   class="vc-btn vc-btn-confirm"
                   onclick="return confirm('Xác nhận đã giao hàng?')">Xác nhận đã giao</a>
                <a href="/QLShopDT_API/vanchuyen/delete/<?= e($shipping['mavc']) ?>"
                   class="vc-btn vc-btn-del"
                   onclick="return confirm('Xóa vận chuyển này?')">Xóa</a>
            </div>
        </div>

        <!-- Thông tin khách hàng -->
        <div class="vc-card">
            <h2 class="vc-card-title">Thông tin khách hàng</h2>
            <div class="vc-card-body">
                <div class="vc-row">
                    <span class="vc-label">Mã khách hàng</span>
                    <span class="vc-value"><?= e($shipping['makh']) ?></span>
                </div>
                <div class="vc-row">
                    <span class="vc-label">Tên khách hàng</span>
                    <span class="vc-value" style="font-weight:600"><?= e($shipping['tenkh'] ?? '—') ?></span>
                </div>
                <div class="vc-row">
                    <span class="vc-label">Địa chỉ giao</span>
                    <span class="vc-value"><?= e($shipping['diachi'] ?? '—') ?></span>
                </div>
                <div class="vc-row">
                    <span class="vc-label">Số điện thoại</span>
                    <span class="vc-value"><?= e($shipping['sdt'] ?? '—') ?></span>
                </div>
            </div>
        </div>

    </div>

    <!-- Sản phẩm trong đơn hàng -->
    <?php if (!empty($orderDetails)): ?>
    <div style="margin-bottom: 40px;">
        <h3 class="vc-section-title">Sản phẩm trong đơn hàng</h3>
        <div class="vc-table-wrap">
            <table class="vc-table">
                <thead>
                    <tr>
                        <th>STT</th>
                        <th>Tên sản phẩm</th>
                        <th>Số lượng</th>
                        <th>Đơn giá</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                $total = 0;
                foreach ($orderDetails as $i => $item):
                    $thanhtien = ($item['sl'] ?? 0) * ($item['gia'] ?? 0);
                    $total += $thanhtien;
                ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td style="text-align:left; font-weight:600"><?= e($item['tensp'] ?? '—') ?></td>
                        <td><?= e($item['sl'] ?? 0) ?></td>
                        <td><?= formatMoney($item['gia'] ?? 0) ?></td>
                        <td class="vc-col-amount"><?= formatMoney($thanhtien) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4">Tổng cộng:</td>
                        <td class="vc-col-amount"><?= formatMoney($total) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <?php endif; ?>

</main>

<?php include BASE_PATH . '/includes/footer.php'; ?>
