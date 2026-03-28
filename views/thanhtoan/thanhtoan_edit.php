<?php
include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['username'];
$sql_role = "SELECT role FROM taikhoan WHERE tentk = '$username'";
$result_role = mysqli_query($conn, $sql_role);
$row_role = mysqli_fetch_assoc($result_role);
$role = $row_role['role'];

if ($role != 1 && $role != 2) {
    echo "<script>alert('Bạn không có quyền!'); window.location.href='thanhtoan.php';</script>";
    exit();
}

$matt = $_REQUEST["matt"];
$sql_select = "SELECT * FROM thanhtoan WHERE matt='$matt'";
$result = mysqli_query($conn, $sql_select);
$row = mysqli_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sửa thanh toán</title>
</head>
<body>
    <h1 align="center">SỬA THANH TOÁN</h1>
    <form method="post" action="thanhtoan_edit_save.php">
        <table align="center" border="1">
            <tr>
                <td colspan="2">Thông tin thanh toán</td>
            </tr>
            <tr>
                <td>Phương thức</td>
                <td>
                    <select name="phuongthuc" required>
                        <option value="Tiền mặt" <?php if($row['phuongthuc']=='Tiền mặt') echo 'selected'; ?>>Tiền mặt</option>
                        <option value="Chuyển khoản" <?php if($row['phuongthuc']=='Chuyển khoản') echo 'selected'; ?>>Chuyển khoản</option>
                        <option value="Thẻ" <?php if($row['phuongthuc']=='Thẻ') echo 'selected'; ?>>Thẻ</option>
                        <option value="Ví điện tử" <?php if($row['phuongthuc']=='Ví điện tử') echo 'selected'; ?>>Ví điện tử</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Số tiền</td>
                <td><input type="number" name="sotien" value="<?php echo $row['sotien']; ?>" required></td>
            </tr>
            <tr>
                <td>Trạng thái</td>
                <td>
                    <select name="trangthai" required>
                        <option value="Chờ xác nhận" <?php if($row['trangthai']=='Chờ xác nhận') echo 'selected'; ?>>Chờ xác nhận</option>
                        <option value="Đã thanh toán" <?php if($row['trangthai']=='Đã thanh toán') echo 'selected'; ?>>Đã thanh toán</option>
                        <option value="Thất bại" <?php if($row['trangthai']=='Thất bại') echo 'selected'; ?>>Thất bại</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Ghi chú</td>
                <td><textarea name="ghichu" rows="3" cols="40"><?php echo $row['ghichu']; ?></textarea></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="hidden" name="matt" value="<?php echo $matt; ?>">
                    <input type="submit" value="Lưu">
                    <input type="button" value="Quay lại" onclick="window.location.href='thanhtoan.php'">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
