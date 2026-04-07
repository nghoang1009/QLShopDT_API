<?php
include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

$masp = isset($_REQUEST["masp"]) ? $_REQUEST["masp"] : "";
$soluong = isset($_REQUEST["soluong"]) ? $_REQUEST["soluong"] : 1;

if (empty($masp)) {
    echo "Không tìm thấy sản phẩm";
    exit();
}

// Lấy mã khách hàng từ bảng khachhang thông qua taikhoan
$sql_get_user = "SELECT kh.makh 
                 FROM taikhoan tk
                 JOIN khachhang kh ON tk.matk = kh.makh
                 WHERE tk.tentk = '$username'";
$result_user = mysqli_query($conn, $sql_get_user);

if (!$result_user || mysqli_num_rows($result_user) == 0) {
    echo "Không tìm thấy thông tin khách hàng. Vui lòng đăng ký thông tin khách hàng trước.";
    exit();
}

$row_user = mysqli_fetch_object($result_user);
$makh = $row_user->makh;

// Kiểm tra xem khách hàng đã có giỏ hàng chưa
$sql_check_giohang = "SELECT magio FROM giohang WHERE makh = '$makh'";
$result_giohang = mysqli_query($conn, $sql_check_giohang);

if (!$result_giohang) {
    die("Lỗi: " . mysqli_error($conn));
}

if (mysqli_num_rows($result_giohang) == 0) {
    // Chưa có giỏ hàng, tạo mới
    $sql_create_giohang = "INSERT INTO giohang (magio, makh) VALUES (NULL, '$makh')";
    mysqli_query($conn, $sql_create_giohang);
    $magio = mysqli_insert_id($conn);
} else {
    // Đã có giỏ hàng
    $row_giohang = mysqli_fetch_object($result_giohang);
    $magio = $row_giohang->magio;
}

// Kiểm tra sản phẩm đã có trong giỏ hàng chưa
$sql_check = "SELECT * FROM giohang_item WHERE magio = '$magio' AND masp = '$masp'";
$result_check = mysqli_query($conn, $sql_check);

if (!$result_check) {
    die("Lỗi: " . mysqli_error($conn));
}

if (mysqli_num_rows($result_check) > 0) {
    // Nếu đã có thì cập nhật số lượng
    $row = mysqli_fetch_object($result_check);
    $soluong_moi = $row->sl + $soluong;
    $sql_update = "UPDATE giohang_item SET sl = '$soluong_moi' WHERE magio = '$magio' AND masp = '$masp'";
    mysqli_query($conn, $sql_update);
} else {
    // Nếu chưa có thì thêm mới
    $sql_insert = "INSERT INTO giohang_item (maitem, magio, masp, sl) 
                   VALUES (NULL, '$magio', '$masp', '$soluong')";
    mysqli_query($conn, $sql_insert);
}

header("Location: giohang.php");
?>
