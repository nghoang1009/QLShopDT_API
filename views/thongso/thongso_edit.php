<?php
/**
 * Sửa thông số kỹ thuật
 */
session_start();
require_once "../../includes/api_helper.php";

// Auth check
requireLogin();
requireRole([1, 2]); // Admin hoặc Nhân viên

$mats = $_GET['mats'] ?? $_POST['mats'] ?? 0;
$masp = $_GET['masp'] ?? $_POST['masp'] ?? 0;

// Xử lý POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $tents  = trim($_POST['tents'] ?? '');
    $giatri = trim($_POST['giatri'] ?? '');
    
    if (empty($tents)) {
        setFlash('error', 'Vui lòng nhập tên thông số');
    } else {
        $result = callAPI('PUT', '/api/thongso/' . $mats, [
            'tents'  => $tents,
            'masp'   => $masp,
            'giatri' => $giatri
        ]);
        
        if ($result && $result['status']) {
            setFlash('success', 'Cập nhật thông số thành công');
            header("Location: thongso.php?masp=$masp");
            exit();
        }
        setFlash('error', $result['message'] ?? 'Lỗi không xác định');
    }
    header("Location: thongso_edit.php?mats=$mats&masp=$masp");
    exit();
}

// Lấy thông tin thông số
$result = callAPI('GET', '/api/thongso/' . $mats);

if (!$result || !$result['status']) {
    setFlash('error', 'Không tìm thấy thông số');
    header("Location: thongso.php?masp=$masp");
    exit();
}

$ts = $result['data'];
$masp = $ts['masp']; // Ghi đè từ DB

// Lấy thông tin sản phẩm
$sp_result = callAPI('GET', '/api/sanpham/' . $masp);
$sanpham = ($sp_result && $sp_result['status']) ? $sp_result['data'] : null;

// Flash messages
$error = getFlash('error');

// Header variables
$page_title = 'Sửa thông số';
$active_nav = 'sanpham';
$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/thongso.css?v=' . time() . '">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";
?>

<main class="container">
    <h1>SỬA THÔNG SỐ</h1>
    
    <?php if ($error): ?>
        <div class="ts-alert ts-alert-error"><?= e($error) ?></div>
    <?php endif; ?>
    
    <form method="POST" class="ts-form">
        <?= csrf_field() ?>
        <input type="hidden" name="mats" value="<?= e($mats) ?>">
        <input type="hidden" name="masp" value="<?= e($masp) ?>">
        
        <div class="ts-id-badge">
            <small>Mã thông số</small>
            <strong>#<?= e($mats) ?></strong>
        </div>
        
        <div class="ts-product-badge-form">
            <span>Sản phẩm:</span>
            <strong><?= e($sanpham['tensp'] ?? 'Sản phẩm #' . $masp) ?></strong>
        </div>
        
        <div class="ts-form-group">
            <label for="tents" class="ts-label">
                Tên thông số <span class="ts-required">*</span>
            </label>
            <input type="text" id="tents" name="tents" class="ts-input" 
                   value="<?= e($ts['tents']) ?>" required>
        </div>
        
        <div class="ts-form-group">
            <label for="giatri" class="ts-label">Giá trị</label>
            <textarea id="giatri" name="giatri" class="ts-input ts-textarea" rows="3"><?= e($ts['giatri']) ?></textarea>
        </div>
        
        <div class="ts-form-actions">
            <button type="submit" class="ts-btn-primary">Cập nhật</button>
            <button type="reset" class="ts-btn-secondary">Đặt lại</button>
            <a href="thongso.php?masp=<?= $masp ?>" class="ts-btn-default">Quay lại</a>
        </div>
    </form>
</main>

<?php include "../../includes/footer.php"; ?>