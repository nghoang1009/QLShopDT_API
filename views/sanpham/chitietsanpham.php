<?php
/**
 * Chi tiết sản phẩm + Thông số kỹ thuật
 */
session_start();
require_once "../../includes/api_helper.php";

// Auth check
requireLogin();

$masp = $_GET['masp'] ?? 0;

// Lấy thông tin sản phẩm từ RESTful API
$result = callAPI('GET', '/api/sanpham/' . (int)$masp);

if (!$result || !$result['status']) {
    setFlash('error', 'Không tìm thấy sản phẩm');
    header("Location: sanpham.php");
    exit();
}

$sp = $result['data'];

// Lấy thông số kỹ thuật của sản phẩm
$ts_result = callAPI('GET', '/api/thongso', ['masp' => $masp]);
$thongso_list = ($ts_result && $ts_result['status']) ? $ts_result['data'] : [];

// Quyền chỉnh sửa
$can_edit = isAdminOrStaff();

// Flash messages
$success = getFlash('success');
$error = getFlash('error');

// Header variables
$page_title = 'Chi tiết: ' . $sp['tensp'];
$active_nav = 'sanpham';
$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/sanpham.css?v=' . time() . '">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";

// Đường dẫn ảnh
$img_base = '/QLShopDT_API/includes/img/';
?>

<main class="container">
    <h1>CHI TIẾT SẢN PHẨM</h1>
    
    <?php if ($success): ?>
        <div class="sp-alert sp-alert-success"><?= e($success) ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="sp-alert sp-alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="sp-detail-wrapper">
        <!-- Thông tin sản phẩm -->
        <div class="sp-detail-card">
            <div class="sp-detail-header">
                <span class="sp-detail-id">#<?= e($sp['masp']) ?></span>
                <h2><?= e($sp['tensp']) ?></h2>
            </div>
            
            <div class="sp-detail-body">
                <div class="sp-detail-img">
                    <?php if (!empty($sp['hinhanh'])): ?>
                        <img src="<?= $img_base . e($sp['hinhanh']) ?>" alt="<?= e($sp['tensp']) ?>">
                    <?php else: ?>
                        <div class="sp-no-img-large">Chưa có ảnh</div>
                    <?php endif; ?>
                </div>
                
                <div class="sp-detail-info">
                    <div class="sp-info-row">
                        <span class="sp-info-label">Giá bán:</span>
                        <span class="sp-info-value sp-price-large"><?= number_format($sp['gia'], 0, ',', '.') ?>₫</span>
                    </div>
                    <div class="sp-info-row">
                        <span class="sp-info-label">Số lượng tồn:</span>
                        <span class="sp-info-value <?= ($sp['sl'] <= 5) ? 'sp-low-stock' : '' ?>"><?= (int)$sp['sl'] ?> sản phẩm</span>
                    </div>
                    <div class="sp-info-row">
                        <span class="sp-info-label">Danh mục:</span>
                        <span class="sp-info-value"><?= e($sp['tendm'] ?? 'Chưa phân loại') ?></span>
                    </div>
                    <div class="sp-info-row">
                        <span class="sp-info-label">Hãng sản xuất:</span>
                        <span class="sp-info-value"><?= e($sp['hang']) ?: 'N/A' ?></span>
                    </div>
                    <div class="sp-info-row">
                        <span class="sp-info-label">Bảo hành:</span>
                        <span class="sp-info-value"><?= (int)$sp['baohanh'] ?> tháng</span>
                    </div>
                    <?php if (!empty($sp['ghichu'])): ?>
                    <div class="sp-info-row sp-info-note">
                        <span class="sp-info-label">Ghi chú:</span>
                        <span class="sp-info-value"><?= nl2br(e($sp['ghichu'])) ?></span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="sp-detail-footer">
                <?php if ($can_edit): ?>
                    <a href="sanpham_edit.php?masp=<?= $sp['masp'] ?>" class="sp-btn sp-btn-edit-lg">Sửa sản phẩm</a>
                <?php endif; ?>
                <?php if (!$can_edit && isset($_SESSION['role']) && $_SESSION['role'] == 0): ?>
                    <form method="POST" action="/QLShopDT_API/app.php/giohang/add" style="display:inline;">
                        <input type="hidden" name="masp" value="<?= (int)$sp['masp'] ?>">
                        <input type="hidden" name="soluong" value="1">
                        <button type="submit" class="sp-btn sp-btn-edit-lg"
                                style="background:#e94560;border:none;cursor:pointer;color:#fff;">
                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                        </button>
                    </form>
                <?php endif; ?>
                <a href="sanpham.php" class="sp-btn sp-btn-default">Quay lại danh sách</a>
            </div>
        </div>
        
        <!-- Thông số kỹ thuật -->
        <div class="sp-specs-card">
            <div class="sp-specs-header">
                <h3>Thông số kỹ thuật</h3>
                <?php if ($can_edit): ?>
                    <a href="../thongso/thongso.php?masp=<?= $sp['masp'] ?>" class="sp-btn sp-btn-add-spec">+ Quản lý</a>
                <?php endif; ?>
            </div>
            
            <div class="sp-specs-body">
                <?php if (count($thongso_list) > 0): ?>
                    <table class="sp-specs-table">
                        <tbody>
                            <?php foreach ($thongso_list as $ts): ?>
                            <tr>
                                <td class="sp-spec-name"><?= e($ts['tents']) ?></td>
                                <td class="sp-spec-value"><?= e($ts['giatri']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="sp-specs-empty">
                        <p>Chưa có thông số kỹ thuật nào</p>
                        <?php if ($can_edit): ?>
                            <a href="../thongso/thongso.php?masp=<?= $sp['masp'] ?>">Thêm ngay</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include "../../includes/footer.php"; ?>
