<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm sản phẩm vào giỏ hàng</title>
</head>
<body>
    <?php
        include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');        
        session_start();
        
        if (!isset($_SESSION['username'])) {
            echo "<p align='center'>Vui lòng <a href='../login.php'>đăng nhập</a> để thêm sản phẩm vào giỏ hàng</p>";
            exit();
        }

        $masp = isset($_REQUEST["masp"]) ? $_REQUEST["masp"] : "";
        
        if (empty($masp)) {
            echo "<p align='center'>Không tìm thấy sản phẩm</p>";
            echo "<p align='center'><a href='../sanpham/sanpham.php'>Quay lại trang sản phẩm</a></p>";
            exit();
        }

        $sql_select = "SELECT sp.masp, sp.tensp, sp.gia, sp.sl, sp.hang, sp.hinhanh, sp.madm, dm.tendm 
                       FROM sanpham sp
                       JOIN danhmuc dm ON sp.madm = dm.madm
                       WHERE sp.masp = '$masp'";
        $result = mysqli_query($conn, $sql_select);
        
        if (!$result || mysqli_num_rows($result) == 0) {
            echo "<p align='center'>Không tìm thấy sản phẩm</p>";
            echo "<p align='center'><a href='../sanpham/sanpham.php'>Quay lại trang sản phẩm</a></p>";
            exit();
        }
        
        $row = mysqli_fetch_object($result);
    ?>

    <h1 align="center">THÊM SẢN PHẨM VÀO GIỎ HÀNG</h1>
    <h2 align="center"><a href="giohang.php">Giỏ hàng</a> | <a href="../sanpham/sanpham.php">Trang sản phẩm</a> | <a href="../trangchu.php">Trang chủ</a></h2>

    <form action="giohang_insert.php" method="post">
        <input type="hidden" name="txt_masp" value="<?php echo $row->masp; ?>">
        
        <table align="center" border="1">
            <tr>
                <td colspan="2" align="center"><strong>Thông tin sản phẩm</strong></td>
            </tr>
            <tr>
                <td>Hình ảnh:</td>
                <td align="center">
                    <img src="../img/<?php echo $row->hinhanh; ?>" width="150">
                </td>
            </tr>
            <tr>
                <td>Tên sản phẩm:</td>
                <td><?php echo $row->tensp; ?></td>
            </tr>
            <tr>
                <td>Hãng:</td>
                <td><?php echo $row->hang; ?></td>
            </tr>
            <tr>
                <td>Danh mục:</td>
                <td><?php echo $row->tendm; ?></td>
            </tr>
            <tr>
                <td>Giá:</td>
                <td><strong><?php echo number_format($row->gia); ?> VNĐ</strong></td>
            </tr>
            <tr>
                <td>Số lượng còn:</td>
                <td><?php echo $row->sl; ?> sản phẩm</td>
            </tr>
            <tr>
                <td>Số lượng mua:</td>
                <td>
                    <input type="number" name="num_soluong" value="1" min="1" max="<?php echo $row->sl; ?>" required>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="Thêm vào giỏ hàng">
                    <input type="button" value="Quay lại" onclick="window.location.href='../sanpham/sanpham.php'">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
