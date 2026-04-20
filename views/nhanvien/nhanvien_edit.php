<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit();
}

$page_title = 'Sửa nhân viên';
$active_nav = 'nhanvien';
$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/danhmuc.css">';
include "../../includes/header.php";
include "../../includes/api_helper.php";

$manv = $_GET['manv'] ?? $_POST['manv'] ?? 0;
$thongbao = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $result = callNhanvienAPI([
        "action" => "update",
        "manv" => $manv,
        "tennv" => $_POST['txt_tennv'] ?? '',
        "diachi" => $_POST['txt_diachi'] ?? '',
        "sdt" => $_POST['txt_sdt'] ?? '',
        "ns" => $_POST['date_ns'] ?? ''
    ]);
    
    if ($result && $result['status']) {
        header("Location: nhanvien.php");
        exit();
    }
    $thongbao = "Lỗi: " . ($result['message'] ?? 'Không xác định');
}

$result = callNhanvienAPI(["action" => "getone", "manv" => $manv]);

if ($result && $result['status']) {
    $tennv = $result['data']['tennv'];
    $diachi = $result['data']['diachi'];
    $sdt = $result['data']['sdt'];
    $ns = $result['data']['ns'];
} else {
    echo '<div class="dm-error-box">';
    echo '<p class="dm-error-title">Không tìm thấy nhân viên</p>';
    echo '<a href="nhanvien.php" class="dm-link-back">Quay lại danh sách</a>';
    echo '</div></body></html>';
    exit();
}
?>

<h1>SỬA NHÂN VIÊN</h1>

<?php if($thongbao): ?>
    <div class="dm-alert-error">
        <?php echo htmlspecialchars($thongbao); ?>
    </div>
<?php endif; ?>

<form method="POST" action="" class="dm-form">
    <input type="hidden" name="manv" value="<?php echo $manv; ?>">
    
    <div class="dm-id-badge">
        <small>Mã nhân viên</small>
        <strong>#<?php echo htmlspecialchars($manv); ?></strong>
    </div>
    
    <div class="dm-form-group">
        <label for="txt_tennv" class="dm-label">
            Tên nhân viên <span class="dm-required">*</span>
        </label>
        <input type="text" id="txt_tennv" name="txt_tennv" value="<?php echo htmlspecialchars($tennv); ?>" class="dm-input" required>
    </div>
    
    <div class="dm-form-group">
        <label for="txt_diachi" class="dm-label">
            Địa chỉ
        </label>
        <input type="text" id="txt_diachi" name="txt_diachi" value="<?php echo htmlspecialchars($diachi); ?>" class="dm-input">
    </div>
    
    <div class="dm-form-group">
        <label for="date_ns" class="dm-label">
            Ngày sinh
        </label>
        <input type="date" id="date_ns" name="date_ns" value="<?php echo htmlspecialchars($ns); ?>" class="dm-input">
    </div>
    
    <div class="dm-form-group">
        <label for="txt_sdt" class="dm-label">
            Số điện thoại
        </label>
        <input type="text" id="txt_sdt" name="txt_sdt" value="<?php echo htmlspecialchars($sdt); ?>" class="dm-input">
    </div>
    
    <div class="dm-form-actions">
        <button type="submit" class="dm-btn dm-btn-primary">Cập nhật</button>
        <button type="reset" class="dm-btn dm-btn-secondary">Đặt lại</button>
        <button type="button" onclick="location.href='nhanvien.php'" class="dm-btn dm-btn-default">Quay lại</button>
    </div>
</form>

</body>
</html>
