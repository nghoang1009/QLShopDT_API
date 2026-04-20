<?php
/**
 * Sửa danh mục
 */
session_start();
require_once "../../includes/api_helper.php";

// Auth check trước output
requireLogin();
requireRole([1, 2]); // Admin hoặc Nhân viên

$madm = $_GET['madm'] ?? $_POST['madm'] ?? 0;

// Xử lý POST trước header
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $tendm = trim($_POST['txt_tendm'] ?? '');
    
    if (empty($tendm)) {
        setFlash('error', 'Vui lòng nhập tên danh mục');
    } else {
        $result = callAPI('PUT', '/api/danhmuc/' . (int)$madm, ['tendm' => $tendm]);
        
        if ($result && $result['status']) {
            setFlash('success', 'Cập nhật danh mục thành công');
            header("Location: danhmuc.php");
            exit();
        }
        setFlash('error', $result['message'] ?? 'Lỗi không xác định');
    }
    header("Location: danhmuc_edit.php?madm=" . $madm);
    exit();
}

// Lấy thông tin danh mục từ RESTful API
$result = callAPI('GET', '/api/danhmuc/' . (int)$madm);

if (!$result || !$result['status']) {
    setFlash('error', 'Không tìm thấy danh mục');
    header("Location: danhmuc.php");
    exit();
}

$tendm = $result['data']['tendm'];

// Header variables
$page_title = 'Sửa danh mục';
$active_nav = 'danhmuc';
$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/danhmuc.css">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";

// Get flash messages
$error = getFlash('error');
?>

<main class="container">
    <h1>SỬA DANH MỤC</h1>

    <?php if ($error): ?>
        <div class="dm-alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" class="dm-form">
        <?= csrf_field() ?>
        <input type="hidden" name="madm" value="<?= e($madm) ?>">
        
        <div class="dm-id-badge">
            <small>Mã danh mục</small>
            <strong>#<?= e($madm) ?></strong>
        </div>
        
        <div class="dm-form-group">
            <label for="txt_tendm" class="dm-label">
                Tên danh mục <span class="dm-required">*</span>
            </label>
            <input type="text" id="txt_tendm" name="txt_tendm" value="<?= e($tendm) ?>" class="dm-input" required>
        </div>
        
        <div class="dm-form-actions">
            <button type="submit" class="dm-btn dm-btn-primary">Cập nhật</button>
            <button type="reset" class="dm-btn dm-btn-secondary">Đặt lại</button>
            <a href="danhmuc.php" class="dm-btn dm-btn-default">Quay lại</a>
        </div>
    </form>
</main>

<?php include "../../includes/footer.php"; ?>
