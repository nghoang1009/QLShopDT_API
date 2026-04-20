<?php
/**
 * Quản lý Đơn hàng - Danh sách
 */
session_start();
require_once "../../includes/api_helper.php";

requireLogin();

$role = getCurrentRole();

// Lấy dữ liệu trước khi include header
if ($role === 0) {
    $makh = $_SESSION['userid'] ?? 0;
    $result = callAPI('GET', '/api/donhang', ['makh' => $makh]);
} else {
    $result = callAPI('GET', '/api/donhang');
}
$orders  = ($result && $result['status']) ? $result['data'] : [];
$tong_dh = count($orders);

$can_manage = isAdminOrStaff();
$success    = getFlash('success');
$error      = getFlash('error');

// Tính thống kê nhanh
$cnt_pending = 0;
$cnt_active  = 0;
$cnt_done    = 0;
foreach ($orders as $o) {
    $tt = $o['trangthai'] ?? '';
    if ($tt === 'Chờ xác nhận')                             $cnt_pending++;
    elseif (in_array($tt, ['Đã xác nhận', 'Đang giao']))    $cnt_active++;
    elseif ($tt === 'Đã giao')                              $cnt_done++;
}

$page_title = 'Quản lý Đơn hàng';
$active_nav = 'donhang';
$extra_css  = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/donhang.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";
?>

<!-- Page Header -->
<div class="dh-page-header">
    <div class="dh-page-header-inner">
        <div>
            <h1 class="dh-page-title">Quản lý Đơn hàng</h1>
            <p class="dh-page-subtitle">Theo dõi và xử lý toàn bộ đơn hàng</p>
        </div>
        <?php if ($can_manage): ?>
            <a href="donhang_create.php" class="dh-add-btn">
                <i class="fas fa-plus"></i> Tạo đơn hàng
            </a>
        <?php else: ?>
            <span class="dh-page-badge"><i class="fas fa-shopping-bag"></i> <?= $tong_dh ?> đơn hàng</span>
        <?php endif; ?>
    </div>
</div>

<main class="container">

    <!-- Stats -->
    <div class="dh-stats">
        <div class="dh-stat-card total">
            <div class="dh-stat-icon"><i class="fas fa-receipt"></i></div>
            <div class="dh-stat-info">
                <div class="dh-stat-value"><?= $tong_dh ?></div>
                <div class="dh-stat-label">Tổng đơn hàng</div>
            </div>
        </div>
        <div class="dh-stat-card pending">
            <div class="dh-stat-icon"><i class="fas fa-clock"></i></div>
            <div class="dh-stat-info">
                <div class="dh-stat-value"><?= $cnt_pending ?></div>
                <div class="dh-stat-label">Chờ xác nhận</div>
            </div>
        </div>
        <div class="dh-stat-card active">
            <div class="dh-stat-icon"><i class="fas fa-truck"></i></div>
            <div class="dh-stat-info">
                <div class="dh-stat-value"><?= $cnt_active ?></div>
                <div class="dh-stat-label">Đang xử lý</div>
            </div>
        </div>
        <div class="dh-stat-card done">
            <div class="dh-stat-icon"><i class="fas fa-check-circle"></i></div>
            <div class="dh-stat-info">
                <div class="dh-stat-value"><?= $cnt_done ?></div>
                <div class="dh-stat-label">Đã hoàn thành</div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    <?php if ($success): ?>
        <div class="dh-alert dh-alert-success">
            <i class="fas fa-check-circle"></i> <?= e($success) ?>
        </div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="dh-alert dh-alert-error">
            <i class="fas fa-exclamation-circle"></i> <?= e($error) ?>
        </div>
    <?php endif; ?>

    <!-- Toolbar -->
    <div class="dh-toolbar">
        <span class="dh-section-label">Danh sách đơn hàng</span>
    </div>

    <!-- Table -->
    <div class="dh-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Mã ĐH</th>
                    <th style="text-align:left">Khách hàng</th>
                    <th>Địa chỉ</th>
                    <th>SĐT</th>
                    <th>Ngày đặt</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái</th>
                    <th>Chi tiết</th>
                    <?php if ($can_manage): ?><th>Thao tác</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($tong_dh > 0): ?>
                    <?php foreach ($orders as $i => $dh): ?>
                        <?php
                        $tt = $dh['trangthai'] ?? '';
                        $badge_class = match($tt) {
                            'Chờ xác nhận' => 'badge-pending',
                            'Đã xác nhận'  => 'badge-confirmed',
                            'Đang giao'    => 'badge-shipping',
                            'Đã giao'      => 'badge-done',
                            'Đã hủy'       => 'badge-cancelled',
                            default        => 'badge-default',
                        };
                        ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td class="dh-td-id">#<?= e($dh['madh']) ?></td>
                            <td class="dh-td-name"><?= e($dh['tenkh']) ?></td>
                            <td><?= e($dh['diachi']) ?></td>
                            <td style="font-family:'Courier New',monospace;font-size:12px"><?= e($dh['sdt']) ?></td>
                            <td class="dh-td-date"><?= date('d/m/Y', strtotime($dh['ngaydat'])) ?></td>
                            <td class="dh-td-price"><?= formatMoney($dh['trigia']) ?></td>
                            <td><span class="dh-badge <?= $badge_class ?>"><?= e($tt) ?></span></td>
                            <td>
                                <a href="donhang_chitiet.php?madh=<?= e($dh['madh']) ?>" class="dh-btn dh-btn-view">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                            </td>
                            <?php if ($can_manage): ?>
                                <td>
                                    <a href="donhang_edit.php?madh=<?= e($dh['madh']) ?>" class="dh-btn dh-btn-edit">
                                        <i class="fas fa-pen"></i> Sửa
                                    </a>
                                    <a href="donhang_del.php?madh=<?= e($dh['madh']) ?>"
                                       class="dh-btn dh-btn-del"
                                       onclick="return confirm('Xác nhận xóa đơn hàng #<?= e($dh['madh']) ?>?')">
                                        <i class="fas fa-trash"></i> Xóa
                                    </a>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $can_manage ? 10 : 9 ?>">
                            <div class="dh-empty">
                                <i class="fas fa-inbox"></i>
                                <p>Chưa có đơn hàng nào</p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="<?= $can_manage ? 10 : 9 ?>">
                        Tổng cộng: <strong><?= $tong_dh ?></strong> đơn hàng
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

</main>

<?php include "../../includes/footer.php"; ?>