<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

include "../../includes/api_helper.php";

$result = callNhanvienAPI([
    "action" => "delete",
    "manv" => $_GET['manv'] ?? 0
]);

if ($result && $result['status']) {
    header("Location: nhanvien.php");
    exit();
} else {
    $page_title = 'Lỗi xóa nhân viên';
    $active_nav = 'nhanvien';
    $extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/danhmuc.css">';
    include "../../includes/header.php";
    
    echo '<div class="dm-error-box">';
    echo '<h3 class="dm-error-title">Xóa thất bại</h3>';
    echo '<p class="dm-error-text">' . htmlspecialchars($result['message'] ?? 'Lỗi không xác định') . '</p>';
    echo '<a href="nhanvien.php" class="dm-btn dm-btn-primary">Quay lại danh sách</a>';
    echo '</div></body></html>';
}
?>
