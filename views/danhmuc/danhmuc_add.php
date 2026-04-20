<?php
/**
 * Thêm danh mục mới
 */
session_start();
require_once "../../includes/api_helper.php";

// Auth check trước output
requireLogin();
requireRole([1, 2]); // Admin hoặc Nhân viên

// Xử lý POST trước header
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $tendm = trim($_POST['txt_tendm'] ?? '');
    
    if (empty($tendm)) {
        setFlash('error', 'Vui lòng nhập tên danh mục');
    } else {
        $result = callAPI('POST', '/api/danhmuc', ['tendm' => $tendm]);
        
        if ($result && $result['status']) {
            setFlash('success', 'Thêm danh mục thành công');
            header("Location: danhmuc.php");
            exit();
        }
        setFlash('error', $result['message'] ?? 'Lỗi không xác định');
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Header variables
$page_title = 'Thêm danh mục';
$active_nav = 'danhmuc';
$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/danhmuc.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";

// Get flash messages
$error = getFlash('error');
?>

<main class="container">
    <h1>THÊM DANH MỤC</h1>

    <?php if ($error): ?>
        <div class="dm-alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="dm-form">
        <?= csrf_field() ?>
        
        <div class="dm-form-group">
            <label for="txt_tendm" class="dm-label">
                Tên danh mục <span class="dm-required">*</span>
            </label>
            <input type="text" id="txt_tendm" name="txt_tendm" class="dm-input" required>
        </div>
        
        <div class="dm-form-actions">
            <button type="submit" class="dm-btn dm-btn-primary">Lưu</button>
            <button type="reset" class="dm-btn dm-btn-secondary">Đặt lại</button>
            <a href="danhmuc.php" class="dm-btn dm-btn-default">Quay lại</a>
        </div>
    </form>
</main>

<?php include "../../includes/footer.php"; ?>
