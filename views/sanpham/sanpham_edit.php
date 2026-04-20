<?php
/**
 * Sửa sản phẩm
 */
session_start();
require_once "../../includes/api_helper.php";

// Auth check trước output
requireLogin();
requireRole([1, 2]); // Admin hoặc Nhân viên

$masp = $_GET['masp'] ?? $_POST['masp'] ?? 0;

// Lấy danh mục để hiển thị dropdown
$dm_result = callAPI('GET', '/api/danhmuc');
$danhmucs = ($dm_result && $dm_result['status']) ? $dm_result['data'] : [];

// Xử lý POST trước header
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    
    $tensp   = trim($_POST['tensp'] ?? '');
    $gia     = (float)($_POST['gia'] ?? 0);
    $sl      = (int)($_POST['sl'] ?? 0);
    $hang    = trim($_POST['hang'] ?? '');
    $baohanh = (int)($_POST['baohanh'] ?? 0);
    $ghichu  = trim($_POST['ghichu'] ?? '');
    $madm    = (int)($_POST['madm'] ?? 0);
    $hinhanh = $_POST['hinhanh_cu'] ?? '';
    
    // Xử lý upload ảnh mới (nếu có)
    if (isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['hinhanh']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $hinhanh_new = 'sp_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = dirname(dirname(__DIR__)) . '/includes/img/' . $hinhanh_new;
            
            if (move_uploaded_file($_FILES['hinhanh']['tmp_name'], $upload_path)) {
                // Xóa ảnh cũ nếu có
                if (!empty($hinhanh)) {
                    $old_path = dirname(dirname(__DIR__)) . '/includes/img/' . $hinhanh;
                    if (file_exists($old_path)) {
                        @unlink($old_path);
                    }
                }
                $hinhanh = $hinhanh_new;
            }
        }
    }
    
    if (empty($tensp)) {
        setFlash('error', 'Vui lòng nhập tên sản phẩm');
    } else {
        $result = callAPI('PUT', '/api/sanpham/' . (int)$masp, [
            'tensp'   => $tensp,
            'gia'     => $gia,
            'sl'      => $sl,
            'hang'    => $hang,
            'baohanh' => $baohanh,
            'ghichu'  => $ghichu,
            'hinhanh' => $hinhanh,
            'madm'    => $madm,
        ]);
        
        if ($result && $result['status']) {
            setFlash('success', 'Cập nhật sản phẩm thành công');
            header("Location: sanpham.php");
            exit();
        }
        setFlash('error', $result['message'] ?? 'Lỗi không xác định');
    }
    header("Location: sanpham_edit.php?masp=" . $masp);
    exit();
}

// Lấy thông tin sản phẩm từ RESTful API
$result = callAPI('GET', '/api/sanpham/' . (int)$masp);

if (!$result || !$result['status']) {
    setFlash('error', 'Không tìm thấy sản phẩm');
    header("Location: sanpham.php");
    exit();
}

$sp = $result['data'];

// Header variables
$page_title = 'Sửa sản phẩm';
$active_nav = 'sanpham';
$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/sanpham.css?v=' . time() . '">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";

// Get flash messages
$error = getFlash('error');

// Đường dẫn ảnh
$img_base = '/QLShopDT_API/includes/img/';
?>

<main class="container">
    <h1>SỬA SẢN PHẨM</h1>

    <?php if ($error): ?>
        <div class="sp-alert sp-alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="sp-form">
        <?= csrf_field() ?>
        <input type="hidden" name="masp" value="<?= e($masp) ?>">
        <input type="hidden" name="hinhanh_cu" value="<?= e($sp['hinhanh']) ?>">
        
        <div class="sp-id-badge">
            <small>Mã sản phẩm</small>
            <strong>#<?= e($masp) ?></strong>
        </div>
        
        <div class="sp-form-grid">
            <div class="sp-form-group sp-form-full">
                <label for="tensp" class="sp-label">
                    Tên sản phẩm <span class="sp-required">*</span>
                </label>
                <input type="text" id="tensp" name="tensp" value="<?= e($sp['tensp']) ?>" class="sp-input" required>
            </div>
            
            <div class="sp-form-group">
                <label for="gia" class="sp-label">Giá (VNĐ)</label>
                <input type="number" id="gia" name="gia" value="<?= (float)$sp['gia'] ?>" class="sp-input" min="0">
            </div>
            
            <div class="sp-form-group">
                <label for="sl" class="sp-label">Số lượng</label>
                <input type="number" id="sl" name="sl" value="<?= (int)$sp['sl'] ?>" class="sp-input" min="0">
            </div>
            
            <div class="sp-form-group">
                <label for="madm" class="sp-label">Danh mục</label>
                <select id="madm" name="madm" class="sp-input sp-select">
                    <option value="0">-- Chọn danh mục --</option>
                    <?php foreach ($danhmucs as $dm): ?>
                        <option value="<?= $dm['madm'] ?>" <?= ($dm['madm'] == $sp['madm']) ? 'selected' : '' ?>>
                            <?= e($dm['tendm']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="sp-form-group">
                <label for="hang" class="sp-label">Hãng sản xuất</label>
                <input type="text" id="hang" name="hang" value="<?= e($sp['hang']) ?>" class="sp-input">
            </div>
            
            <div class="sp-form-group">
                <label for="baohanh" class="sp-label">Bảo hành (tháng)</label>
                <input type="number" id="baohanh" name="baohanh" value="<?= (int)$sp['baohanh'] ?>" class="sp-input" min="0">
            </div>
            
            <div class="sp-form-group">
                <label for="hinhanh" class="sp-label">Hình ảnh mới</label>
                <input type="file" id="hinhanh" name="hinhanh" class="sp-input sp-file" accept="image/*">
                <?php if (!empty($sp['hinhanh'])): ?>
                    <div class="sp-current-img">
                        <small>Ảnh hiện tại:</small>
                        <img src="<?= $img_base . e($sp['hinhanh']) ?>" alt="Current">
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="sp-form-group sp-form-full">
                <label for="ghichu" class="sp-label">Ghi chú</label>
                <textarea id="ghichu" name="ghichu" class="sp-input sp-textarea" rows="3"><?= e($sp['ghichu']) ?></textarea>
            </div>
        </div>
        
        <div class="sp-form-actions">
            <button type="submit" class="sp-btn sp-btn-primary">Cập nhật</button>
            <button type="reset" class="sp-btn sp-btn-secondary">Đặt lại</button>
            <a href="sanpham.php" class="sp-btn sp-btn-default">Quay lại</a>
        </div>
    </form>
</main>

<?php include "../../includes/footer.php"; ?>