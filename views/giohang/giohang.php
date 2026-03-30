<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">;
</head>
<body>
    <?php
        include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        include "../../includes/header.php";
        include "../../includes/footer.php";
        if (!isset($_SESSION['username'])) {
            echo "<p align='center'>Vui lòng <a href='../login.php'>đăng nhập</a> để xem giỏ hàng</p>";
            exit();
        }

        $username = $_SESSION['username'];
        
        // Lấy mã khách hàng từ bảng khachhang thông qua taikhoan
        $sql_get_user = "SELECT kh.makh 
                         FROM taikhoan tk
                         JOIN khachhang kh ON tk.matk = kh.makh
                         WHERE tk.tentk = '$username'";
        $result_user = mysqli_query($conn, $sql_get_user);
        
        if (!$result_user) {
            die("Lỗi truy vấn: " . mysqli_error($conn));
        }
        
        if (mysqli_num_rows($result_user) == 0) {
            echo "<p align='center'>Không tìm thấy thông tin khách hàng. Bạn cần đăng ký thông tin khách hàng.</p>";
            exit();
        }
        
        $row_user = mysqli_fetch_object($result_user);
        $makh = $row_user->makh;

        // Kiểm tra xem có giỏ hàng không
        $sql_check_giohang = "SELECT magio FROM giohang WHERE makh = '$makh'";
        $result_giohang = mysqli_query($conn, $sql_check_giohang);
        
        if (!$result_giohang) {
            die("Lỗi truy vấn giỏ hàng: " . mysqli_error($conn));
        }
        
        if (mysqli_num_rows($result_giohang) == 0) {
            echo "<p align='center'>Giỏ hàng của bạn đang trống</p>";
            echo "<p align='center'><a href='../sanpham/sanpham.php'>Mua sắm ngay</a></p>";
            exit();
        }
        
        $row_giohang = mysqli_fetch_object($result_giohang);
        $magio = $row_giohang->magio;

        // Lấy thông tin giỏ hàng với JOIN để lấy thông tin sản phẩm
        $sql_select = "SELECT gi.maitem, gi.magio, gi.masp, gi.sl, 
                              sp.tensp, sp.gia, sp.hinhanh, sp.hang 
                       FROM giohang_item gi
                       JOIN sanpham sp ON gi.masp = sp.masp
                       WHERE gi.magio = '$magio'";
        $result = mysqli_query($conn, $sql_select);
        
        if (!$result) {
            die("Lỗi truy vấn sản phẩm: " . mysqli_error($conn));
        }
        
        $tong_sp = mysqli_num_rows($result);

        if ($tong_sp == 0) {
            echo "<p align='center'>Giỏ hàng của bạn đang trống</p>";
            echo "<p align='center'><a href='../sanpham/sanpham.php'>Mua sắm ngay</a></p>";
            exit();
        }

        $stt = 0;
        $tongtien = 0;
        while($row = mysqli_fetch_object($result)) {
            $stt++;
            $maitem[$stt] = $row->maitem;
            $masp[$stt] = $row->masp;
            $tensp[$stt] = $row->tensp;
            $gia[$stt] = $row->gia;
            $soluong[$stt] = $row->sl;
            $hinhanh[$stt] = $row->hinhanh;
            $hang[$stt] = $row->hang;
            $thanhtien[$stt] = $gia[$stt] * $soluong[$stt];
            $tongtien += $thanhtien[$stt];
        }
    ?>
    <br>
    <h1 align="center">GIỎ HÀNG CỦA BẠN</h1>
    <table width="1200" align="center" border="1">
        <tr>
            <th>STT</th>
            <th>Hình ảnh</th>
            <th>Tên sản phẩm</th>
            <th>Hãng</th>
            <th>Đơn giá</th>
            <th>Số lượng</th>
            <th>Thành tiền</th>
            <th>Thao tác</th>
        </tr>

        <?php
        for ($i = 1; $i <= $tong_sp; $i++) {
        ?>
            <tr align="center">
                <td><?php echo $i; ?></td>
                <td><img src="../img/<?php echo $hinhanh[$i]; ?>" width="80"></td>
                <td><?php echo $tensp[$i]; ?></td>
                <td><?php echo $hang[$i]; ?></td>
                <td><?php echo number_format($gia[$i]); ?> VNĐ</td>
                <td>
                    <form action="giohang_edit.php" method="post" style="display:inline">
                        <input type="hidden" name="maitem" value="<?php echo $maitem[$i]; ?>">
                        <input type="number" name="soluong" value="<?php echo $soluong[$i]; ?>" min="1" style="width:60px">
                        <input type="submit" value="Cập nhật">
                    </form>
                </td>
                <td><?php echo number_format($thanhtien[$i]); ?> VNĐ</td>
                <td>
                    <a href="giohang_del.php?maitem=<?php echo $maitem[$i]; ?>" 
                       onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">Xóa</a>
                </td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td colspan="6" align="right"><strong>Tổng cộng:</strong></td>
            <td align="center"><strong><?php echo number_format($tongtien); ?> VNĐ</strong></td>
            <td></td>
        </tr>
        <tr>
            <td colspan="8" align="center">
                <a href="../sanpham/sanpham.php">Tiếp tục mua hàng</a> | 
                <a href="giohang_clear.php" onclick="return confirm('Bạn có chắc muốn xóa toàn bộ giỏ hàng?')">Xóa giỏ hàng</a> |
                <a href="../donhang/donhang_create.php">Đặt hàng</a>
            </td>
        </tr>
    </table>
</body>
</html>
