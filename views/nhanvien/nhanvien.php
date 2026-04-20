<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Lấy thông tin role từ header (đã xử lý trong header.php)
include "../../includes/api_helper.php";

$role = $_SESSION['role'] ?? 0;

// Chỉ Admin mới có quyền quản lý nhân viên
if ($role != 1) {
    echo "<h3 align='center' style='color:red;'>Bạn không có quyền truy cập chức năng này!</h3>";
    echo "<p align='center'><a href='../trangchu.php'>Quay lại trang chủ</a></p>";
    exit();
}

$extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';
$page_title = 'Quản lý Nhân viên';
$active_nav = 'nhanvien';
include "../../includes/header.php";
include "../../includes/footer.php";

// Lấy danh sách nhân viên từ API
$result = callNhanVienAPI(['action' => 'getall']);
$employees = ($result && $result['status']) ? $result['data'] : [];
$tong_bg = count($employees);
?>
<html>
    <link rel="stylesheet" href="/QLShopDT_API/assets/css/sanpham.css">
</html>
<h1 align="center">QUẢN LÝ NHÂN VIÊN</h1>

<table width="1300" align="center" border="1">
        <tr>
            <th>STT</th>
            <th width="250">Tên nhân viên</th>
            <th>Địa chỉ</th>
            <th>Số điện thoại</th>
            <th>Ngày sinh</th>
            <th><a href="nhanvien_add.php">Thêm nhân viên</a></th>
        </tr>

        <?php foreach ($employees as $i => $nv): ?>
            <tr align="center">
                <td><?php echo $i + 1; ?></td>
                <td><?php echo htmlspecialchars($nv['tennv']); ?></td>
                <td><?php echo htmlspecialchars($nv['diachi']); ?></td>
                <td><?php echo htmlspecialchars($nv['sdt']); ?></td>
                <td><?php echo htmlspecialchars($nv['ns']); ?></td>
                <td> 
                    <a href="nhanvien_edit.php?manv=<?php echo $nv['manv']; ?>">Sửa</a> |
                    <a href="nhanvien_del.php?manv=<?php echo $nv['manv']; ?>" 
                       onclick="return confirm('Bạn có chắc muốn xóa nhân viên này?')">Xóa</a>
                </td>
            </tr>
        <?php endforeach; ?>

        <tr>
            <td colspan="6" align="right">Bảng có <?php echo $tong_bg; ?> nhân viên</td>
        </tr>
    </table>

</body>
</html>
