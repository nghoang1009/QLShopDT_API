<?php
/**
 * Khách hàng API - Sử dụng Model
 */
header("Content-Type: application/json; charset=utf-8");

// Load Models
require_once dirname(__DIR__) . '/model/KhachHang.php';
require_once dirname(__DIR__) . '/model/TaiKhoan.php';

// Đọc dữ liệu từ input
$data   = json_decode(file_get_contents("php://input"), true);
$action = isset($data['action']) ? $data['action'] : '';

// Khởi tạo Models
$khModel = new KhachHang();
$tkModel = new TaiKhoan();

// ===================== XEM TẤT CẢ KHÁCH HÀNG =====================
if ($action == 'getall') {
    $customers = $khModel->getAllWithAccount();

    echo json_encode([
        "status"  => true,
        "message" => "Lấy danh sách khách hàng thành công",
        "data"    => $customers ?: [],
        "total"   => $customers ? count($customers) : 0
    ], JSON_UNESCAPED_UNICODE);
}

// ===================== XEM 1 KHÁCH HÀNG =====================
else if ($action == 'getone') {
    $makh = isset($data['makh']) ? (int)$data['makh'] : 0;
    $customer = $khModel->findWithAccount($makh);

    if ($customer) {
        echo json_encode([
            "status"  => true,
            "message" => "Lấy thông tin khách hàng thành công",
            "data"    => $customer
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => false, 
            "message" => "Không tìm thấy khách hàng"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// ===================== THÊM KHÁCH HÀNG =====================
else if ($action == 'add') {
    $tenkh  = isset($data['tenkh']) ? trim($data['tenkh']) : '';
    $diachi = isset($data['diachi']) ? trim($data['diachi']) : '';
    $sdt    = isset($data['sdt']) ? trim($data['sdt']) : '';

    if (empty($tenkh)) {
        echo json_encode([
            "status" => false,
            "message" => "Tên khách hàng không được để trống"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Tạo tài khoản trước, dùng tenkh làm tentk, mật khẩu mặc định 123456, role 0
    $tkResult = $tkModel->register($tenkh, '123456', 0);
    
    if ($tkResult['success']) {
        $matk = $tkResult['id'];
        
        $khResult = $khModel->add([
            'makh' => $matk,
            'tenkh' => $tenkh,
            'diachi' => $diachi,
            'sdt' => $sdt
        ]);

        if ($khResult !== false) {
            echo json_encode([
                "status"  => true,
                "message" => "Thêm khách hàng thành công",
                "makh"    => $matk
            ], JSON_UNESCAPED_UNICODE);
        } else {
            // Rollback - xóa tài khoản nếu không tạo được khách hàng
            $tkModel->deleteAccount($matk);
            echo json_encode([
                "status" => false, 
                "message" => "Lỗi thêm khách hàng"
            ], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo json_encode([
            "status" => false, 
            "message" => $tkResult['message']
        ], JSON_UNESCAPED_UNICODE);
    }
}

// ===================== CẬP NHẬT KHÁCH HÀNG =====================
else if ($action == 'update') {
    $makh   = isset($data['makh']) ? (int)$data['makh'] : 0;
    $tenkh  = isset($data['tenkh']) ? trim($data['tenkh']) : '';
    $diachi = isset($data['diachi']) ? trim($data['diachi']) : '';
    $sdt    = isset($data['sdt']) ? trim($data['sdt']) : '';

    if (empty($tenkh)) {
        echo json_encode([
            "status" => false,
            "message" => "Tên khách hàng không được để trống"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $result = $khModel->updateCustomer($makh, [
        'tenkh' => $tenkh,
        'diachi' => $diachi,
        'sdt' => $sdt
    ]);

    if ($result !== false) {
        echo json_encode([
            "status" => true, 
            "message" => "Cập nhật khách hàng thành công"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => false, 
            "message" => "Lỗi cập nhật khách hàng"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// ===================== XÓA KHÁCH HÀNG =====================
else if ($action == 'delete') {
    $makh = isset($data['makh']) ? (int)$data['makh'] : 0;

    // Xóa tài khoản sẽ tự động xóa khách hàng (CASCADE)
    $result = $tkModel->deleteAccount($makh);

    if ($result !== false) {
        echo json_encode([
            "status" => true, 
            "message" => "Xóa khách hàng thành công"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => false, 
            "message" => "Lỗi xóa khách hàng"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// ===================== LẤY MÃ KH THEO USERNAME =====================
else if ($action == 'getbyusername') {
    $username = isset($data['username']) ? trim($data['username']) : '';
    
    $makh = $khModel->findByUsername($username);
    
    if ($makh) {
        echo json_encode([
            "status" => true,
            "message" => "Tìm thấy khách hàng",
            "makh" => $makh
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => false, 
            "message" => "Không tìm thấy khách hàng"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// ===================== TÌM KIẾM KHÁCH HÀNG =====================
else if ($action == 'search') {
    $keyword = isset($data['keyword']) ? trim($data['keyword']) : '';
    $customers = $khModel->search($keyword);

    echo json_encode([
        "status"  => true,
        "message" => "Tìm kiếm khách hàng thành công",
        "data"    => $customers ?: [],
        "total"   => $customers ? count($customers) : 0
    ], JSON_UNESCAPED_UNICODE);
}

else {
    echo json_encode([
        "status"  => false,
        "message" => "Action không hợp lệ. Sử dụng: getall, getone, add, update, delete, getbyusername, search"
    ], JSON_UNESCAPED_UNICODE);
}
?>