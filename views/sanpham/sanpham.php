<?php
/**
 * Quản lý Sản phẩm - Danh sách
 */
session_start();
require_once "../../includes/api_helper.php";

// Auth check
requireLogin();

// Lấy danh sách sản phẩm từ RESTful API
$result = callAPI('GET', '/api/sanpham');
$products = ($result && $result['status']) ? $result['data'] : [];
$tong_sp = count($products);

// Lấy role từ session
$can_edit = isAdminOrStaff();

// Flash messages
$success = getFlash('success');
$error = getFlash('error');

// Header variables
$page_title = 'Quản lý Sản phẩm';
$active_nav = 'sanpham';
$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/sanpham.css?v=' . time() . '">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";

// Đường dẫn ảnh
$img_base = '/QLShopDT_API/includes/img/';
?>

<main class="container">
    <h1>QUẢN LÝ SẢN PHẨM</h1>
    
    <?php if ($success): ?>
        <div class="sp-alert sp-alert-success"><?= e($success) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="sp-alert sp-alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="sp-table-wrapper">
        <table class="sp-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Hình ảnh</th>
                    <th>Tên sản phẩm</th>
                    <th>Giá</th>
                    <th>SL</th>
                    <th>Danh mục</th>
                    <th>Hãng</th>
                    <?php if ($can_edit): ?>
                        <th><a href="sanpham_add.php" class="sp-btn-add">+ Thêm SP</a></th>
                    <?php else: ?>
                        <th>Thao tác</th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php if (count($products) > 0): ?>
                    <?php foreach ($products as $i => $sp): ?>
                    <tr>
                        <td class="sp-stt"><?= $i + 1 ?></td>
                        <td class="sp-img">
                            <?php if (!empty($sp['hinhanh'])): ?>
                                <img src="<?= $img_base . e($sp['hinhanh']) ?>" alt="<?= e($sp['tensp']) ?>">
                            <?php else: ?>
                                <div class="sp-no-img">No img</div>
                            <?php endif; ?>
                        </td>
                        <td class="sp-name"><?= e($sp['tensp']) ?></td>
                        <td class="sp-price"><?= number_format($sp['gia'], 0, ',', '.') ?>₫</td>
                        <td class="sp-qty"><?= (int)$sp['sl'] ?></td>
                        <td class="sp-cat"><?= e($sp['tendm'] ?? 'N/A') ?></td>
                        <td class="sp-brand"><?= e($sp['hang']) ?></td>
                        <td class="sp-actions">
                            <a href="chitietsanpham.php?masp=<?= $sp['masp'] ?>" class="sp-btn sp-btn-detail">Chi tiết</a>
                            <?php if ($can_edit): ?>
                                <a href="sanpham_edit.php?masp=<?= $sp['masp'] ?>" class="sp-btn sp-btn-edit">Sửa</a>
                                <a href="sanpham_del.php?masp=<?= $sp['masp'] ?>" class="sp-btn sp-btn-del"
                                   onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">Xóa</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="<?= $can_edit ? '8' : '8' ?>" class="sp-empty-state">
                            <div class="sp-empty-icon">📦</div>
                            <strong>Chưa có sản phẩm nào</strong>
                            <p>Hãy thêm sản phẩm đầu tiên của bạn</p>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="8">Tổng số: <strong><?= $tong_sp ?></strong> sản phẩm</td>
                </tr>
            </tfoot>
        </table>
    </div>
</main>

<?php include "../../includes/footer.php"; ?>