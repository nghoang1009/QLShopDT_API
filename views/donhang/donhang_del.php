<?php
/**
 * Xóa Đơn hàng
 */
session_start();
require_once "../../includes/api_helper.php";

requireLogin();
requireRole([1, 2]);

$madh = (int)($_GET['madh'] ?? 0);

if (!$madh) {
    setFlash('error', 'Không tìm thấy đơn hàng');
    header("Location: donhang.php");
    exit();
}

$result = callAPI('DELETE', '/api/donhang/' . $madh);

if ($result && $result['status']) {
    setFlash('success', 'Xóa đơn hàng #' . $madh . ' thành công');
} else {
    setFlash('error', $result['message'] ?? 'Xóa đơn hàng thất bại');
}

header("Location: donhang.php");
exit();