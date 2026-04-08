<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm vận chuyển</title>
</head>
<body>
    <h1 align="center">THÊM THÔNG TIN VẬN CHUYỂN</h1>
    
    <?php
        include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        if (!isset($_SESSION['username'])) {
            echo "<p align='center'>Vui lòng <a href='../login.php'>đăng nhập</a></p>";
            exit();
        }

        mysqli_set_charset($conn, "utf8");
        
        $role = $_SESSION['role'] ?? 0;

        if ($role == '0') {
            echo "<p align='center'>Bạn không có quyền thêm vận chuyển!</p>";
            echo "<p align='center'><a href='vanchuyen.php'>Quay lại</a></p>";
            exit();
        }

        // Lấy danh sách đơn hàng chưa có vận chuyển
        $sql_donhang = "SELECT dh.madh, dh.makh, kh.tenkh, dh.ngaydat, dh.trigia
                        FROM donhang dh
                        JOIN khachhang kh ON dh.makh = kh.makh
                        WHERE dh.madh NOT IN (SELECT madh FROM vanchuyen)
                        ORDER BY dh.madh DESC";
        $result_donhang = mysqli_query($conn, $sql_donhang);
    ?>

    <form action="vanchuyen_insert.php" method="post">
        <table border="1" align="center">
            <tr>
                <td colspan="2" align="center">Thông tin vận chuyển</td>
            </tr>
            <tr>
                <td>Chọn đơn hàng:</td>
                <td>
                    <select name="madh" required>
                        <option value="">-- Chọn đơn hàng --</option>
                        <?php
                        while($row = mysqli_fetch_object($result_donhang)) {
                            echo "<option value='{$row->madh}'>Đơn hàng #{$row->madh} - {$row->tenkh} - " . number_format($row->trigia) . " VNĐ</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Ngày giao dự kiến:</td>
                <td>
                    <input type="date" name="ngaygiao" required>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="Thêm">
                    <input type="reset" value="Reset">
                    <input type="button" value="Quay lại" onclick="window.location.href='vanchuyen.php'">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
