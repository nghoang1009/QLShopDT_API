<?php
/**
 * Vận chuyển API - Sử dụng Model
 */
header("Content-Type: application/json; charset=utf-8");

// Load Model
require_once dirname(__DIR__) . '/model/VanChuyen.php';

// Đọc dữ liệu từ input
$data   = json_decode(file_get_contents("php://input"), true);
$action = isset($data['action']) ? $data['action'] : '';

// Khởi tạo Model
$model = new VanChuyen();

// XEM TẤT CẢ VẬN CHUYỂN
if ($action == 'getall') {
    $shipments = $model->getAllWithDetails();

    echo json_encode([
        "status"  => true,
        "message" => "Lấy danh sách vận chuyển thành công",
        "data"    => $shipments ?: [],
        "total"   => $shipments ? count($shipments) : 0
    ], JSON_UNESCAPED_UNICODE);
}

// XEM CHI TIẾT 1 VẬN CHUYỂN
else if ($action == 'getone') {
    $mavc = isset($data['mavc']) ? (int)$data['mavc'] : 0;
    $shipment = $model->getOneWithDetails($mavc);

    if ($shipment) {
        echo json_encode([
            "status"  => true,
            "message" => "Lấy thông tin vận chuyển thành công",
            "data"    => $shipment
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status"  => false,
            "message" => "Không tìm thấy vận chuyển"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// LẤY VẬN CHUYỂN THEO ĐƠN HÀNG
else if ($action == 'getbyorder') {
    $madh = isset($data['madh']) ? (int)$data['madh'] : 0;
    $shipment = $model->getByOrder($madh);

    if ($shipment) {
        echo json_encode([
            "status"  => true,
            "message" => "Lấy thông tin vận chuyển thành công",
            "data"    => $shipment
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status"  => false,
            "message" => "Không tìm thấy vận chuyển cho đơn hàng này"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// THÊM VẬN CHUYỂN
else if ($action == 'add') {
    $madh     = isset($data['madh']) ? (int)$data['madh'] : 0;
    $makh     = isset($data['makh']) ? (int)$data['makh'] : 0;
    $ngaygiao = isset($data['ngaygiao']) ? trim($data['ngaygiao']) : '';

    if (!$madh || !$makh) {
        echo json_encode([
            "status"  => false,
            "message" => "Mã đơn hàng và mã khách hàng không được để trống"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $new_id = $model->add($madh, $makh, $ngaygiao);

    if ($new_id) {
        echo json_encode([
            "status"  => true,
            "message" => "Thêm vận chuyển thành công",
            "mavc"    => $new_id
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status"  => false,
            "message" => "Lỗi khi thêm vận chuyển"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// CẬP NHẬT VẬN CHUYỂN
else if ($action == 'update') {
    $mavc     = isset($data['mavc']) ? (int)$data['mavc'] : 0;
    $madh     = isset($data['madh']) ? (int)$data['madh'] : 0;
    $makh     = isset($data['makh']) ? (int)$data['makh'] : 0;
    $ngaygiao = isset($data['ngaygiao']) ? trim($data['ngaygiao']) : '';

    if (!$mavc || !$madh || !$makh) {
        echo json_encode([
            "status"  => false,
            "message" => "Thiếu thông tin cần thiết"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $result = $model->updateShipping($mavc, $madh, $makh, $ngaygiao);

    if ($result !== false) {
        echo json_encode([
            "status"  => true,
            "message" => "Cập nhật vận chuyển thành công"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status"  => false,
            "message" => "Lỗi khi cập nhật vận chuyển"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// XÓA VẬN CHUYỂN
else if ($action == 'delete') {
    $mavc = isset($data['mavc']) ? (int)$data['mavc'] : 0;

    $result = $model->deleteShipping($mavc);

    if ($result !== false) {
        echo json_encode([
            "status"  => true,
            "message" => "Xóa vận chuyển thành công"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status"  => false,
            "message" => "Lỗi khi xóa vận chuyển"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// ACTION KHÔNG HỢP LỆ
else {
    echo json_encode([
        "status"  => false,
        "message" => "Action không hợp lệ. Sử dụng: getall, getone, getbyorder, add, update, delete"
    ], JSON_UNESCAPED_UNICODE);
}
?>
