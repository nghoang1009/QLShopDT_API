<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/QLShopDT_API/config/database.php');
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: ../auth/login.php");
    exit();
}

$role = $_SESSION['role'] ?? 0;

if ($role != 1 && $role != 2) {
    echo "<script>alert('Bạn không có quyền!'); window.location.href='thanhtoan.php';</script>";
    exit();
}

$madh = $_REQUEST["madh"];
$phuongthuc = $_REQUEST["phuongthuc"];
$sotien = $_REQUEST["sotien"];
$trangthai = $_REQUEST["trangthai"];
$ghichu = isset($_REQUEST["ghichu"]) ? $_REQUEST["ghichu"] : "";

$ngaythanhtoan = date('Y-m-d H:i:s');

$sql_insert = "INSERT INTO `thanhtoan` (`matt`, `madh`, `phuongthuc`, `ngaythanhtoan`, `sotien`, `trangthai`, `ghichu`) 
               VALUES (NULL, '$madh', '$phuongthuc', '$ngaythanhtoan', '$sotien', '$trangthai', '$ghichu')";

mysqli_query($conn, $sql_insert);
header("Location: thanhtoan.php");
?>
