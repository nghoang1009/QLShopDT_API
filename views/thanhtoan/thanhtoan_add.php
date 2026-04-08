<?php
include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$role = $_SESSION['role'] ?? 0;

if ($role != 1 && $role != 2) {
    echo "<script>alert('Bạn không có quyền thêm thanh toán!'); window.location.href='thanhtoan.php';</script>";
    exit();
}

// Lấy danh sách đơn hàng
$sql_dh = "SELECT dh.madh, kh.tenkh, dh.trigia 
            FROM donhang dh 
            JOIN khachhang kh ON dh.makh = kh.makh 
            ORDER BY dh.madh DESC";
$result_dh = mysqli_query($conn, $sql_dh);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Thêm thanh toán</title>
</head>
<body>
    <h1 align="center">THÊM THANH TOÁN</h1>
    <form method="post" action="thanhtoan_insert.php">
        <table align="center" border="1">
            <tr>
                <td colspan="2" align="center"><strong>Thông tin thanh toán</strong></td>
            </tr>
            <tr>
                <td>Đơn hàng</td>
                <td>
                    <select name="madh" required>
                        <option value="">--Chọn đơn hàng--</option>
                        <?php while($row = mysqli_fetch_assoc($result_dh)): ?>
                            <option value="<?php echo $row['madh']; ?>">
                                ĐH<?php echo $row['madh']; ?> - <?php echo $row['tenkh']; ?> 
                                (<?php echo number_format($row['trigia'], 0, ',', '.'); ?> đ)
                            </option>
                        <?php endwhile; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Phương thức</td>
                <td>
                    <select name="phuongthuc" required>
                        <option value="">--Chọn phương thức--</option>
                        <option value="Tiền mặt">Tiền mặt</option>
                        <option value="Chuyển khoản">Chuyển khoản</option>
                        <option value="Thẻ">Thẻ</option>
                        <option value="Ví điện tử">Ví điện tử</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Số tiền</td>
                <td><input type="number" name="sotien" required></td>
            </tr>
            <tr>
                <td>Trạng thái</td>
                <td>
                    <select name="trangthai" required>
                        <option value="">--Chọn trạng thái--</option>
                        <option value="Chờ xác nhận">Chờ xác nhận</option>
                        <option value="Đã thanh toán">Đã thanh toán</option>
                        <option value="Thất bại">Thất bại</option>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Ghi chú</td>
                <td><textarea name="ghichu" rows="3" cols="40"></textarea></td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="Thêm">
                    <input type="reset" value="Reset">
                    <input type="button" value="Quay lại" onclick="window.location.href='thanhtoan.php'">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
