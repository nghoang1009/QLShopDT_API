<?php
/**
 * Chi tiết Đơn hàng
 */
session_start();
require_once "../../includes/api_helper.php";

requireLogin();

$madh = (int)($_REQUEST['madh'] ?? 0);

if (!$madh) {
    setFlash('error', 'Không tìm thấy đơn hàng');
    header("Location: donhang.php");
    exit();
}

$result_dh = callAPI('GET', '/api/donhang/' . $madh);

if (!($result_dh && $result_dh['status'])) {
    setFlash('error', 'Không tìm thấy đơn hàng');
    header("Location: donhang.php");
    exit();
}
$donhang = $result_dh['data'];
$chitiet = $donhang['chitiet'] ?? [];

$page_title = 'Chi tiết Đơn hàng #' . $madh;
$active_nav = 'donhang';
$extra_css  = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/chitietdonhang.css?v=2">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";

$tt = $donhang['trangthai'] ?? '';
$badge_class = match($tt) {
    'Chờ xác nhận' => 'badge-pending',
    'Đã xác nhận'  => 'badge-confirmed',
    'Đang giao'    => 'badge-shipping',
    'Đã giao'      => 'badge-done',
    'Đã hủy'       => 'badge-cancelled',
    default        => 'badge-default',
};

// Build timeline steps
$steps = [
    ['label' => 'Chờ xác nhận', 'icon' => 'fas fa-hourglass-start'],
    ['label' => 'Đã xác nhận',  'icon' => 'fas fa-check'],
    ['label' => 'Đang giao',    'icon' => 'fas fa-truck'],
    ['label' => 'Đã giao',      'icon' => 'fas fa-box-open'],
];

$step_order = ['Chờ xác nhận' => 0, 'Đã xác nhận' => 1, 'Đang giao' => 2, 'Đã giao' => 3];
$current_step = $step_order[$tt] ?? -1;
$is_cancelled = ($tt === 'Đã hủy');
?>

<!-- Page Header -->
<div class="ct-page-header">
    <div class="ct-page-header-inner">
        <div class="ct-page-left">
            <nav class="ct-breadcrumb">
                <a href="donhang.php">Đơn hàng</a>
                <span>›</span>
                <span>Chi tiết</span>
            </nav>
            <h1 class="ct-page-title">
                Đơn hàng <span class="ct-order-num">#<?= e($donhang['madh']) ?></span>
            </h1>
        </div>
        <span class="dh-badge <?= $badge_class ?>"><?= e($tt) ?></span>
    </div>
</div>

<main class="container">

    <!-- Order Info Card -->
    <div class="ct-info-card">
        <div class="ct-info-card-header">
            <div>
                <div class="ct-info-card-title">Thông tin đơn hàng</div>
                <div class="ct-info-card-id">
                    Đơn <span>#<?= e($donhang['madh']) ?></span>
                </div>
            </div>
            <span class="dh-badge <?= $badge_class ?>"><?= e($tt) ?></span>
        </div>
        <div class="ct-info-grid">
            <div class="ct-info-item">
                <span class="ct-info-label">Khách hàng</span>
                <span class="ct-info-value"><?= e($donhang['tenkh']) ?></span>
            </div>
            <div class="ct-info-item">
                <span class="ct-info-label">Số điện thoại</span>
                <span class="ct-info-value"><?= e($donhang['sdt']) ?></span>
            </div>
            <div class="ct-info-item">
                <span class="ct-info-label">Địa chỉ</span>
                <span class="ct-info-value"><?= e($donhang['diachi']) ?></span>
            </div>
            <div class="ct-info-item">
                <span class="ct-info-label">Ngày đặt hàng</span>
                <span class="ct-info-value"><?= date('d/m/Y', strtotime($donhang['ngaydat'])) ?></span>
            </div>
            <?php if (!empty($donhang['tennv'])): ?>
            <div class="ct-info-item">
                <span class="ct-info-label">Nhân viên xử lý</span>
                <span class="ct-info-value"><?= e($donhang['tennv']) ?></span>
            </div>
            <?php endif; ?>
            <div class="ct-info-item ct-highlight">
                <span class="ct-info-label">Tổng tiền</span>
                <span class="ct-info-value ct-price-value"><?= formatMoney($donhang['trigia']) ?></span>
            </div>
        </div>
    </div>

    <!-- Status Timeline -->
    <?php if (!$is_cancelled): ?>
    <div class="ct-timeline">
        <div class="ct-timeline-title"><i class="fas fa-route"></i> Trạng thái đơn hàng</div>
        <div class="ct-timeline-steps">
            <?php foreach ($steps as $idx => $step):
                $cls = '';
                if ($idx < $current_step)      $cls = 'done';
                elseif ($idx === $current_step) $cls = 'active';
            ?>
            <div class="ct-step <?= $cls ?>">
                <div class="ct-step-dot"><i class="<?= $step['icon'] ?>"></i></div>
                <div class="ct-step-label"><?= $step['label'] ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Product Table -->
    <p class="ct-section-title"><i class="fas fa-box"></i> Sản phẩm trong đơn</p>

    <div class="ct-table-wrap">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên sản phẩm</th>
                    <th>Hãng</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($chitiet) > 0): ?>
                    <?php foreach ($chitiet as $i => $ct): ?>
                        <?php $sl = $ct['soluong'] ?? $ct['sl'] ?? 0; ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= e($ct['tensp']) ?></td>
                            <td><?= e($ct['hang']) ?></td>
                            <td><?= formatMoney($ct['gia']) ?></td>
                            <td><?= e($sl) ?></td>
                            <td class="ct-td-subtotal"><?= formatMoney($ct['gia'] * $sl) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="ct-empty">
                            <i class="fas fa-box-open" style="font-size:32px;display:block;margin-bottom:8px;opacity:.3"></i>
                            Không có sản phẩm trong đơn hàng này
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">Tổng cộng</td>
                    <td class="ct-td-subtotal"><?= formatMoney($donhang['trigia']) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Actions -->
    <div class="ct-actions">
        <a href="donhang.php" class="ct-btn ct-btn-back">
            <i class="fas fa-arrow-left"></i> Quay lại
        </a>
        <?php if (isAdminOrStaff()): ?>
            <a href="donhang_edit.php?madh=<?= e($donhang['madh']) ?>" class="ct-btn ct-btn-primary">
                <i class="fas fa-pen"></i> Cập nhật đơn
            </a>
        <?php endif; ?>
    </div>

</main>

<?php include "../../includes/footer.php"; ?>