<?php
/**
 * Xóa sản phẩm
 */
session_start();
require_once "../../includes/api_helper.php";

// Auth check
requireLogin();
requireRole([1, 2]); // Admin hoặc Nhân viên

$masp = $_GET['masp'] ?? 0;

if (empty($masp)) {
    setFlash('error', 'Không tìm thấy sản phẩm');
    header("Location: sanpham.php");
    exit();
}

// Gọi RESTful API xóa
$result = callAPI('DELETE', '/api/sanpham/' . (int)$masp);

if ($result && $result['status']) {
    setFlash('success', 'Xóa sản phẩm thành công');
} else {
    setFlash('error', $result['message'] ?? 'Xóa sản phẩm thất bại');
}

header("Location: sanpham.php");
exit();