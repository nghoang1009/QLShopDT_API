<?php
/**
 * Quản lý Thông số kỹ thuật - Danh sách
 */
session_start();
require_once "../../includes/api_helper.php";

// Auth check
requireLogin();

$masp = $_GET['masp'] ?? $_REQUEST['masp'] ?? '';

if (empty($masp)) {
    setFlash('error', 'Không tìm thấy sản phẩm');
    header("Location: ../sanpham/sanpham.php");
    exit();
}

// Lấy thông tin sản phẩm
$sp_result = callAPI('GET', '/api/sanpham/' . $masp);
$sanpham = ($sp_result && $sp_result['status']) ? $sp_result['data'] : null;

if (!$sanpham) {
    setFlash('error', 'Không tìm thấy sản phẩm');
    header("Location: ../sanpham/sanpham.php");
    exit();
}

// Lấy danh sách thông số từ API
$result = callAPI('GET', '/api/thongso', ['masp' => $masp]);
$thongsos = ($result && $result['status']) ? $result['data'] : [];
$tong_ts = count($thongsos);

// Quyền chỉnh sửa
$can_edit = isAdminOrStaff();

// Flash messages
$success = getFlash('success');
$error = getFlash('error');

// Header variables
$page_title = 'Thông số: ' . $sanpham['tensp'];
$active_nav = 'sanpham';
$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/thongso.css?v=' . time() . '">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";
?>

<main class="container">
    <h1>THÔNG SỐ KỸ THUẬT</h1>
    
    <!-- Product Info Card -->
    <div class="ts-product-card">
        <div class="ts-product-info">
            <div class="ts-product-icon">📱</div>
            <div class="ts-product-details">
                <h3><?= e($sanpham['tensp']) ?></h3>
                <p>Mã SP: #<?= e($masp) ?> | Hãng: <?= e($sanpham['hang'] ?: 'N/A') ?></p>
            </div>
        </div>
        <a href="../sanpham/chitietsanpham.php?masp=<?= $masp ?>" class="ts-back-link">Quay lại chi tiết</a>
    </div>
    
    <?php if ($success): ?>
        <div class="ts-alert ts-alert-success"><?= e($success) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="ts-alert ts-alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="ts-table-wrapper">
        <table class="ts-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Tên thông số</th>
                    <th>Giá trị</th>
                    <?php if ($can_edit): ?>
                    <th class="ts-header-add">
                        <a href="thongso_add.php?masp=<?= $masp ?>" class="ts-btn ts-btn-add">+ Thêm mới</a>
                    </th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if ($tong_ts > 0): ?>
                    <?php foreach ($thongsos as $i => $ts): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($ts['tents']) ?></td>
                        <td><?= e($ts['giatri']) ?></td>
                        <?php if ($can_edit): ?>
                        <td>
                            <div class="ts-actions">
                                <a href="thongso_edit.php?mats=<?= $ts['mats'] ?>&masp=<?= $masp ?>" class="ts-btn ts-btn-edit">Sửa</a>
                                <a href="thongso_del.php?mats=<?= $ts['mats'] ?>&masp=<?= $masp ?>" 
                                   class="ts-btn ts-btn-del"
                                   onclick="return confirm('Bạn có chắc muốn xóa thông số này?')">Xóa</a>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $can_edit ? '4' : '3' ?>" class="ts-empty-state">
                            <div class="ts-empty-icon">📋</div>
                            <strong>Chưa có thông số nào</strong>
                            <p>Thêm thông số kỹ thuật để mô tả chi tiết sản phẩm</p>
                            <?php if ($can_edit): ?>
                                <a href="thongso_add.php?masp=<?= $masp ?>" class="ts-btn ts-btn-add">+ Thêm thông số đầu tiên</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <?php if ($tong_ts > 0): ?>
            <tfoot>
                <tr>
                    <td colspan="<?= $can_edit ? '4' : '3' ?>">
                        Tổng số: <strong><?= $tong_ts ?></strong> thông số
                    </td>
                </tr>
            </tfoot>
            <?php endif; ?>
        </table>
    </div>
</main>

<?php include "../../includes/footer.php"; ?>