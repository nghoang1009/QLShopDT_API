<?php
/**
 * Thống kê tổng quan - Dashboard
 * Được render qua ThongKeController@index
 * Biến có sẵn: $overview, $topProducts, $topCustomers, $ordersByStatus, $revenueByCategory
 */
require_once BASE_PATH . '/includes/api_helper.php';

$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/thongke.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include BASE_PATH . '/includes/header.php';

/* Hàm tính rank badge */
function rankBadge(int $i): string {
    if ($i === 1) return '<span class="tk-rank gold">1</span>';
    if ($i === 2) return '<span class="tk-rank silver">2</span>';
    if ($i === 3) return '<span class="tk-rank bronze">3</span>';
    return '<span class="tk-rank other">' . $i . '</span>';
}

/* Màu bar theo trạng thái đơn hàng */
function statusColor(string $s): string {
    $map = [
        'Đã giao'      => '#10b981',
        'Đang giao'    => '#3b82f6',
        'Chờ xác nhận' => '#f59e0b',
        'Đã hủy'       => '#ef4444',
    ];
    return $map[$s] ?? '#6b7280';
}

/* Màu badge theo trạng thái */
function statusBadgeClass(string $s): string {
    $map = [
        'Đã giao'      => 'tk-badge-green',
        'Đang giao'    => 'tk-badge-blue',
        'Chờ xác nhận' => 'tk-badge-yellow',
        'Đã hủy'       => 'tk-badge-red',
    ];
    return $map[$s] ?? 'tk-badge-gray';
}

$totalStatus = array_sum(array_column($ordersByStatus ?? [], 'so_luong'));
?>

<div class="tk-dashboard">
    <h1 class="tk-page-title">Thống kê tổng quan</h1>

    <!-- ===== KPI CARDS ===== -->
    <div class="tk-kpi-grid">
        <div class="tk-kpi-card">
            <div class="tk-kpi-icon revenue">💰</div>
            <div class="tk-kpi-info">
                <div class="tk-kpi-value"><?= formatMoney($overview['tong_doanhthu'] ?? 0) ?></div>
                <div class="tk-kpi-label">Doanh thu (đã giao)</div>
            </div>
        </div>
        <div class="tk-kpi-card">
            <div class="tk-kpi-icon orders">📦</div>
            <div class="tk-kpi-info">
                <div class="tk-kpi-value"><?= number_format($overview['tong_donhang'] ?? 0) ?></div>
                <div class="tk-kpi-label">Tổng đơn hàng</div>
            </div>
        </div>
        <div class="tk-kpi-card">
            <div class="tk-kpi-icon customers">👥</div>
            <div class="tk-kpi-info">
                <div class="tk-kpi-value"><?= number_format($overview['tong_khachhang'] ?? 0) ?></div>
                <div class="tk-kpi-label">Khách hàng</div>
            </div>
        </div>
        <div class="tk-kpi-card">
            <div class="tk-kpi-icon products">📱</div>
            <div class="tk-kpi-info">
                <div class="tk-kpi-value"><?= number_format($overview['tong_sanpham'] ?? 0) ?></div>
                <div class="tk-kpi-label">Sản phẩm</div>
            </div>
        </div>
    </div><!-- /kpi-grid -->

    <!-- ===== ROW 1: Top sản phẩm + Trạng thái đơn hàng ===== -->
    <div class="tk-grid-2">

        <!-- Top sản phẩm bán chạy -->
        <div class="tk-panel">
            <div class="tk-panel-title">Top 5 sản phẩm bán chạy</div>
            <div class="tk-panel-body">
                <?php if (!empty($topProducts)): ?>
                <table class="tk-ptable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Sản phẩm</th>
                            <th style="text-align:right">SL bán</th>
                            <th style="text-align:right">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($topProducts as $i => $sp): ?>
                        <tr>
                            <td><?= rankBadge($i + 1) ?></td>
                            <td>
                                <div style="font-weight:600;line-height:1.3"><?= e($sp['tensp']) ?></div>
                                <div style="font-size:.75rem;color:var(--tk-muted)"><?= e($sp['hang']) ?></div>
                            </td>
                            <td style="text-align:right;font-weight:700"><?= number_format($sp['so_luong_ban']) ?></td>
                            <td style="text-align:right;color:var(--tk-accent);font-weight:600"><?= formatMoney($sp['doanhthu']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="tk-empty">Chưa có dữ liệu</div>
                <?php endif; ?>
            </div>
        </div><!-- /top products -->

        <!-- Trạng thái đơn hàng -->
        <div class="tk-panel">
            <div class="tk-panel-title">Trạng thái đơn hàng</div>
            <div class="tk-panel-body">
                <?php if (!empty($ordersByStatus)): ?>
                <div class="tk-status-list">
                    <?php foreach ($ordersByStatus as $row):
                        $pct = $totalStatus > 0 ? round($row['so_luong'] / $totalStatus * 100) : 0;
                        $color = statusColor($row['trangthai']);
                    ?>
                    <div class="tk-status-item">
                        <div class="tk-status-header">
                            <span class="tk-status-name">
                                <span class="tk-badge <?= statusBadgeClass($row['trangthai']) ?>"><?= e($row['trangthai']) ?></span>
                            </span>
                            <span class="tk-status-count"><?= number_format($row['so_luong']) ?> đơn &nbsp;(<?= $pct ?>%)</span>
                        </div>
                        <div class="tk-bar-bg">
                            <div class="tk-bar-fill" style="width:<?= $pct ?>%;background:<?= $color ?>"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                    <div class="tk-empty">Chưa có dữ liệu</div>
                <?php endif; ?>
            </div>
        </div><!-- /orders by status -->

    </div><!-- /row 1 -->

    <!-- ===== ROW 2: Top khách hàng + Doanh thu theo danh mục ===== -->
    <div class="tk-grid-2">

        <!-- Top khách hàng -->
        <div class="tk-panel">
            <div class="tk-panel-title">Top 5 khách hàng</div>
            <div class="tk-panel-body">
                <?php if (!empty($topCustomers)): ?>
                <table class="tk-ptable">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Khách hàng</th>
                            <th style="text-align:right">Đơn hàng</th>
                            <th style="text-align:right">Tổng chi</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($topCustomers as $i => $kh): ?>
                        <tr>
                            <td><?= rankBadge($i + 1) ?></td>
                            <td>
                                <div style="font-weight:600"><?= e($kh['tenkh']) ?></div>
                                <div style="font-size:.75rem;color:var(--tk-muted)"><?= e($kh['sdt']) ?></div>
                            </td>
                            <td style="text-align:right;font-weight:700"><?= number_format($kh['so_don']) ?></td>
                            <td style="text-align:right;color:var(--tk-accent);font-weight:600"><?= formatMoney($kh['tong_chi']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="tk-empty">Chưa có dữ liệu</div>
                <?php endif; ?>
            </div>
        </div><!-- /top customers -->

        <!-- Doanh thu theo danh mục -->
        <div class="tk-panel">
            <div class="tk-panel-title">Doanh thu theo danh mục</div>
            <div class="tk-panel-body">
                <?php if (!empty($revenueByCategory)): ?>
                <table class="tk-ptable">
                    <thead>
                        <tr>
                            <th>Danh mục</th>
                            <th style="text-align:right">SL bán</th>
                            <th style="text-align:right">Đơn</th>
                            <th style="text-align:right">Doanh thu</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($revenueByCategory as $dm): ?>
                        <tr>
                            <td style="font-weight:600"><?= e($dm['tendm']) ?></td>
                            <td style="text-align:right"><?= number_format($dm['so_luong_ban']) ?></td>
                            <td style="text-align:right"><?= number_format($dm['so_don']) ?></td>
                            <td style="text-align:right;color:var(--tk-accent);font-weight:600"><?= formatMoney($dm['doanhthu']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="tk-empty">Chưa có dữ liệu</div>
                <?php endif; ?>
            </div>
        </div><!-- /revenue by category -->

    </div><!-- /row 2 -->

</div><!-- /tk-dashboard -->

<?php include BASE_PATH . '/includes/footer.php'; ?>
