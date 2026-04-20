<?php
/**
 * Xóa Nhân viên
 */
session_start();
require_once "../../includes/api_helper.php";

requireLogin();
requireRole([1]);

$manv = (int)($_GET['manv'] ?? 0);

if (!$manv) {
    setFlash('error', 'Không tìm thấy nhân viên');
    header("Location: nhanvien.php");
    exit();
}

$result = callAPI('DELETE', '/api/nhanvien/' . $manv);

if ($result && $result['status']) {
    setFlash('success', 'Xóa nhân viên thành công');
} else {
    setFlash('error', $result['message'] ?? 'Xóa nhân viên thất bại');
}

header("Location: nhanvien.php");
exit();
