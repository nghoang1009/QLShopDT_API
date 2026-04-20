<?php
/**
 * Giỏ hàng - views/giohang/index.php
 */

$extra_css = '<link rel="stylesheet" href="' . BASE_URL . '/assets/css/giohang.css">
<link rel="stylesheet" href="' . BASE_URL . '/assets/css/footer.css">';

include BASE_PATH . '/includes/header.php';

$role      = $role ?? 0;
$items     = $items ?? [];
$total     = $total ?? 0;
$imgBase   = BASE_URL . '/includes/img/';
$appUrl    = BASE_URL . '/app.php';

// ── Hàm format tiền ────────────────────────────────────────────────────
function fmtVnd($n) {
    return number_format((float)$n, 0, ',', '.') . ' ₫';
}
?>

<h1>GIỎ HÀNG</h1>

<?php if (!empty($error)): ?>
    <p style="text-align:center; color:#ef4444; font-weight:600;">
        <i class="fas fa-exclamation-circle"></i> <?= e($error) ?>
    </p>
<?php endif; ?>

<?php /* ==================== KHÁCH HÀNG (role=0) ==================== */ ?>
<?php if ($role === 0): ?>

    <?php if (empty($items)): ?>
        <div class="cart-empty">
            <i class="fas fa-shopping-cart"></i>
            <p>Giỏ hàng của bạn đang trống</p>
            <a href="<?= $appUrl ?>/"><i class="fas fa-store"></i> Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>

        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Hãng</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                    <th>Xóa</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $i => $item): ?>
                <tr>
                    <td><?= $i + 1 ?></td>
                    <td>
                        <?php
                            $img = !empty($item['hinhanh'])
                                ? $imgBase . e($item['hinhanh'])
                                : $imgBase . 'default.png';
                        ?>
                        <img src="<?= $img ?>" alt="<?= e($item['tensp']) ?>">
                    </td>
                    <td><?= e($item['tensp']) ?></td>
                    <td><?= e($item['hang']) ?></td>
                    <td><?= fmtVnd($item['gia']) ?></td>
                    <td>
                        <form method="POST" action="<?= $appUrl ?>/giohang/update" style="display:flex;align-items:center;gap:5px;justify-content:center;">
                            <input type="hidden" name="masp" value="<?= (int)$item['masp'] ?>">
                            <input type="number" name="soluong" value="<?= (int)$item['sl'] ?>" min="1" max="999">
                            <button type="submit"><i class="fas fa-sync-alt"></i></button>
                        </form>
                    </td>
                    <td><?= fmtVnd($item['thanhtien']) ?></td>
                    <td>
                        <a href="<?= $appUrl ?>/giohang/remove/<?= (int)$item['masp'] ?>"
                           onclick="return confirm('Xóa sản phẩm này khỏi giỏ hàng?')">
                            <i class="fas fa-trash-alt"></i> Xóa
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align:right;">Tổng cộng:</td>
                    <td colspan="2" style="color:var(--dm-accent);font-size:16px;"><?= fmtVnd($total) ?></td>
                </tr>
            </tfoot>
        </table>

        <div class="cart-actions">
            <a href="<?= $appUrl ?>/" class="btn-continue">
                <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
            </a>
            <a href="<?= $appUrl ?>/donhang/create" class="btn-checkout">
                <i class="fas fa-credit-card"></i> Đặt hàng &amp; Thanh toán
            </a>
        </div>

    <?php endif; ?>

<?php /* ==================== ADMIN / NHÂN VIÊN (role=1,2) ==================== */ ?>
<?php else: ?>

    <?php if (empty($items)): ?>
        <div class="cart-empty">
            <i class="fas fa-shopping-cart"></i>
            <p>Không có giỏ hàng nào trong hệ thống</p>
        </div>
    <?php else: ?>

        <?php
        // Nhóm items theo từng khách hàng
        $grouped = [];
        foreach ($items as $item) {
            $makh = $item['makh'];
            if (!isset($grouped[$makh])) {
                $grouped[$makh] = [
                    'tenkh' => $item['tenkh'],
                    'items' => [],
                    'subtotal' => 0,
                ];
            }
            $grouped[$makh]['items'][]  = $item;
            $grouped[$makh]['subtotal'] += $item['thanhtien'];
        }
        ?>

        <p style="text-align:center;color:var(--dm-muted);margin-bottom:24px;font-size:13px;">
            <i class="fas fa-info-circle"></i>
            Hiển thị giỏ hàng của <?= count($grouped) ?> khách hàng
            &nbsp;|&nbsp; Tổng giá trị: <strong style="color:var(--dm-accent)"><?= fmtVnd($total) ?></strong>
        </p>

        <?php foreach ($grouped as $makh => $group): ?>
            <div style="max-width:1400px;width:calc(100% - 48px);margin:0 auto 16px;">
                <div style="background:linear-gradient(135deg,#1a1a2e,#2d2d44);color:#fff;padding:10px 16px;border-radius:10px 10px 0 0;display:inline-flex;align-items:center;gap:8px;">
                    <i class="fas fa-user" style="opacity:0.7;font-size:12px;"></i>
                    <span style="font-weight:700;font-size:13px;"><?= e($group['tenkh']) ?></span>
                    <span style="opacity:0.5;font-weight:400;font-size:12px;">#<?= (int)$makh ?></span>
                </div>
            </div>

            <table style="margin-top:0;border-radius:0 0 10px 10px;margin-bottom:32px;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ảnh</th>
                        <th>Tên sản phẩm</th>
                        <th>Hãng</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th colspan="2">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($group['items'] as $i => $item): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <?php
                                $img = !empty($item['hinhanh'])
                                    ? $imgBase . e($item['hinhanh'])
                                    : $imgBase . 'default.png';
                            ?>
                            <img src="<?= $img ?>" alt="<?= e($item['tensp']) ?>">
                        </td>
                        <td><?= e($item['tensp']) ?></td>
                        <td><?= e($item['hang']) ?></td>
                        <td><?= fmtVnd($item['gia']) ?></td>
                        <td><?= (int)$item['sl'] ?></td>
                        <td colspan="2"><?= fmtVnd($item['thanhtien']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="6" style="text-align:right;">Tổng của <?= e($group['tenkh']) ?>:</td>
                        <td colspan="2" style="color:var(--dm-accent);"><?= fmtVnd($group['subtotal']) ?></td>
                    </tr>
                </tfoot>
            </table>
        <?php endforeach; ?>

    <?php endif; ?>

<?php endif; ?>

<?php include BASE_PATH . '/includes/footer.php'; ?>
