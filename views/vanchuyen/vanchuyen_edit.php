<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa vận chuyển</title>
</head>
<body>
    <h1 align="center">SỬA THÔNG TIN VẬN CHUYỂN</h1>
    
    <?php
        include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        if (!isset($_SESSION['username'])) {
            echo "<p align='center'>Vui lòng <a href='../login.php'>đăng nhập</a></p>";
            exit();
        }

        $username = $_SESSION['username'];
        mysqli_set_charset($conn, "utf8");
        
        $sql_get_role = "SELECT role FROM taikhoan WHERE tentk = '$username'";
        $result_role = mysqli_query($conn, $sql_get_role);
        $row_role = mysqli_fetch_object($result_role);

        if ($row_role->role == '0') {
            echo "<p align='center'>Bạn không có quyền sửa vận chuyển!</p>";
            echo "<p align='center'><a href='vanchuyen.php'>Quay lại</a></p>";
            exit();
        }

        $mavc = isset($_REQUEST["mavc"]) ? $_REQUEST["mavc"] : "";
        
        if (empty($mavc)) {
            echo "<p align='center'>Không tìm thấy thông tin vận chuyển</p>";
            exit();
        }

        $sql_select = "SELECT vc.mavc, vc.madh, vc.makh, vc.ngaygiao,
                              kh.tenkh, dh.trigia
                       FROM vanchuyen vc
                       JOIN khachhang kh ON vc.makh = kh.makh
                       JOIN donhang dh ON vc.madh = dh.madh
                       WHERE vc.mavc = '$mavc'";
        $result = mysqli_query($conn, $sql_select);
        
        if (!$result || mysqli_num_rows($result) == 0) {
            echo "<p align='center'>Không tìm thấy thông tin vận chuyển</p>";
            exit();
        }
        
        $row = mysqli_fetch_object($result);
    ?>

    <form action="vanchuyen_edit_save.php" method="post">
        <input type="hidden" name="mavc" value="<?php echo $row->mavc; ?>">
        
        <table border="1" align="center">
            <tr>
                <td colspan="2" align="center">Thông tin vận chuyển</td>
            </tr>
            <tr>
                <td>Mã vận chuyển:</td>
                <td><strong><?php echo $row->mavc; ?></strong></td>
            </tr>
            <tr>
                <td>Đơn hàng:</td>
                <td>Đơn hàng #<?php echo $row->madh; ?> - <?php echo $row->tenkh; ?> - <?php echo number_format($row->trigia); ?> VNĐ</td>
            </tr>
            <tr>
                <td>Ngày giao dự kiến:</td>
                <td>
                    <input type="date" name="ngaygiao" value="<?php echo $row->ngaygiao; ?>" required>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="Cập nhật">
                    <input type="reset" value="Reset">
                    <input type="button" value="Quay lại" onclick="window.location.href='vanchuyen.php'">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
