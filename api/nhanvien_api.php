<?php
/**
 * Nhân viên API - Sử dụng Model
 */
header("Content-Type: application/json; charset=utf-8");

// Load Model
require_once dirname(__DIR__) . '/model/NhanVien.php';

// Đọc dữ liệu từ input
$data = json_decode(file_get_contents("php://input"), true);
$action = isset($data['action']) ? $data['action'] : '';

// Khởi tạo Model
$model = new NhanVien();

// XEM TẤT CẢ NHÂN VIÊN
if ($action == 'getall') {
    $employees = $model->getAll();
    
    echo json_encode([
        "status" => true,
        "message" => "Lấy danh sách nhân viên thành công",
        "data" => $employees ?: [],
        "total" => $employees ? count($employees) : 0
    ], JSON_UNESCAPED_UNICODE);
}

// XEM 1 NHÂN VIÊN
else if ($action == 'getone') {
    $manv = isset($data['manv']) ? (int)$data['manv'] : 0;
    $employee = $model->findById($manv);
    
    if ($employee) {
        echo json_encode([
            "status" => true,
            "message" => "Lấy thông tin nhân viên thành công",
            "data" => $employee
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Không tìm thấy nhân viên"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// THÊM NHÂN VIÊN
else if ($action == 'add') {
    $tennv = isset($data['tennv']) ? trim($data['tennv']) : '';
    $diachi = isset($data['diachi']) ? trim($data['diachi']) : '';
    $sdt = isset($data['sdt']) ? trim($data['sdt']) : '';
    $ns = isset($data['ns']) ? trim($data['ns']) : '';
    
    if (empty($tennv)) {
        echo json_encode([
            "status" => false,
            "message" => "Tên nhân viên không được để trống"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $new_id = $model->add([
        'manv' => null,
        'tennv' => $tennv,
        'sdt' => $sdt,
        'ns' => $ns ?: null
    ]);
    
    if ($new_id) {
        echo json_encode([
            "status" => true,
            "message" => "Thêm nhân viên thành công",
            "manv" => $new_id
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Lỗi khi thêm nhân viên"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// CẬP NHẬT NHÂN VIÊN
else if ($action == 'update') {
    $manv = isset($data['manv']) ? (int)$data['manv'] : 0;
    $tennv = isset($data['tennv']) ? trim($data['tennv']) : '';
    $diachi = isset($data['diachi']) ? trim($data['diachi']) : '';
    $sdt = isset($data['sdt']) ? trim($data['sdt']) : '';
    $ns = isset($data['ns']) ? trim($data['ns']) : '';
    
    if (empty($tennv)) {
        echo json_encode([
            "status" => false,
            "message" => "Tên nhân viên không được để trống"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $result = $model->updateStaff($manv, [
        'tennv' => $tennv,
        'sdt' => $sdt,
        'ns' => $ns ?: null
    ]);
    
    if ($result !== false) {
        echo json_encode([
            "status" => true,
            "message" => "Cập nhật nhân viên thành công"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Lỗi khi cập nhật nhân viên"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// XÓA NHÂN VIÊN
else if ($action == 'delete') {
    $manv = isset($data['manv']) ? (int)$data['manv'] : 0;
    
    $result = $model->deleteStaff($manv);
    
    if ($result !== false) {
        echo json_encode([
            "status" => true,
            "message" => "Xóa nhân viên thành công"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Lỗi khi xóa nhân viên"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// TÌM KIẾM NHÂN VIÊN
else if ($action == 'search') {
    $keyword = isset($data['keyword']) ? trim($data['keyword']) : '';
    $employees = $model->search($keyword);
    
    echo json_encode([
        "status" => true,
        "message" => "Tìm kiếm nhân viên thành công",
        "data" => $employees ?: [],
        "total" => $employees ? count($employees) : 0
    ], JSON_UNESCAPED_UNICODE);
}

else {
    echo json_encode([
        "status" => false,
        "message" => "Action không hợp lệ. Sử dụng: getall, getone, add, update, delete, search"
    ], JSON_UNESCAPED_UNICODE);
}
?>
