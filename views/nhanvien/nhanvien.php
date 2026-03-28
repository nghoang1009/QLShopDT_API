<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Lấy thông tin role từ header (đã xử lý trong header.php)
include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');

$username = $_SESSION['username'];
$sql_role = "SELECT role FROM taikhoan WHERE tentk = '$username'";
$result_role = mysqli_query($conn, $sql_role);
$row_role = mysqli_fetch_assoc($result_role);
$role = $row_role['role'];

// Chỉ Admin mới có quyền quản lý nhân viên
if ($role != 1) {
    echo "<h3 align='center' style='color:red;'>Bạn không có quyền truy cập chức năng này!</h3>";
    echo "<p align='center'><a href='../trangchu.php'>Quay lại trang chủ</a></p>";
    exit();
}

$page_title = 'Quản lý Nhân viên';
$active_nav = 'nhanvien';
include "../../includes/header.php";

// Lấy danh sách nhân viên
$sql_select = "SELECT * FROM `nhanvien`";
$result = mysqli_query($conn, $sql_select);
$employees = mysqli_fetch_all($result, MYSQLI_ASSOC);
$tong_bg = count($employees);
?>

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
