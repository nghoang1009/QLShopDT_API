<?php
/**
 * Quản lý Thanh toán - Danh sách
 * Render bởi ThanhToanController@index
 * Biến: $payments, $role, $canEdit, $success, $error
 */
require_once BASE_PATH . '/includes/api_helper.php';

$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/thanhtoan.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include BASE_PATH . '/includes/header.php';

function ttBadge(string $trangthai): string {
    $map = [
        'Đã thanh toán'  => 'tt-badge-green',
        'Chờ xác nhận'   => 'tt-badge-yellow',
        'Đã hủy'         => 'tt-badge-red',
        'Hoàn tiền'      => 'tt-badge-blue',
    ];
    $cls = $map[$trangthai] ?? 'tt-badge-gray';
    return '<span class="tt-badge ' . $cls . '">' . e($trangthai) . '</span>';
}
?>

<div class="tt-toolbar">
    <h1>QUẢN LÝ THANH TOÁN</h1>
    <?php if ($canEdit): ?>
        <a href="/QLShopDT_API/thanhtoan/add" class="tt-add-btn">+ Thêm thanh toán</a>
    <?php endif; ?>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= e($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-error"><?= e($error) ?></div>
<?php endif; ?>

<div class="tt-table-wrap">
    <?php if (!empty($payments)): ?>
    <table class="tt-table">
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã TT</th>
                <th>Mã ĐH</th>
                <th>Khách hàng</th>
                <th>Phương thức</th>
                <th>Ngày TT</th>
                <th>Số tiền</th>
                <th>Trạng thái</th>
                <th>Ghi chú</th>
                <?php if ($canEdit): ?><th>Thao tác</th><?php endif; ?>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($payments as $i => $tt): ?>
            <tr>
                <td><?= $i + 1 ?></td>
                <td><strong>#<?= e($tt['matt']) ?></strong></td>
                <td>#<?= e($tt['madh']) ?></td>
                <td>
                    <div style="font-weight:600"><?= e($tt['tenkh']) ?></div>
                    <div style="font-size:.75rem;color:var(--tt-muted)"><?= e($tt['sdt'] ?? '') ?></div>
                </td>
                <td><?= e($tt['phuongthuc']) ?></td>
                <td style="white-space:nowrap"><?= date('d/m/Y H:i', strtotime($tt['ngaythanhtoan'])) ?></td>
                <td style="color:var(--tt-accent);font-weight:700"><?= formatMoney($tt['sotien']) ?></td>
                <td><?= ttBadge($tt['trangthai']) ?></td>
                <td style="max-width:180px;color:var(--tt-muted);font-size:.82rem"><?= e($tt['ghichu'] ?? '') ?></td>
                <?php if ($canEdit): ?>
                <td>
                    <div class="tt-actions">
                        <a href="/QLShopDT_API/thanhtoan/detail/<?= $tt['matt'] ?>" class="tt-btn tt-btn-view">Xem</a>
                        <a href="/QLShopDT_API/thanhtoan/edit/<?= $tt['matt'] ?>" class="tt-btn tt-btn-edit">Sửa</a>
                        <a href="/QLShopDT_API/thanhtoan/delete/<?= $tt['matt'] ?>"
                           class="tt-btn tt-btn-delete"
                           onclick="return confirm('Xóa thanh toán #<?= $tt['matt'] ?>?')">Xóa</a>
                    </div>
                </td>
                <?php endif; ?>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php else: ?>
        <div class="tt-empty">
            <div class="tt-empty-icon">💳</div>
            Chưa có dữ liệu thanh toán
        </div>
    <?php endif; ?>
</div>

<?php include BASE_PATH . '/includes/footer.php'; ?>
