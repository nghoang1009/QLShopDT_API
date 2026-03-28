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
    echo "<script>alert('Bạn không có quyền thêm nhân viên!'); window.location.href='nhanvien.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm nhân viên</title>
</head>
<body>
    <h1 align="center">THÊM NHÂN VIÊN</h1>
    <?php
        $sql_select = "SELECT * FROM `nhanvien`";
        $result = mysqli_query($conn, $sql_select);
        $tong = mysqli_num_rows($result);

        $stt = 0;
        while($row = mysqli_fetch_object($result)) {
            $stt++;
            $manv[$stt] = $row->manv;
            $tennv[$stt] = $row->tennv;
            $diachi[$stt] = $row->diachi;
            $sdt[$stt] = $row->sdt;
            $ns[$stt] = $row->ns;
        }
    ?>

    <form action="nhanvien_insert.php" method="post" enctype="multipart/form-data">
        <table border="1" align="center">
            <tr>
                <td colspan="2" align="center">Thêm nhân viên</td>
            </tr>
            <tr>
                <td>Tên nhân viên:</td>
                <td>
                    <input type="text" name="txt_tennv">
                </td>
            </tr>
            <tr>
                <td>Địa chỉ:</td>
                <td>
                    <input type="text" name="txt_diachi">
                </td>
            </tr>
            <tr>
                <td>Ngày sinh:</td>
                <td>
                    <input type="date" name="date_ns">
                </td>
            </tr>
            <tr>
                <td>Số điện thoại:</td>
                <td>
                    <input type="text" name="txt_sdt">
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
