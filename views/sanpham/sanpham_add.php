<?php
/**
 * Thêm sản phẩm mới
 */
session_start();
require_once "../../includes/api_helper.php";

// Auth check trước output
requireLogin();
requireRole([1, 2]); // Admin hoặc Nhân viên

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
    $hinhanh = '';
    
    // Xử lý upload ảnh
    if (isset($_FILES['hinhanh']) && $_FILES['hinhanh']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $ext = strtolower(pathinfo($_FILES['hinhanh']['name'], PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed)) {
            $hinhanh = 'sp_' . time() . '_' . uniqid() . '.' . $ext;
            $upload_path = dirname(dirname(__DIR__)) . '/includes/img/' . $hinhanh;
            
            if (!move_uploaded_file($_FILES['hinhanh']['tmp_name'], $upload_path)) {
                setFlash('error', 'Không thể upload ảnh');
                header("Location: " . $_SERVER['REQUEST_URI']);
                exit();
            }
        } else {
            setFlash('error', 'Định dạng ảnh không hợp lệ (chỉ jpg, png, gif, webp)');
            header("Location: " . $_SERVER['REQUEST_URI']);
            exit();
        }
    }
    
    if (empty($tensp)) {
        setFlash('error', 'Vui lòng nhập tên sản phẩm');
    } else {
        $result = callAPI('POST', '/api/sanpham', [
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
            setFlash('success', 'Thêm sản phẩm thành công');
            header("Location: sanpham.php");
            exit();
        }
        setFlash('error', $result['message'] ?? 'Lỗi không xác định');
    }
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit();
}

// Header variables
$page_title = 'Thêm sản phẩm';
$active_nav = 'sanpham';
$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/sanpham.css?v=' . time() . '">
<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';

include "../../includes/header.php";

// Get flash messages
$error = getFlash('error');
?>

<main class="container">
    <h1>THÊM SẢN PHẨM</h1>

    <?php if ($error): ?>
        <div class="sp-alert sp-alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data" class="sp-form">
        <?= csrf_field() ?>
        
        <div class="sp-form-grid">
            <div class="sp-form-group sp-form-full">
                <label for="tensp" class="sp-label">
                    Tên sản phẩm <span class="sp-required">*</span>
                </label>
                <input type="text" id="tensp" name="tensp" class="sp-input" required>
            </div>
            
            <div class="sp-form-group">
                <label for="gia" class="sp-label">Giá (VNĐ)</label>
                <input type="number" id="gia" name="gia" class="sp-input" min="0" value="0">
            </div>
            
            <div class="sp-form-group">
                <label for="sl" class="sp-label">Số lượng</label>
                <input type="number" id="sl" name="sl" class="sp-input" min="0" value="0">
            </div>
            
            <div class="sp-form-group">
                <label for="madm" class="sp-label">Danh mục</label>
                <select id="madm" name="madm" class="sp-input sp-select">
                    <option value="0">-- Chọn danh mục --</option>
                    <?php foreach ($danhmucs as $dm): ?>
                        <option value="<?= $dm['madm'] ?>"><?= e($dm['tendm']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="sp-form-group">
                <label for="hang" class="sp-label">Hãng sản xuất</label>
                <input type="text" id="hang" name="hang" class="sp-input" placeholder="Apple, Samsung, ...">
            </div>
            
            <div class="sp-form-group">
                <label for="baohanh" class="sp-label">Bảo hành (tháng)</label>
                <input type="number" id="baohanh" name="baohanh" class="sp-input" min="0" value="12">
            </div>
            
            <div class="sp-form-group">
                <label for="hinhanh" class="sp-label">Hình ảnh</label>
                <input type="file" id="hinhanh" name="hinhanh" class="sp-input sp-file" accept="image/*">
            </div>
            
            <div class="sp-form-group sp-form-full">
                <label for="ghichu" class="sp-label">Ghi chú</label>
                <textarea id="ghichu" name="ghichu" class="sp-input sp-textarea" rows="3"></textarea>
            </div>
        </div>
        
        <div class="sp-form-actions">
            <button type="submit" class="sp-btn sp-btn-primary">Lưu</button>
            <button type="reset" class="sp-btn sp-btn-secondary">Đặt lại</button>
            <a href="sanpham.php" class="sp-btn sp-btn-default">Quay lại</a>
        </div>
    </form>
</main>

<?php include "../../includes/footer.php"; ?>