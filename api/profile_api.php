<?php
/**
 * Profile API - Quản lý thông tin cá nhân sử dụng Model
 */
header("Content-Type: application/json; charset=utf-8");

// Load Models
require_once dirname(__DIR__) . '/model/KhachHang.php';
require_once dirname(__DIR__) . '/model/NhanVien.php';

// Đọc dữ liệu từ input
$data   = json_decode(file_get_contents("php://input"), true);
$action = isset($data['action']) ? $data['action'] : '';

// Khởi tạo Models
$khModel = new KhachHang();
$nvModel = new NhanVien();

// ===================== LẤY THÔNG TIN CÁ NHÂN =====================
if ($action == 'get') {
    $user_id = isset($data['user_id']) ? (int)$data['user_id'] : 0;
    $role    = isset($data['role']) ? (int)$data['role'] : 0;

    if ($role == 0) { // Khách hàng
        $profile = $khModel->findById($user_id);
        if ($profile) {
            echo json_encode([
                "status"  => true,
                "message" => "Lấy thông tin cá nhân thành công",
                "data"    => [
                    "tenkh" => $profile['tenkh'],
                    "diachi" => $profile['diachi'],
                    "sdt" => $profile['sdt']
                ]
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "message" => "Không tìm thấy thông tin cá nhân"], JSON_UNESCAPED_UNICODE);
        }
    } else { // Nhân viên hoặc Admin
        $profile = $nvModel->findById($user_id);
        if ($profile) {
            echo json_encode([
                "status"  => true,
                "message" => "Lấy thông tin cá nhân thành công",
                "data"    => [
                    "tenkh" => $profile['tennv'],
                    "diachi" => $profile['diachi'],
                    "sdt" => $profile['sdt'],
                    "ns" => $profile['ns'] ?? null
                ]
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(["status" => false, "message" => "Không tìm thấy thông tin cá nhân"], JSON_UNESCAPED_UNICODE);
        }
    }
}

// ===================== CẬP NHẬT THÔNG TIN CÁ NHÂN =====================
else if ($action == 'update') {
    $user_id = isset($data['user_id']) ? (int)$data['user_id'] : 0;
    $role    = isset($data['role']) ? (int)$data['role'] : 0;
    $name    = trim($data['name'] ?? '');
    $phone   = trim($data['phone'] ?? '');
    $address = trim($data['address'] ?? '');
    $birthday = isset($data['birthday']) ? trim($data['birthday']) : null;

    if (empty($name)) {
        echo json_encode(["status" => false, "message" => "Tên không được để trống"], JSON_UNESCAPED_UNICODE);
        exit;
    }

    if ($role == 0) { // Khách hàng
        $result = $khModel->updateCustomer($user_id, [
            'tenkh' => $name,
            'diachi' => $address,
            'sdt' => $phone
        ]);
    } else { // Nhân viên
        $updateData = [
            'tennv' => $name,
            'sdt' => $phone
        ];
        if ($birthday) {
            $updateData['ns'] = $birthday;
        }
        $result = $nvModel->updateStaff($user_id, $updateData);
    }

    if ($result !== false) {
        echo json_encode(["status" => true, "message" => "Cập nhật thông tin cá nhân thành công"], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["status" => false, "message" => "Lỗi cập nhật thông tin"], JSON_UNESCAPED_UNICODE);
    }
}

// ===================== TẠO PROFILE MỚI =====================
else if ($action == 'create') {
    $user_id = isset($data['user_id']) ? (int)$data['user_id'] : 0;
    $role    = isset($data['role']) ? (int)$data['role'] : 0;
    $name    = trim($data['name'] ?? 'User');

    if ($role == 0) { // Khách hàng
        $result = $khModel->add([
            'makh' => $user_id,
            'tenkh' => $name,
            'diachi' => '',
            'sdt' => ''
        ]);
    } else { // Nhân viên
        $current_date = date('Y-m-d');
        $result = $nvModel->add([
            'manv' => $user_id,
            'tennv' => $name,
            'diachi' => '',
            'sdt' => '',
            'ns' => $current_date
        ]);
    }

    if ($result !== false) {
        echo json_encode(["status" => true, "message" => "Tạo profile thành công"], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(["status" => false, "message" => "Lỗi tạo profile"], JSON_UNESCAPED_UNICODE);
    }
}

else {
    echo json_encode(["status" => false, "message" => "Action không hợp lệ. Sử dụng: get, update, create"], JSON_UNESCAPED_UNICODE);
}
?>
