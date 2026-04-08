<?php
include($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/api/db.php');
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../login.php");
    exit();
}

mysqli_set_charset($conn, "utf8");

$role = $_SESSION['role'] ?? 0;

if ($role == '0') {
    echo "<p align='center'>Bạn không có quyền xóa đơn hàng!</p>";
    echo "<p align='center'><a href='donhang.php'>Quay lại</a></p>";
    exit();
}

$madh = isset($_REQUEST["madh"]) ? $_REQUEST["madh"] : "";

if (empty($madh)) {
    echo "<p align='center'>Không tìm thấy đơn hàng!</p>";
    echo "<p align='center'><a href='donhang.php'>Quay lại</a></p>";
    exit();
}

// Hoàn trả số lượng sản phẩm vào kho
$sql_chitiet = "SELECT masp, sl FROM chitietdonhang WHERE madh = '$madh'";
$result_chitiet = mysqli_query($conn, $sql_chitiet);

while($row = mysqli_fetch_object($result_chitiet)) {
    $sql_update_kho = "UPDATE sanpham SET sl = sl + $row->sl WHERE masp = '$row->masp'";
    mysqli_query($conn, $sql_update_kho);
}

// Xóa chi tiết đơn hàng
$sql_delete_chitiet = "DELETE FROM chitietdonhang WHERE madh = '$madh'";
mysqli_query($conn, $sql_delete_chitiet);

// Xóa vận chuyển
$sql_delete_vanchuyen = "DELETE FROM vanchuyen WHERE madh = '$madh'";
mysqli_query($conn, $sql_delete_vanchuyen);

// Xóa đơn hàng
$sql_delete = "DELETE FROM donhang WHERE madh = '$madh'";

if (mysqli_query($conn, $sql_delete)) {
    echo "<p align='center'>Xóa đơn hàng thành công!</p>";
    echo "<p align='center'><a href='donhang.php'>Quay lại danh sách</a></p>";
} else {
    echo "<p align='center'>Lỗi: " . mysqli_error($conn) . "</p>";
    echo "<p align='center'><a href='donhang.php'>Quay lại</a></p>";
}

mysqli_close($conn);
?>
