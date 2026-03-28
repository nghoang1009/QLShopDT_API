<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$page_title = 'Sửa danh mục';
$active_nav = 'danhmuc';
$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/danhmuc.css">';
include "../../includes/header.php";
include "../../includes/api_helper.php";

$madm = $_GET['madm'] ?? $_POST['madm'] ?? 0;
$thongbao = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = callDanhmucAPI([
        "action" => "update",
        "madm" => $madm,
        "tendm" => $_POST['txt_tendm'] ?? ''
    ]);
    
    if ($result && $result['status']) {
        header("Location: danhmuc.php");
        exit();
    }
    $thongbao = "Lỗi: " . ($result['message'] ?? 'Không xác định');
}

$result = callDanhmucAPI(["action" => "getone", "madm" => $madm]);

if ($result && $result['status']) {
    $tendm = $result['data']['tendm'];
} else {
    echo '<div class="dm-error-box">';
    echo '<p class="dm-error-title">Không tìm thấy danh mục</p>';
    echo '<a href="danhmuc.php" class="dm-link-back">Quay lại danh sách</a>';
    echo '</div></body></html>';
    exit();
}
?>

<h1>SỬA DANH MỤC</h1>

<?php if($thongbao): ?>
    <div class="dm-alert-error">
        <?php echo htmlspecialchars($thongbao); ?>
    </div>
<?php endif; ?>

<form method="POST" action="" class="dm-form">
    <input type="hidden" name="madm" value="<?php echo $madm; ?>">
    
    <div class="dm-id-badge">
        <small>Mã danh mục</small>
        <strong>#<?php echo htmlspecialchars($madm); ?></strong>
    </div>
    
    <div class="dm-form-group">
        <label for="txt_tendm" class="dm-label">
            Tên danh mục <span class="dm-required">*</span>
        </label>
        <input type="text" id="txt_tendm" name="txt_tendm" value="<?php echo htmlspecialchars($tendm); ?>" class="dm-input" required>
    </div>
    
    <div class="dm-form-actions">
        <button type="submit" class="dm-btn dm-btn-primary">Cập nhật</button>
        <button type="reset" class="dm-btn dm-btn-secondary">Đặt lại</button>
        <button type="button" onclick="location.href='danhmuc.php'" class="dm-btn dm-btn-default">Quay lại</button>
    </div>
</form>

</body>
</html>
