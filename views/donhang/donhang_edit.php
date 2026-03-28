<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sửa đơn hàng</title>
</head>
<body>
    <h1 align="center">SỬA ĐƠN HÀNG</h1>
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
        mysqli_set_charset($conn, "utf8");
        
        $sql_get_role = "SELECT role FROM taikhoan WHERE tentk = '$username'";
        $result_role = mysqli_query($conn, $sql_get_role);
        $row_role = mysqli_fetch_object($result_role);
        
        if ($row_role->role == '0') {
            echo "<p align='center'>Bạn không có quyền sửa đơn hàng!</p>";
            echo "<p align='center'><a href='donhang.php'>Quay lại</a></p>";
            exit();
        }

        $madh = isset($_REQUEST["madh"]) ? $_REQUEST["madh"] : "";
        
        if (empty($madh)) {
            echo "<p align='center'>Không tìm thấy đơn hàng</p>";
            exit();
        }

        $sql_donhang = "SELECT dh.madh, dh.ngaydat, dh.trigia, dh.makh, dh.manv,
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
        
        $sql_nhanvien = "SELECT manv, tennv FROM nhanvien ORDER BY tennv";
        $result_nhanvien = mysqli_query($conn, $sql_nhanvien);
    ?>

    <form method="POST" action="donhang_edit_save.php">
        <input type="hidden" name="madh" value="<?php echo $donhang->madh; ?>">
        
        <table width="800" align="center" border="1">
            <tr>
                <td colspan="2"><strong>THÔNG TIN ĐƠN HÀNG</strong></td>
            </tr>
            <tr>
                <td width="200">Mã đơn hàng:</td>
                <td><strong><?php echo $donhang->madh; ?></strong></td>
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
            <tr>
                <td>Ngày đặt:</td>
                <td>
                    <input type="date" name="ngaydat" value="<?php echo $donhang->ngaydat; ?>" required>
                </td>
            </tr>
            <tr>
                <td>Nhân viên xử lý:</td>
                <td>
                    <select name="manv" required>
                        <option value="">-- Chọn nhân viên --</option>
                        <?php while($nv = mysqli_fetch_object($result_nhanvien)): ?>
                            <option value="<?php echo $nv->manv; ?>" 
                                <?php echo ($nv->manv == $donhang->manv) ? 'selected' : ''; ?>>
                                <?php echo $nv->tennv; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Tổng tiền:</td>
                <td>
                    <input type="number" name="trigia" value="<?php echo $donhang->trigia; ?>" 
                           step="1000" min="0" required>
                </td>
            </tr>
            <tr>
                <td colspan="2" align="center">
                    <input type="submit" value="Lưu">
                    <a href="donhang.php">Hủy</a>
                </td>
            </tr>
        </table>
    </form>

    <br>
    <h3 align="center">Danh sách sản phẩm</h3>
    
    <?php
        $sql_chitiet = "SELECT ctdh.masp, ctdh.sl,
                               sp.tensp, sp.gia
                        FROM chitietdonhang ctdh
                        JOIN sanpham sp ON ctdh.masp = sp.masp
                        WHERE ctdh.madh = '$madh'";
        $result_chitiet = mysqli_query($conn, $sql_chitiet);
    ?>

    <table width="1000" align="center" border="1">
        <tr>
            <th>STT</th>
            <th>Mã SP</th>
            <th>Tên sản phẩm</th>
            <th>Đơn giá</th>
            <th>Số lượng</th>
            <th>Thành tiền</th>
        </tr>
        <?php
        $stt = 0;
        while($row = mysqli_fetch_object($result_chitiet)) {
            $stt++;
            $thanhtien = $row->gia * $row->sl;
        ?>
        <tr align="center">
            <td><?php echo $stt; ?></td>
            <td><?php echo $row->masp; ?></td>
            <td><?php echo $row->tensp; ?></td>
            <td><?php echo number_format($row->gia); ?> VNĐ</td>
            <td><?php echo $row->sl; ?></td>
            <td><?php echo number_format($thanhtien); ?> VNĐ</td>
        </tr>
        <?php } ?>
    </table>

    <?php mysqli_close($conn); ?>
</body>
</html>
