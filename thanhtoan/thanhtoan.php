<?php
include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
include "../header.php";
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$username = $_SESSION['username'];
$sql_role = "SELECT role FROM taikhoan WHERE tentk = '$username'";
$result_role = mysqli_query($conn, $sql_role);
$row_role = mysqli_fetch_assoc($result_role);
$userid = $_SESSION['userid'];
$role = $row_role['role'];

$isAdminOrStaff = ($role == 1 || $role == 2);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý thanh toán</title>
    <link rel="stylesheet" href="../css/nv.css">
</head>
<body>
    <h1 align="center">QUẢN LÝ THANH TOÁN</h1>
    <h2 align="center"><a href="../trangchu.php">TRANG CHỦ</a></h2>

    <?php
    $sql_select = "SELECT tt.*, dh.ngaydat, kh.tenkh, nv.tennv 
                   FROM thanhtoan tt
                   JOIN donhang dh ON tt.madh = dh.madh
                   JOIN khachhang kh ON dh.makh = kh.makh
                   JOIN nhanvien nv ON dh.manv = nv.manv";

    if ($role == 0) // Nếu là khách hàng
        $sql_select = $sql_select . ' ' . "WHERE kh.makh = $userid";

    $sql_select = $sql_select . ' ' . "ORDER BY tt.ngaythanhtoan DESC";


    $result = mysqli_query($conn, $sql_select);
    $tong_bg = mysqli_num_rows($result);

    $stt = 0;
    while($row = mysqli_fetch_assoc($result)) {
        $stt++;
        $matt[$stt] = $row['matt'];
        $madh[$stt] = $row['madh'];
        $phuongthuc[$stt] = $row['phuongthuc'];
        $ngaythanhtoan[$stt] = $row['ngaythanhtoan'];
        $sotien[$stt] = $row['sotien'];
        $trangthai[$stt] = $row['trangthai'];
        $ghichu[$stt] = $row['ghichu'];
        $tenkh[$stt] = $row['tenkh'];
        $tennv[$stt] = $row['tennv'];
    }
    ?>

    <table width="1400" align="center" border="1">
        <tr>
            <th>STT</th>
            <th>Mã ĐH</th>
            <th>Khách hàng</th>
            <th>Nhân viên</th>
            <th>Phương thức</th>
            <th>Ngày thanh toán</th>
            <th>Số tiền</th>
            <th>Trạng thái</th>
            <th>Ghi chú</th>
            <?php if ($isAdminOrStaff): ?>
            <th width="200">
                <a href="thanhtoan_add.php">Thêm thanh toán</a>
            </th>
            <?php endif; ?>
        </tr>

        <?php
        for ($i=1; $i<=$tong_bg; $i++) {
        ?>
            <tr align="center">
                <td><?php echo $i; ?></td>
                <td><?php echo $madh[$i]; ?></td>
                <td><?php echo $tenkh[$i]; ?></td>
                <td><?php echo $tennv[$i]; ?></td>
                <td><?php echo $phuongthuc[$i]; ?></td>
                <td><?php echo date('d/m/Y H:i', strtotime($ngaythanhtoan[$i])); ?></td>
                <td><?php echo number_format($sotien[$i], 0, ',', '.'); ?> đ</td>
                <td><?php echo $trangthai[$i]; ?></td>
                <td><?php echo $ghichu[$i]; ?></td>
                <?php if ($isAdminOrStaff): ?>
                <td>
                    <a href="thanhtoan_edit.php?matt=<?php echo $matt[$i]; ?>">Sửa</a> |
                    <a href="thanhtoan_del.php?matt=<?php echo $matt[$i]; ?>"
                       onclick="return confirm('Bạn có chắc muốn xóa thanh toán này?')">Xóa</a>
                </td>
                <?php endif; ?>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td colspan="<?php echo $isAdminOrStaff ? '10' : '9'; ?>" align="right">
                Tổng: <?php echo $tong_bg; ?> thanh toán
            </td>
        </tr>
    </table>
</body>
</html>
