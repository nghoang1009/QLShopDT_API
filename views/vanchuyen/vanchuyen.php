<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý vận chuyển</title>
</head>
<body>
    
    <?php
        $extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';
        include "../../includes/footer.php";
        include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
        include "../../includes/header.php";


        if (!isset($_SESSION['username'])) {
            echo "<p align='center'>Vui lòng <a href='../login.php'>đăng nhập</a> để xem thông tin vận chuyển</p>";
            exit();
        }

        $username = $_SESSION['username'];
        mysqli_set_charset($conn, "utf8");
        
        // Lấy role của user
        $sql_get_role = "SELECT role FROM taikhoan WHERE tentk = '$username'";
        $result_role = mysqli_query($conn, $sql_get_role);
        $row_role = mysqli_fetch_object($result_role);
        $role = $row_role->role;

        // Nếu là khách hàng, chỉ xem vận chuyển của mình
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
            
            $sql_select = "SELECT vc.mavc, vc.madh, vc.ngaygiao,
                                  kh.tenkh, kh.diachi, kh.sdt,
                                  dh.ngaydat, dh.trigia
                           FROM vanchuyen vc
                           JOIN khachhang kh ON vc.makh = kh.makh
                           JOIN donhang dh ON vc.madh = dh.madh
                           WHERE vc.makh = '$makh'
                           ORDER BY vc.mavc DESC";
        } else {
            // Admin hoặc nhân viên xem tất cả vận chuyển
            $sql_select = "SELECT vc.mavc, vc.madh, vc.ngaygiao, vc.makh,
                                  kh.tenkh, kh.diachi, kh.sdt,
                                  dh.ngaydat, dh.trigia
                           FROM vanchuyen vc
                           JOIN khachhang kh ON vc.makh = kh.makh
                           JOIN donhang dh ON vc.madh = dh.madh
                           ORDER BY vc.mavc DESC";
        }
        
        $result = mysqli_query($conn, $sql_select);
        
        if (!$result) {
            die("Lỗi truy vấn: " . mysqli_error($conn));
        }
        
        $tong_vc = mysqli_num_rows($result);

        $stt = 0;
        while($row = mysqli_fetch_object($result)) {
            $stt++;
            $mavc[$stt] = $row->mavc;
            $madh[$stt] = $row->madh;
            $ngaygiao[$stt] = $row->ngaygiao;
            $tenkh[$stt] = $row->tenkh;
            $diachi[$stt] = $row->diachi;
            $sdt[$stt] = $row->sdt;
            $ngaydat[$stt] = $row->ngaydat;
            $trigia[$stt] = $row->trigia;
        }
    ?>
    <br>
    <h1 align="center">QUẢN LÝ VẬN CHUYỂN</h1>
    <table width="1400" align="center" border="1">
        <tr>
            <th>STT</th>
            <th>Mã vận chuyển</th>
            <th>Mã đơn hàng</th>
            <th>Tên khách hàng</th>
            <th>Địa chỉ giao hàng</th>
            <th>SĐT</th>
            <th>Ngày đặt</th>
            <th>Ngày giao</th>
            <th>Tổng tiền</th>
            <?php if ($role != '0'): ?>
            <th><a href="vanchuyen_add.php">Thêm vận chuyển</a></th>
            <?php endif; ?>
        </tr>

        <?php
        if ($tong_vc > 0) {
            for ($i=1; $i<=$tong_vc; $i++) {
        ?>
            <tr align="center">
                <td><?php echo $i; ?></td>
                <td><?php echo $mavc[$i]; ?></td>
                <td><a href="../donhang/donhang_chitiet.php?madh=<?php echo $madh[$i]; ?>"><?php echo $madh[$i]; ?></a></td>
                <td><?php echo $tenkh[$i]; ?></td>
                <td><?php echo $diachi[$i]; ?></td>
                <td><?php echo $sdt[$i]; ?></td>
                <td><?php echo date('d/m/Y', strtotime($ngaydat[$i])); ?></td>
                <td><?php echo date('d/m/Y', strtotime($ngaygiao[$i])); ?></td>
                <td><?php echo number_format($trigia[$i]); ?> VNĐ</td>
                <?php if ($role != '0'): ?>
                <td>
                    <a href="vanchuyen_edit.php?mavc=<?php echo $mavc[$i]; ?>">Sửa</a> |
                    <a href="vanchuyen_del.php?mavc=<?php echo $mavc[$i]; ?>" 
                       onclick="return confirm('Bạn có chắc muốn xóa thông tin vận chuyển này?')">Xóa</a>
                </td>
                <?php endif; ?>
            </tr>
        <?php
            }
        } else {
            $colspan = ($role != '0') ? '10' : '9';
            echo "<tr><td colspan='$colspan' align='center'>Chưa có thông tin vận chuyển nào</td></tr>";
        }
        ?>
        <tr>
            <td colspan="<?php echo ($role != '0') ? '10' : '9'; ?>" align="right">Tổng số: <?php echo $tong_vc; ?> đơn vận chuyển</td>
        </tr>
    </table>
</body>
</html>
