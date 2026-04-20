<?php
/**
 * Xóa danh mục
 */
session_start();
require_once "../../includes/api_helper.php";

// Auth check
requireLogin();
requireRole([1, 2]); // Admin hoặc Nhân viên

$madm = $_GET['madm'] ?? 0;

if (empty($madm)) {
    setFlash('error', 'Không tìm thấy danh mục');
    header("Location: danhmuc.php");
    exit();
}

// Gọi RESTful API xóa
$result = callAPI('DELETE', '/api/danhmuc/' . (int)$madm);

if ($result && $result['status']) {
    setFlash('success', 'Xóa danh mục thành công');
} else {
    setFlash('error', $result['message'] ?? 'Xóa danh mục thất bại');
}

header("Location: danhmuc.php");
exit();
