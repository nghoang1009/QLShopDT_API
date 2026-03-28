<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tạo đơn hàng</title>
</head>
<body>
    <?php
    include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
    session_start();
    
    if (!isset($_SESSION['username'])) {
        header("Location: ../login.php");
        exit();
    }

    $username = $_SESSION['username'];
    mysqli_set_charset($conn, "utf8");

    // Lấy mã khách hàng
    $sql_get_user = "SELECT kh.makh 
                     FROM taikhoan tk
                     JOIN khachhang kh ON tk.matk = kh.makh
                     WHERE tk.tentk = '$username'";
    $result_user = mysqli_query($conn, $sql_get_user);
    
    if (!$result_user || mysqli_num_rows($result_user) == 0) {
        echo "<p align='center'>Không tìm thấy thông tin khách hàng</p>";
        echo "<p align='center'><a href='giohang.php'>Quay lại</a></p>";
        exit();
    }
    
    $row_user = mysqli_fetch_object($result_user);
    $makh = $row_user->makh;

    // Lấy giỏ hàng
    $sql_get_giohang = "SELECT magio FROM giohang WHERE makh = '$makh'";
    $result_giohang = mysqli_query($conn, $sql_get_giohang);
    
    if (!$result_giohang || mysqli_num_rows($result_giohang) == 0) {
        echo "<p align='center'>Giỏ hàng trống</p>";
        echo "<p align='center'><a href='giohang.php'>Quay lại</a></p>";
        exit();
    }
    
    $row_giohang = mysqli_fetch_object($result_giohang);
    $magio = $row_giohang->magio;

    // Lấy các sản phẩm trong giỏ hàng
    $sql_items = "SELECT gi.masp, gi.sl, sp.gia, sp.sl as sl_kho
                  FROM giohang_item gi
                  JOIN sanpham sp ON gi.masp = sp.masp
                  WHERE gi.magio = '$magio'";
    $result_items = mysqli_query($conn, $sql_items);
    
    if (!$result_items || mysqli_num_rows($result_items) == 0) {
        echo "<p align='center'>Giỏ hàng trống</p>";
        echo "<p align='center'><a href='giohang.php'>Quay lại</a></p>";
        exit();
    }

    // Kiểm tra số lượng tồn kho
    $trigia = 0;
    $items = array();
    while($row = mysqli_fetch_object($result_items)) {
        if ($row->sl > $row->sl_kho) {
            echo "<p align='center'>Sản phẩm mã " . $row->masp . " không đủ số lượng trong kho</p>";
            echo "<p align='center'><a href='giohang.php'>Quay lại</a></p>";
            exit();
        }
        $items[] = $row;
        $trigia += $row->gia * $row->sl;
    }

    // Lấy mã nhân viên
    $sql_get_nv = "SELECT manv FROM nhanvien LIMIT 1";
    $result_nv = mysqli_query($conn, $sql_get_nv);
    $row_nv = mysqli_fetch_object($result_nv);
    $manv = isset($row_nv->manv) ? $row_nv->manv : 1;

    // Tạo đơn hàng
    $ngaydat = date('Y-m-d');
    
    $sql_create_donhang = "INSERT INTO donhang (madh, makh, ngaydat, manv, trigia) 
                           VALUES (NULL, '$makh', '$ngaydat', '$manv', '$trigia')";
    
    if (!mysqli_query($conn, $sql_create_donhang)) {
        die("Lỗi tạo đơn hàng: " . mysqli_error($conn));
    }
    
    $madh = mysqli_insert_id($conn);

    // Thêm chi tiết đơn hàng và cập nhật số lượng tồn kho
    foreach($items as $item) {
        $sql_insert_chitiet = "INSERT INTO chitietdonhang (madh, masp, sl) 
                               VALUES ('$madh', '$item->masp', '$item->sl')";
        mysqli_query($conn, $sql_insert_chitiet);
        
        // Cập nhật số lượng tồn kho
        $sl_moi = $item->sl_kho - $item->sl;
        $sql_update_kho = "UPDATE sanpham SET sl = '$sl_moi' WHERE masp = '$item->masp'";
        mysqli_query($conn, $sql_update_kho);
    }

    // Xóa giỏ hàng
    $sql_delete_items = "DELETE FROM giohang_item WHERE magio = '$magio'";
    mysqli_query($conn, $sql_delete_items);

    mysqli_close($conn);
    ?>
    
    <h2 align="center">ĐẶT HÀNG THÀNH CÔNG!</h2>
    <p align="center">Mã đơn hàng của bạn: <strong><?php echo $madh; ?></strong></p>
    <p align="center">Tổng tiền: <strong><?php echo number_format($trigia); ?> VNĐ</strong></p>
    <p align="center">
        <a href="donhang_chitiet.php?madh=<?php echo $madh; ?>">Xem chi tiết đơn hàng</a> | 
        <a href="donhang.php">Xem tất cả đơn hàng</a> | 
        <a href="../trangchu.php">Về trang chủ</a>
    </p>
</body>
</html>
