<?php
/**
 * Thêm thông số kỹ thuật mới
 */
session_start();
require_once "../../includes/api_helper.php";

// Auth check
requireLogin();
requireRole([1, 2]); // Admin hoặc Nhân viên

$masp = $_GET['masp'] ?? $_POST['masp'] ?? '';

// Lấy thông tin sản phẩm
$sp_result = callAPI('GET', '/api/sanpham/' . $masp);
$sanpham = ($sp_result && $sp_result['status']) ? $sp_result['data'] : null;

if (!$sanpham) {
    setFlash('error', 'Không tìm thấy sản phẩm');
    header("Location: ../sanpham/sanpham.php");
    exit();
}

// Xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $tents  = trim($_POST['tents'] ?? '');
    $giatri = trim($_POST['giatri'] ?? '');
    $masp   = $_POST['masp'] ?? '';
    
    if (empty($tents)) {
        setFlash('error', 'Vui lòng nhập tên thông số');
    } else {
        $result = callAPI('POST', '/api/thongso', [
            'tents'  => $tents,
            'masp'   => $masp,
            'giatri' => $giatri
        ]);
        
        if ($result && $result['status']) {
            setFlash('success', 'Thêm thông số thành công');
            header("Location: thongso.php?masp=$masp");
            exit();
        }
        setFlash('error', $result['message'] ?? 'Lỗi không xác định');
    }
    header("Location: thongso_add.php?masp=$masp");
    exit();
}

// Flash messages
$error = getFlash('error');

// Header variables
$page_title = 'Thêm thông số';
$active_nav = 'sanpham';
$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/thongso.css?v=' . time() . '">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";
?>

<main class="container">
    <h1>THÊM THÔNG SỐ</h1>
    
    <?php if ($error): ?>
        <div class="ts-alert ts-alert-error"><?= e($error) ?></div>
    <?php endif; ?>
    
    <form method="POST" class="ts-form">
        <?= csrf_field() ?>
        <input type="hidden" name="masp" value="<?= e($masp) ?>">
        
        <div class="ts-product-badge-form">
            <span>Sản phẩm:</span>
            <strong><?= e($sanpham['tensp']) ?> (#<?= e($masp) ?>)</strong>
        </div>
        
        <div class="ts-form-group">
            <label for="tents" class="ts-label">
                Tên thông số <span class="ts-required">*</span>
            </label>
            <input type="text" id="tents" name="tents" class="ts-input" 
                   placeholder="VD: Màn hình, CPU, RAM..." required>
        </div>
        
        <div class="ts-form-group">
            <label for="giatri" class="ts-label">Giá trị</label>
            <textarea id="giatri" name="giatri" class="ts-input ts-textarea" rows="3"
                      placeholder="VD: 6.7 inch AMOLED, Snapdragon 8 Gen 2..."></textarea>
        </div>
        
        <div class="ts-form-actions">
            <button type="submit" class="ts-btn-primary">Lưu</button>
            <button type="reset" class="ts-btn-secondary">Đặt lại</button>
            <a href="thongso.php?masp=<?= $masp ?>" class="ts-btn-default">Quay lại</a>
        </div>
    </form>
</main>

<?php include "../../includes/footer.php"; ?>