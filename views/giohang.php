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
    <link rel="stylesheet" href="/QLShopDT_API/assets/css/giohang.css">
</head>
<body>
    <?php
        require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/model/giohang/GioHang_db.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/model/giohang/GioHangAPI.php');

        include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/includes/header.php');
        include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/includes/footer.php');
        
        if (!isset($_SESSION['username'])) {
            echo "<p align='center'>Vui lòng <a href='/QLShopDT_API/views/login.php'>đăng nhập</a> để xem giỏ hàng</p>";
            exit();
        }

        $username = $_SESSION['username'];
        $role = $_SESSION['role'];
        
        global $giohang, $tong_sp;
        if ($role == 0){
            // Tìm mã khách hàng theo tên tài khoản
            $makh = GioHang_db::timMaKH($username);
        
            if (!$makh) {
                echo "Không tìm thấy thông tin khách hàng. Vui lòng đăng ký trước.";
                echo "<br><a href='/QLShopDT_API/view/register.php'>Quay lại</a>";
                exit();
            }

            $giohang = GioHangAPI::getGioHang($makh);
            
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
            $giohang = GioHangAPI::getAllGioHang();
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
                <td><img src="/QLShopDT_API/includes/img/<?php echo $giohang[$i]["hinhanh"] ?>" width="80"></td>
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