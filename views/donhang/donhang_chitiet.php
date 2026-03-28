<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chi tiết đơn hàng</title>
</head>
<body>
    <h1 align="center">CHI TIẾT ĐƠN HÀNG</h1>
    <h2 align="center">
        <a href="donhang.php">Danh sách đơn hàng</a> | 
        <a href="../trangchu.php">Trang chủ</a>
    </h2>
    
    <?php
        include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        if (!isset($_SESSION['username'])) {
            echo "<p align='center'>Vui lòng <a href='../login.php'>đăng nhập</a></p>";
            exit();
        }

        $madh = isset($_REQUEST["madh"]) ? $_REQUEST["madh"] : "";
        
        if (empty($madh)) {
            echo "<p align='center'>Không tìm thấy đơn hàng</p>";
            exit();
        }

        mysqli_set_charset($conn, "utf8");
        
        // Lấy thông tin đơn hàng
        $sql_donhang = "SELECT dh.madh, dh.ngaydat, dh.trigia,
                               kh.tenkh, kh.diachi, kh.sdt
                        FROM donhang dh
                        JOIN khachhang kh ON dh.makh = kh.makh
                        WHERE dh.madh = '$madh'";
        $result_donhang = mysqli_query($conn, $sql_donhang);
        
        if (!$result_donhang || mysqli_num_rows($result_donhang) == 0) {
            echo "<p align='center'>Không tìm thấy đơn hàng</p>";
            exit();
        }
        
        $donhang = mysqli_fetch_object($result_donhang);
        
        // Lấy chi tiết đơn hàng
        $sql_chitiet = "SELECT ctdh.masp, ctdh.sl,
                               sp.tensp, sp.hinhanh, sp.hang, sp.gia
                        FROM chitietdonhang ctdh
                        JOIN sanpham sp ON ctdh.masp = sp.masp
                        WHERE ctdh.madh = '$madh'";
        $result_chitiet = mysqli_query($conn, $sql_chitiet);
        
        if (!$result_chitiet) {
            die("Lỗi: " . mysqli_error($conn));
        }
        
        $tong_sp = mysqli_num_rows($result_chitiet);
        
        $stt = 0;
        while($row = mysqli_fetch_object($result_chitiet)) {
            $stt++;
            $masp[$stt] = $row->masp;
            $tensp[$stt] = $row->tensp;
            $gia[$stt] = $row->gia;
            $soluong[$stt] = $row->sl;
            $hinhanh[$stt] = $row->hinhanh;
            $hang[$stt] = $row->hang;
            $thanhtien[$stt] = $gia[$stt] * $soluong[$stt];
        }
    ?>

    <table width="900" align="center" border="1">
        <tr>
            <th colspan="2"><strong>Thông tin đơn hàng</strong></th >
        </tr>
        <tr>
            <td width="200">Mã đơn hàng:</td>
            <td><strong><?php echo $donhang->madh; ?></strong></td>
        </tr>
        <tr>
            <td>Ngày đặt:</td>
            <td><?php echo date('d/m/Y', strtotime($donhang->ngaydat)); ?></td>
        </tr>
        <tr>
            <td>Khách hàng:</td>
            <td><?php echo $donhang->tenkh; ?></td>
        </tr>
        <tr>
            <td>Địa chỉ:</td>
            <td><?php echo $donhang->diachi; ?></td>
        </tr>
        <tr>
            <td>Số điện thoại:</td>
            <td><?php echo $donhang->sdt; ?></td>
        </tr>
    </table>

    <br>

    <table width="1200" align="center" border="1">
        <tr>
            <th colspan="7"><strong>Chi tiết sản phẩm</strong></th>
        </tr>
        <tr>
            <th>STT</th>
            <th>Hình ảnh</th>
            <th>Tên sản phẩm</th>
            <th>Hãng</th>
            <th>Đơn giá</th>
            <th>Số lượng</th>
            <th>Thành tiền</th>
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
                <td><?php echo $soluong[$i]; ?></td>
                <td><?php echo number_format($thanhtien[$i]); ?> VNĐ</td>
            </tr>
        <?php
        }
        ?>
        <tr>
            <td colspan="6" align="right"><strong>Tổng cộng:</strong></td>
            <td align="center"><strong><?php echo number_format($donhang->trigia); ?> VNĐ</strong></td>
        </tr>
    </table>
</body>
</html>
