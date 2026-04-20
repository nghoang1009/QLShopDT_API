<?php
/**
 * Quản lý Khách hàng - Danh sách
 */
session_start();
require_once "../../includes/api_helper.php";

requireLogin();
requireRole([1, 2]);

$keyword = trim($_GET['keyword'] ?? '');
$params  = $keyword !== '' ? ['keyword' => $keyword] : [];

$result    = callAPI('GET', '/api/khachhang', $params);
$customers = ($result && $result['status']) ? $result['data'] : [];
$tong_kh   = count($customers);

$success = getFlash('success');
$error   = getFlash('error');

$page_title = 'Quản lý Khách hàng';
$active_nav = 'khachhang';
$extra_css  = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/khachhang.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";
?>

<main class="container">

    <div class="kh-toolbar">
        <h1>QUẢN LÝ KHÁCH HÀNG</h1>

        <form method="GET" action="khachhang.php" class="kh-search">
            <input type="text" name="keyword"
                   placeholder="Tìm theo tên, SĐT, địa chỉ..."
                   value="<?= e($keyword) ?>">
            <button type="submit">Tìm</button>
            <?php if ($keyword !== ''): ?>
                <a href="khachhang.php" class="dm-btn dm-btn-default" style="padding:9px 14px; font-size:13px;">✕</a>
            <?php endif; ?>
        </form>

        <a href="khachhang_add.php" class="kh-add-btn">+ Thêm khách hàng</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if ($keyword !== ''): ?>
        <p style="margin-bottom:12px; color:var(--dm-muted); font-size:14px;">
            Kết quả cho "<strong><?= e($keyword) ?></strong>": <?= $tong_kh ?> khách hàng
        </p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã KH</th>
                <th>Tên khách hàng</th>
                <th>Địa chỉ</th>
                <th>Số điện thoại</th>
                <th>Tên tài khoản</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($tong_kh > 0): ?>
                <?php foreach ($customers as $i => $kh): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($kh['makh']) ?></td>
                        <td style="text-align:left; font-weight:600;"><?= e($kh['tenkh']) ?></td>
                        <td style="text-align:left; color:var(--dm-muted); font-size:13px;"><?= e($kh['diachi']) ?></td>
                        <td><?= e($kh['sdt']) ?></td>
                        <td style="font-size:13px; color:var(--dm-muted);"><?= e($kh['tentk'] ?? '—') ?></td>
                        <td>
                            <a href="khachhang_edit.php?makh=<?= e($kh['makh']) ?>">Sửa</a>
                            <a href="khachhang_del.php?makh=<?= e($kh['makh']) ?>"
                               onclick="return confirm('Bạn có chắc muốn xóa khách hàng \"<?= e($kh['tenkh']) ?>\"?')">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7" class="kh-empty">
                        <strong><?= $keyword !== '' ? 'Không tìm thấy khách hàng nào' : 'Chưa có khách hàng nào' ?></strong>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="7">
                    Tổng số: <strong><?= $tong_kh ?></strong> khách hàng
                </td>
            </tr>
        </tfoot>
    </table>

</main>

<?php include "../../includes/footer.php"; ?>