<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý đơn hàng</title>
</head>
<body>
    
    <?php
        if (!isset($_SESSION['username'])) {
            echo "<p align='center'>Vui lòng <a href='../login.php'>đăng nhập</a> để xem đơn hàng</p>";
            exit();
        }
        $username = $_SESSION['username'];
        $extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';
        include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        include "../../includes/header.php";
        include "../../includes/footer.php";
        mysqli_set_charset($conn, "utf8");
        
        $role = $_SESSION['role'] ?? 0;

        // Nếu là khách hàng, chỉ xem đơn hàng của mình
        if ($role == '0') {
            $sql_get_makh = "SELECT kh.makh 
                             FROM taikhoan tk
                             JOIN khachhang kh ON tk.matk = kh.makh
                             WHERE tk.tentk = '$username'";
            $result_makh = mysqli_query($conn, $sql_get_makh);
            
            if (mysqli_num_rows($result_makh) == 0) {
                echo "<p align='center'>Không tìm thấy thông tin khách hàng</p>";
                exit();
            }
            
            $row_makh = mysqli_fetch_object($result_makh);
            $makh = $row_makh->makh;
            
            $sql_select = "SELECT dh.madh, dh.ngaydat, dh.trigia, 
                                  kh.tenkh, kh.diachi, kh.sdt
                           FROM donhang dh
                           JOIN khachhang kh ON dh.makh = kh.makh
                           WHERE dh.makh = '$makh'
                           ORDER BY dh.madh DESC";
        } else {
            // Admin hoặc nhân viên xem tất cả đơn hàng
            $sql_select = "SELECT dh.madh, dh.ngaydat, dh.trigia, 
                                  kh.tenkh, kh.diachi, kh.sdt, dh.makh
                           FROM donhang dh
                           JOIN khachhang kh ON dh.makh = kh.makh
                           ORDER BY dh.madh DESC";
        }
        
        $result = mysqli_query($conn, $sql_select);
        
        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($conn));
        }
        
        $tong_dh = mysqli_num_rows($result);

        $stt = 0;
        while($row = mysqli_fetch_object($result)) {
            $stt++;
            $madh[$stt] = $row->madh;
            $ngaydat[$stt] = $row->ngaydat;
            $trigia[$stt] = $row->trigia;
            $tenkh[$stt] = $row->tenkh;
            $diachi[$stt] = $row->diachi;
            $sdt[$stt] = $row->sdt;
        }
    ?>
    <h1 align="center">QUẢN LÝ ĐƠN HÀNG</h1>
    <table width="1500" align="center" border="1">
        <tr>
            <th>STT</th>
            <th>Mã đơn hàng</th>
            <th>Tên khách hàng</th>
            <th>Địa chỉ</th>
            <th>SĐT</th>
            <th>Ngày đặt</th>
            <th>Tổng tiền</th>
            <th>Chi tiết</th>
            <?php if ($role != '0'): ?>
            <th>Thao tác</th>
            <?php endif; ?>
        </tr>

        <?php
        if ($tong_dh > 0) {
            for ($i=1; $i<=$tong_dh; $i++) {
        ?>
            <tr align="center">
                <td><?php echo $i; ?></td>
                <td><?php echo $madh[$i]; ?></td>
                <td><?php echo $tenkh[$i]; ?></td>
                <td><?php echo $diachi[$i]; ?></td>
                <td><?php echo $sdt[$i]; ?></td>
                <td><?php echo date('d/m/Y', strtotime($ngaydat[$i])); ?></td>
                <td><?php echo number_format($trigia[$i]); ?> VNĐ</td>
                <td>
                    <a href="donhang_chitiet.php?madh=<?php echo $madh[$i]; ?>">Xem chi tiết</a>
                </td>
                <?php if ($role != '0'): ?>
                <td>
                    <a href="donhang_edit.php?madh=<?php echo $madh[$i]; ?>">Cập nhật</a> |
                    <a href="donhang_del.php?madh=<?php echo $madh[$i]; ?>" 
                       onclick="return confirm('Bạn có chắc muốn xóa đơn hàng này?')">Xóa</a>
                </td>
                <?php endif; ?>
            </tr>
        <?php
            }
        } else {
            $colspan = ($role != '0') ? '9' : '8';
            echo "<tr><td colspan='$colspan' align='center'>Chưa có đơn hàng nào</td></tr>";
        }
        ?>
        <tr>
            <td colspan="<?php echo ($role != '0') ? '9' : '8'; ?>" align="right">Tổng số: <?php echo $tong_dh; ?> đơn hàng</td>
        </tr>
    </table>
</body>
</html>
