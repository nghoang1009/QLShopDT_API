<?php
include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Kiểm tra quyền - Chỉ Admin
$username = $_SESSION['username'];
$sql_role = "SELECT role FROM taikhoan WHERE tentk = '$username'";
$result_role = mysqli_query($conn, $sql_role);
$row_role = mysqli_fetch_assoc($result_role);
$role = $row_role['role'];

if ($role != 1) {
    echo "<script>alert('Bạn không có quyền sửa nhân viên!'); window.location.href='nhanvien.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa nhân viên</title>
</head>
<body>
    <h1 align="center">SỬA NHÂN VIÊN</h1>
    <?php
        $manv = $_REQUEST["manv"];
        
        mysqli_select_db($conn, "qlshopdienthoai") or die("Không tìm thấy CSDL");
        $sql_select = "SELECT * FROM `nhanvien` WHERE `manv` = '$manv'";
        $result = mysqli_query($conn, $sql_select);
        $row = mysqli_fetch_object($result);

        $manv = $row->manv;
        $tennv = $row->tennv;
        $diachi = $row->diachi;
        $sdt = $row->sdt;
        $ns = $row->ns;
    ?>

    <form method="post" action="nhanvien_edit_save.php?manv=<?php echo $manv; ?>" enctype="multipart/form-data">        
        <table border="1" align="center">
            <tr>
                <td colspan="2" align="center">Thông tin nhân viên</td>
            </tr>
            <tr>
                <td>Tên nhân viên:</td>
                <td>
                    <input type="text" name="txt_tennv" value="<?php echo $tennv; ?>">
                </td>
            </tr>
            <tr>
                <td>Địa chỉ:</td>
                <td>
                    <input type="text" name="txt_diachi" value="<?php echo $diachi; ?>">
                </td>
            </tr>
            <tr>
                <td>Ngày sinh:</td>
                <td>
                    <input type="date" name="date_ns" value="<?php echo $ns; ?>">
                </td>
            </tr>
            <tr>
                <td>Số điện thoại:</td>
                <td>
                    <input type="text" name="txt_sdt" value="<?php echo $sdt; ?>">
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                <input type="submit" value="OK">
                <input type="reset" value="Reset">
                <input type="button" value="Quay lại" onclick="window.location.href='nhanvien.php'">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
