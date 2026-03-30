<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/main.css">
    <title>Thống kê doanh thu</title>
    <html>
    <link rel="stylesheet" href="/QLShopDT_API/assets/css/sanpham.css">;
</html>
</head>
<body>
    <?php
    $extra_css = '<link rel="stylesheet" href="/QLShopDT_API/assets/css/footer.css">';
    include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
    include "../../includes/header.php";
    include "../../includes/footer.php";

    $sql_select = "SELECT tt.*, dh.ngaydat, kh.tenkh, nv.tennv 
                   FROM thanhtoan tt
                   JOIN donhang dh ON tt.madh = dh.madh
                   JOIN khachhang kh ON dh.makh = kh.makh
                   JOIN nhanvien nv ON dh.manv = nv.manv
                   WHERE 1=1";
 
    if (isset($_POST['dayChecked'])) {
        $dayChecked = $_POST['dayChecked'];
        if ($dayChecked) {
            $day = $_POST['day'] != null ? (int)$_POST['day'] : 10;
            $sql_select .= " AND DAY(dh.ngaydat) = '$day'";
        }
    }


    if (isset($_POST['monthChecked'])){
        $monthChecked = $_POST['monthChecked'];
        if ($monthChecked) {
            $month = $_POST['month'] != null ? (int)$_POST['month'] : 1;
            $sql_select .= " AND MONTH(dh.ngaydat) = '$month'";
        }
    }


    if (isset($_POST['yearChecked'])) {
        $yearChecked = $_POST['yearChecked'];
        if ($yearChecked) {
            $year = $_POST['year'] != null ? (int)$_POST['year'] : 2025;
            $sql_select .= " AND YEAR(dh.ngaydat) = '$year'";
        }
    }

    if (isset($_POST['phuongThuc'])) {
        $phuongThucThanhToan = $_POST['phuongThuc'];
        if ($phuongThucThanhToan != "Tất cả")
            $sql_select .= " AND phuongThuc = '$phuongThucThanhToan'";
    }


    if (isset($_POST['trangThai'])) {
        $trangThaiThanhToan = $_POST['trangThai'];
        if ($trangThaiThanhToan != "Tất cả")
            $sql_select .= " AND trangThai = '$trangThaiThanhToan'";
    }

    $result = mysqli_query($conn, $sql_select);
    $tong_bg = mysqli_num_rows($result);

    $stt = 0;
    while($row = mysqli_fetch_assoc($result)) {
        $stt++;
        $phuongthuc[$stt] = $row['phuongthuc'];
        $ngaythanhtoan[$stt] = $row['ngaythanhtoan'];
        $sotien[$stt] = $row['sotien'];
        $trangthai[$stt] = $row['trangthai'];
        $ghichu[$stt] = $row['ghichu'];
        $tenkh[$stt] = $row['tenkh'];
        $tennv[$stt] = $row['tennv'];
    }
    ?>

    <div class="filters">
        <h3>Lọc theo:<h3>
        <h4>Thời gian thanh toán:</h4><hr>
        <form action="thongke.php" method="post">
            
            <input type="checkbox" name="dayChecked" id="dayChecked" <?php if (isset($dayChecked) && $dayChecked) echo 'checked';?>>
            <label for="dayChecked">Ngày</label>
            <input type="number" name="day" id="day" value="<?php echo $day; ?>">
            <br>

            <input type="checkbox" name="monthChecked" id="monthChecked" <?php if (isset($monthChecked) && $monthChecked) echo 'checked';?>>
            <label for="monthChecked">Tháng</label>
            <input type="number" name="month" id="month" value="<?php echo $month; ?>">
            <br>

            <input type="checkbox" name="yearChecked" id="yearChecked" <?php if (isset ($yearChecked) && $yearChecked) echo 'checked';?>>
            <label for="yearChecked">Năm</label>
            <input type="number" name="year" id="year" value="<?php echo $year; ?>">
            <br>

            <h4>Phương thức thanh toán:</h4><hr>
            <select name="phuongThuc" id="phuongThuc">
                <option value="Tất cả" selected>Tất cả</option>
                <option value="Chuyển khoản" <?php if ($phuongThucThanhToan === "Chuyển khoản") echo "selected"; ?>>Chuyển khoản</option>
                <option value="Tiền mặt" <?php if ($phuongThucThanhToan === "Tiền mặt") echo "selected"; ?>>Tiền mặt</option>
                <option value="Thẻ" <?php if ($phuongThucThanhToan === "Thẻ") echo "selected"; ?>>Thẻ</option>
                <option value="Ví điện tử" <?php if ($phuongThucThanhToan === "Ví điện tử") echo "selected"; ?>>Ví điện tử</option>
            </select>

            <h4 style="margin-top: 10px;">Trạng thái thanh toán:</h4><hr>
            <select name="trangThai" id="trangThai">
                <option value="Tất cả" selected>Tất cả</option>
                <option value="Chờ xác nhận" <?php if ($trangThaiThanhToan === "Chờ xác nhận") echo "selected"; ?>>Chờ xác nhận</option>
                <option value="Đã thanh toán" <?php if ($trangThaiThanhToan === "Đã thanh toán") echo "selected"; ?>>Đã thanh toán</option>
                <option value="Thất bại" <?php if ($trangThaiThanhToan === "Thất bại") echo "selected"; ?>>Thất bại</option>
            </select>
            
            <input type="submit" value="Lọc" style="margin-top: 10px;">
        </form>
    </div>

    <h1 align="center">THỐNG KÊ DOANH THU</h1>
    <table>
        <tr>
            <th>STT</th>
            <th>Khách hàng</th>
            <th>Nhân viên</th>
            <th>Phương thức</th>
            <th>Ngày thanh toán</th>
            <th>Số tiền</th>
            <th>Trạng thái</th>
            <th>Ghi chú</th>
        </tr>

    <?php
        for ($i=1; $i<=$tong_bg; $i++) {
    ?>
        <tr align="center">
            <td><?php echo $i; ?></td>
            <td><?php echo $tenkh[$i]; ?></td>
            <td><?php echo $tennv[$i]; ?></td>
            <td><?php echo $phuongthuc[$i]; ?></td>
            <td><?php echo date('d/m/Y H:i', strtotime($ngaythanhtoan[$i])); ?></td>
            <td><?php echo number_format($sotien[$i], 0, ',', '.'); ?> đ</td>
            <td><?php echo $trangthai[$i]; ?></td>
            <td><?php echo $ghichu[$i]; ?></td>
        </tr>
    <?php
        }
    ?>

        <tr>
            <td colspan="8" align="right">
                <?php if (isset($sotien)) echo "Tổng thu:", number_format(array_sum($sotien), 0, ',', '.'), "đ";
                        else echo "Không có hóa đơn nào được tìm thấy!";
                ?>
            </td>
        </tr>
    </table>
</body>
</html>