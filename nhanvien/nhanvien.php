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
// Chỉ Admin mới có quyền quản lý nhân viên
$isAdmin = ($role == 1);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/nv.css">
    <title>Quản lý nhân viên</title>
</head>
<body>
    <h1 align="center">QUẢN LÝ NHÂN VIÊN</h1>
    <h2 align="center"><a href="../trangchu.php">Trang chủ</a></h2>
    
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
    // Nếu không phải Admin, không cho xem
    if (!$isAdmin) {
        echo "<h3 align='center' style='color:red;'>Bạn không có quyền truy cập chức năng này!</h3>";
        echo "<p align='center'><a href='../trangchu.php'>Quay lại trang chủ</a></p>";
        exit();
    }
    
    $sql_select = "SELECT * FROM `nhanvien`";
    $result = mysqli_query($conn, $sql_select);
    $tong_bg = mysqli_num_rows($result);

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

    <table width="1300" align="center" border="1">
        <tr>
            <th>STT</th>
            <th width="250">Tên nhân viên</th>
            <th>Địa chỉ</th>
            <th>Số điện thoại</th>
            <th>Ngày sinh</th>
            <th><a href="nhanvien_add.php">Thêm nhân viên</a></th>
        </tr>

        <?php
        for ($i=1; $i<=$tong_bg; $i++) {
        ?>
            <tr align="center">
                <td><?php echo $i; ?></td>
                <td><?php echo $tennv[$i]; ?></td>
                <td><?php echo $diachi[$i]; ?></td>
                <td><?php echo $sdt[$i]; ?></td>
                <td><?php echo $ns[$i]; ?></td>
                <td> 
                    <a href="nhanvien_edit.php?manv=<?php echo $manv[$i]; ?>">Sửa</a> |
                    <a href="nhanvien_del.php?manv=<?php echo $manv[$i]; ?>" 
                       onclick="return confirm('Bạn có chắc muốn xóa nhân viên này?')">Xóa</a>
                </td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td colspan="6" align="right">Bảng có <?php echo $tong_bg; ?> nhân viên</td>
        </tr>
    </table>
</body>
</html>