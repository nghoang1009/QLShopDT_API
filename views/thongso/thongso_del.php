<?php
/**
 * Xóa thông số kỹ thuật
 */
session_start();
require_once "../../includes/api_helper.php";

// Auth check
requireLogin();
requireRole([1, 2]); // Admin hoặc Nhân viên

$mats = $_GET['mats'] ?? 0;
$masp = $_GET['masp'] ?? 0;

if (empty($mats)) {
    setFlash('error', 'Không tìm thấy thông số cần xóa');
    header("Location: thongso.php?masp=$masp");
    exit();
}

// Gọi API để xóa thông số
$result = callAPI('DELETE', '/api/thongso/' . $mats);

if ($result && $result['status']) {
    setFlash('success', 'Xóa thông số thành công');
} else {
    setFlash('error', $result['message'] ?? 'Xóa thông số thất bại');
}

header("Location: thongso.php?masp=$masp");
exit();