<?php
/**
 * Quản lý Nhân viên - Danh sách
 */
session_start();
require_once "../../includes/api_helper.php";

requireLogin();
requireRole([1]);

$keyword  = trim($_GET['keyword'] ?? '');
$params   = $keyword !== '' ? ['keyword' => $keyword] : [];

$result    = callAPI('GET', '/api/nhanvien', $params);
$employees = ($result && $result['status']) ? $result['data'] : [];
$tong_nv   = count($employees);

$success = getFlash('success');
$error   = getFlash('error');

$page_title = 'Quản lý Nhân viên';
$active_nav = 'nhanvien';
$extra_css  = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/nhanvien.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";
?>

<main class="container">

    <div class="nv-toolbar">
        <h1>QUẢN LÝ NHÂN VIÊN</h1>

        <form method="GET" action="nhanvien.php" class="nv-search">
            <input type="text" name="keyword"
                   placeholder="Tìm theo tên, SĐT..."
                   value="<?= e($keyword) ?>">
            <button type="submit">Tìm</button>
            <?php if ($keyword !== ''): ?>
                <a href="nhanvien.php" class="dm-btn dm-btn-default" style="padding:9px 14px; font-size:13px;">✕</a>
            <?php endif; ?>
        </form>

        <a href="nhanvien_add.php" class="nv-add-btn">+ Thêm nhân viên</a>
    </div>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= e($success) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if ($keyword !== ''): ?>
        <p style="margin-bottom:12px; color:var(--dm-muted); font-size:14px;">
            Kết quả cho "<strong><?= e($keyword) ?></strong>": <?= $tong_nv ?> nhân viên
        </p>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>STT</th>
                <th>Mã NV</th>
                <th>Tên nhân viên</th>
                <th>Số điện thoại</th>
                <th>Ngày sinh</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($tong_nv > 0): ?>
                <?php foreach ($employees as $i => $nv): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($nv['manv']) ?></td>
                        <td style="text-align:left; font-weight:600;"><?= e($nv['tennv']) ?></td>
                        <td><?= e($nv['sdt']) ?></td>
                        <td><?= $nv['ns'] ? date('d/m/Y', strtotime($nv['ns'])) : '—' ?></td>
                        <td>
                            <a href="nhanvien_edit.php?manv=<?= e($nv['manv']) ?>">Sửa</a>
                            <a href="nhanvien_del.php?manv=<?= e($nv['manv']) ?>"
                               onclick="return confirm('Bạn có chắc muốn xóa nhân viên \"<?= e($nv['tennv']) ?>\"?')">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="nv-empty">
                        <strong><?= $keyword !== '' ? 'Không tìm thấy nhân viên nào' : 'Chưa có nhân viên nào' ?></strong>
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="6">
                    Tổng số: <strong><?= $tong_nv ?></strong> nhân viên
                </td>
            </tr>
        </tfoot>
    </table>

</main>

<?php include "../../includes/footer.php"; ?>
