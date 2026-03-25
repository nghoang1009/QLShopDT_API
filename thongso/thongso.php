<?php
include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
include "../header.php";
session_start();

// Kiểm tra đăng nhập
if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

// Lấy thông tin role
$username = $_SESSION['username'];
$sql_role = "SELECT role FROM taikhoan WHERE tentk = '$username'";
$result_role = mysqli_query($conn, $sql_role);
$row_role = mysqli_fetch_assoc($result_role);
$role = $row_role['role'];

// role: 1 = Admin, 2 = Nhân viên, 0 = Khách hàng
$isAdminOrStaff = ($role == 1 || $role == 2);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/nv.css">
    <title>Thông số sản phẩm</title>
</head>
<body>
    <h1 align="center">THÔNG SỐ SẢN PHẨM</h1>
    <h2 align="center"><a href="../trangchu.php">TRANG CHỦ</a></h2>
    
    <?php
    // Hiển thị thông tin role
    $chucvu = '';
    switch ($role) {
        case 1:
            $chucvu = 'Admin';
            break;
        case 2:
            $chucvu = 'Nhân viên';
            break;
        case 0:
            $chucvu = 'Khách hàng';
            break;
    }
    ?>
    
    <?php 
        $masp = $_REQUEST["masp"];
        
        // Thêm WHERE để lọc theo masp
        $sql_select = "SELECT ts.mats, ts.tents, ts.giatri, sp.tensp, ts.masp
                       FROM thongso ts
                       JOIN sanpham sp ON ts.masp = sp.masp
                       WHERE ts.masp = '$masp'";  
        $result = mysqli_query($conn, $sql_select);
        $tong_bg_ts = mysqli_num_rows($result);

        $stt = 0;
        while($row = mysqli_fetch_object($result)) {
            $stt++;
            $mats[$stt] = $row->mats;
            $tents[$stt] = $row->tents;
            $giatri[$stt] = $row->giatri;
            $tensp[$stt] = $row->tensp;
        }
    ?>

    <table width="1300" align="center" border="1">
        <tr>
            <th>STT</th>
            <th>Tên sản phẩm</th>
            <th>Tên thông số</th>
            <th width="700">Giá trị</th>
            <?php if ($isAdminOrStaff): ?>
            <th width="180">
                <a href="thongso_add.php?masp=<?php echo $masp; ?>">Thêm thông số</a>
            </th>
            <?php endif; ?>
        </tr>

        <?php
        for ($i=1; $i<=$tong_bg_ts; $i++) {
        ?>
            <tr align="center">
                <td><?php echo $i; ?></td>
                <td><?php echo $tensp[$i]; ?></td>
                <td><?php echo $tents[$i]; ?></td>
                <td><?php echo $giatri[$i]; ?></td>
                <?php if ($isAdminOrStaff): ?>
                <td> 
                    <a href="thongso_edit.php?mats=<?php echo $mats[$i]; ?>&masp=<?php echo $masp; ?>">Sửa</a> |
                    <a href="thongso_del.php?mats=<?php echo $mats[$i]; ?>&masp=<?php echo $masp; ?>"
                       onclick="return confirm('Bạn có chắc muốn xóa thông số này?')">Xóa</a>
                </td>
                <?php endif; ?>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td colspan="<?php echo $isAdminOrStaff ? '5' : '4'; ?>" align="right">
                Bảng có <?php echo $tong_bg_ts; ?> thông số
            </td>
        </tr>
    </table>
</body>
</html>