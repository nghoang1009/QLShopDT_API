<?php
/**
 * Quản lý Vận chuyển - Danh sách
 * Render bởi VanChuyenController@index
 * Biến: $shippings, $role, $success, $error
 */
require_once BASE_PATH . '/includes/api_helper.php';

$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/vanchuyen.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include BASE_PATH . '/includes/header.php';

$canEdit = ($role == 1 || $role == 2);

function vcBadge(string $trangthai): string {
    $map = [
        'Đã giao'   => 'vc-badge-green',
        'Đang giao' => 'vc-badge-blue',
        'Chờ lấy'   => 'vc-badge-yellow',
        'Hủy'       => 'vc-badge-red',
    ];
    $cls = $map[$trangthai] ?? 'vc-badge-gray';
    return '<span class="vc-badge ' . $cls . '">' . e($trangthai) . '</span>';
}

function vcStatus(array $vc): string {
    if (empty($vc['ngaygiao'])) return vcBadge('Chờ lấy');
    $today = new DateTime();
    $giao  = new DateTime($vc['ngaygiao']);
    return vcBadge($today >= $giao ? 'Đã giao' : 'Đang giao');
}
?>

<main class="container">

    <div class="vc-toolbar">
        <h1>QUẢN LÝ VẬN CHUYỂN</h1>
        <?php if ($canEdit): ?>
            <a href="/QLShopDT_API/vanchuyen/add" class="vc-add-btn">+ Thêm vận chuyển</a>
        <?php endif; ?>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="vc-table-wrap">
    <?php if (!empty($shippings)): ?>
        <table class="vc-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Mã VC</th>
                    <th>Mã ĐH</th>
                    <th>Khách hàng</th>
                    <th>Địa chỉ</th>
                    <th>SĐT</th>
                    <th>Ngày đặt</th>
                    <th>Ngày giao</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($shippings as $i => $vc): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td class="vc-col-id">#<?= e($vc['mavc']) ?></td>
                    <td class="vc-col-order">
                        <a href="/QLShopDT_API/donhang/detail/<?= e($vc['madh']) ?>">#<?= e($vc['madh']) ?></a>
                    </td>
                    <td class="vc-col-name"><?= e($vc['tenkh'] ?? '—') ?></td>
                    <td class="vc-col-addr"><?= e($vc['diachi'] ?? '—') ?></td>
                    <td class="vc-col-phone"><?= e($vc['sdt'] ?? '—') ?></td>
                    <td class="vc-col-date"><?= $vc['ngaydat'] ? date('d/m/Y', strtotime($vc['ngaydat'])) : '—' ?></td>
                    <td class="vc-col-date vc-col-deliver">
                        <?= $vc['ngaygiao'] ? date('d/m/Y', strtotime($vc['ngaygiao'])) : '—' ?>
                    </td>
                    <td class="vc-col-amount"><?= formatMoney($vc['trigia'] ?? 0) ?></td>
                    <td><?= vcStatus($vc) ?></td>
                    <td class="vc-col-actions">
                        <div class="vc-actions">
                            <a href="/QLShopDT_API/vanchuyen/detail/<?= e($vc['mavc']) ?>" class="vc-btn vc-btn-view">Xem</a>
                            <?php if ($canEdit): ?>
                                <a href="/QLShopDT_API/vanchuyen/edit/<?= e($vc['mavc']) ?>" class="vc-btn vc-btn-edit">Sửa</a>
                                <a href="/QLShopDT_API/vanchuyen/confirm/<?= e($vc['mavc']) ?>"
                                   class="vc-btn vc-btn-confirm"
                                   onclick="return confirm('Xác nhận đã giao đơn #<?= e($vc['madh']) ?>?')">Đã giao</a>
                                <a href="/QLShopDT_API/vanchuyen/delete/<?= e($vc['mavc']) ?>"
                                   class="vc-btn vc-btn-del"
                                   onclick="return confirm('Xóa vận chuyển #<?= e($vc['mavc']) ?>?')">Xóa</a>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="11">Tổng: <strong><?= count($shippings) ?></strong> đơn vận chuyển</td>
                </tr>
            </tfoot>
        </table>
    <?php else: ?>
        <div class="vc-empty">
            <p>Chưa có dữ liệu vận chuyển nào.</p>
            <?php if ($canEdit): ?>
                <a href="/QLShopDT_API/vanchuyen/add" class="vc-add-btn">+ Thêm vận chuyển đầu tiên</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    </div>

</main>

<?php include BASE_PATH . '/includes/footer.php'; ?>
