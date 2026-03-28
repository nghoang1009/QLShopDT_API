<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$page_title = 'Thêm danh mục';
$active_nav = 'danhmuc';
$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/danhmuc.css">';
include "../../includes/header.php";
include "../../includes/api_helper.php";

$thongbao = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = callDanhmucAPI([
        "action" => "add",
        "tendm" => $_POST['txt_tendm'] ?? ''
    ]);
    
    if ($result && $result['status']) {
        header("Location: danhmuc.php");
        exit();
    }
    $thongbao = "Lỗi: " . ($result['message'] ?? 'Không xác định');
}
?>

<h1>THÊM DANH MỤC</h1>

<?php if($thongbao): ?>
    <div class="dm-alert-error">
        <?php echo htmlspecialchars($thongbao); ?>
    </div>
<?php endif; ?>

<form method="POST" action="" class="dm-form">
    <div class="dm-form-group">
        <label for="txt_tendm" class="dm-label">
            Tên danh mục <span class="dm-required">*</span>
        </label>
        <input type="text" id="txt_tendm" name="txt_tendm" class="dm-input" required>
    </div>
    
    <div class="dm-form-actions">
        <button type="submit" class="dm-btn dm-btn-primary">Lưu</button>
        <button type="reset" class="dm-btn dm-btn-secondary">Đặt lại</button>
        <button type="button" onclick="location.href='danhmuc.php'" class="dm-btn dm-btn-default">Quay lại</button>
    </div>
</form>

</body>
</html>
