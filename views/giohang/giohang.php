<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ hàng</title>
    <link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">
</head>
<body>
    <?php
        include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        include "../../includes/header.php";
        include "../../includes/footer.php";
        include "../../model/giohang.php";

        if (!isset($_SESSION['username'])) {
            echo "<p align='center'>Vui lòng <a href='../login.php'>đăng nhập</a> để xem giỏ hàng</p>";
            exit();
        }

        $username = $_SESSION['username'];
        $role = $_SESSION['role'];
        
        global $giohang, $tong_sp;
        if ($role == 0){
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

            $giohang = GioHang::getGioHang($makh);
            
            if (!$giohang) {
                die("Lỗi truy vấn sản phẩm: " . sizeof($giohang));
            }
            
            $tong_sp = sizeof($giohang);

            if ($tong_sp == 0) {
                echo "<p align='center'>Giỏ hàng của bạn đang trống</p>";
                echo "<p align='center'><a href='../sanpham/sanpham.php'>Mua sắm ngay</a></p>";
                exit();
            }
        }
        else {
            $giohang = GioHang::getAllGioHang();
            $tong_sp = sizeof($giohang);
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
        global $tongtien;
        $tongtien = 0;

        for ($i = 0; $i < $tong_sp; $i++) {
            $tongtien += $giohang[$i]["thanhtien"];
        ?>
            <tr align="center">
                <td><?php echo $i+1; ?></td>
                <td><img src="../../includes/img/<?php echo $giohang[$i]["hinhanh"] ?>" width="80"></td>
                <td><?php echo $giohang[$i]["tensp"]; ?></td>
                <td><?php echo $giohang[$i]["hang"]; ?></td>
                <td><?php echo number_format($giohang[$i]["gia"]); ?> VNĐ</td>
                <td>
                    <form action="giohang_edit.php" method="post" style="display:inline">
                        <input type="hidden" name="maitem" value="<?php echo $giohang[$i]["maitem"]; ?>">
                        <input type="number" name="soluong" value="<?php echo $giohang[$i]["sl"]; ?>" min="1" style="width:60px">
                        <input type="submit" value="Cập nhật">
                    </form>
                </td>
                <td><?php echo number_format($giohang[$i]["thanhtien"]); ?> VNĐ</td>
                <td>
                    <a href="giohang_del.php?maitem=<?php echo $giohang[$i]["maitem"]; ?>" 
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
