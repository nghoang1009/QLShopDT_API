<?php
/**
 * Xóa Khách hàng
 */
session_start();
require_once "../../includes/api_helper.php";

requireLogin();
requireRole([1, 2]);

$makh = (int)($_GET['makh'] ?? 0);

if (!$makh) {
    setFlash('error', 'Không tìm thấy khách hàng');
    header("Location: khachhang.php");
    exit();
}

$result = callAPI('DELETE', '/api/khachhang/' . $makh);

if ($result && $result['status']) {
    setFlash('success', 'Xóa khách hàng thành công');
} else {
    setFlash('error', $result['message'] ?? 'Xóa khách hàng thất bại');
}

header("Location: khachhang.php");
exit();