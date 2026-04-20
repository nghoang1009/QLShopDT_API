<?php
/**
 * Thanh toán API - Sử dụng Model
 */
header("Content-Type: application/json; charset=utf-8");

// Load Model
require_once dirname(__DIR__) . '/model/ThanhToan.php';
require_once dirname(__DIR__) . '/model/DonHang.php';

// Đọc dữ liệu từ input
$data = json_decode(file_get_contents("php://input"), true);
$action = isset($data['action']) ? $data['action'] : '';

// Khởi tạo Model
$model = new ThanhToan();
$donHangModel = new DonHang();

// XEM TẤT CẢ THANH TOÁN
if ($action == 'getall') {
    $payments = $model->getAllWithDetails();
    
    echo json_encode([
        "status" => true,
        "message" => "Lấy danh sách thanh toán thành công",
        "data" => $payments ?: [],
        "total" => $payments ? count($payments) : 0
    ], JSON_UNESCAPED_UNICODE);
}

// XEM THANH TOÁN THEO KHÁCH HÀNG
else if ($action == 'getbycustomer') {
    $makh = isset($data['makh']) ? (int)$data['makh'] : 0;
    $payments = $model->getByCustomer($makh);
    
    echo json_encode([
        "status" => true,
        "message" => "Lấy danh sách thanh toán thành công",
        "data" => $payments ?: [],
        "total" => $payments ? count($payments) : 0
    ], JSON_UNESCAPED_UNICODE);
}

// XEM 1 THANH TOÁN
else if ($action == 'getone') {
    $matt = isset($data['matt']) ? (int)$data['matt'] : 0;
    $payment = $model->getOneWithDetails($matt);
    
    if ($payment) {
        echo json_encode([
            "status" => true,
            "message" => "Lấy thông tin thanh toán thành công",
            "data" => $payment
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Không tìm thấy thanh toán"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// XEM THANH TOÁN THEO ĐƠN HÀNG
else if ($action == 'getbyorder') {
    $madh = isset($data['madh']) ? (int)$data['madh'] : 0;
    $payment = $model->getByOrder($madh);
    
    if ($payment) {
        echo json_encode([
            "status" => true,
            "message" => "Lấy thông tin thanh toán thành công",
            "data" => $payment
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Đơn hàng chưa có thanh toán"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// LẤY CÁC ĐƠN HÀNG CHƯA THANH TOÁN
else if ($action == 'getunpaid') {
    $orders = $model->getUnpaidOrders();
    
    echo json_encode([
        "status" => true,
        "message" => "Lấy danh sách đơn hàng chưa thanh toán thành công",
        "data" => $orders ?: [],
        "total" => $orders ? count($orders) : 0
    ], JSON_UNESCAPED_UNICODE);
}

// THÊM THANH TOÁN
else if ($action == 'add') {
    $madh = isset($data['madh']) ? (int)$data['madh'] : 0;
    $phuongthuc = isset($data['phuongthuc']) ? trim($data['phuongthuc']) : '';
    $sotien = isset($data['sotien']) ? (float)$data['sotien'] : 0;
    $trangthai = isset($data['trangthai']) ? trim($data['trangthai']) : 'Chờ xác nhận';
    $ghichu = isset($data['ghichu']) ? trim($data['ghichu']) : '';
    
    // Validate
    if ($madh <= 0 || empty($phuongthuc) || $sotien <= 0) {
        echo json_encode([
            "status" => false,
            "message" => "Vui lòng nhập đầy đủ thông tin"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Kiểm tra đơn hàng tồn tại
    $order = $donHangModel->getOneWithDetails($madh);
    if (!$order) {
        echo json_encode([
            "status" => false,
            "message" => "Không tìm thấy đơn hàng"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // Kiểm tra đã thanh toán chưa
    $existingPayment = $model->getByOrder($madh);
    if ($existingPayment && $existingPayment['trangthai'] == 'Đã thanh toán') {
        echo json_encode([
            "status" => false,
            "message" => "Đơn hàng này đã được thanh toán"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $paymentData = [
        'madh' => $madh,
        'phuongthuc' => $phuongthuc,
        'sotien' => $sotien,
        'trangthai' => $trangthai,
        'ghichu' => $ghichu
    ];
    
    $result = $model->add($paymentData);
    
    if ($result) {
        // Cập nhật trạng thái đơn hàng nếu đã thanh toán
        if ($trangthai == 'Đã thanh toán') {
            $donHangModel->updateStatus($madh, 'Đã xác nhận');
        }
        
        echo json_encode([
            "status" => true,
            "message" => "Thêm thanh toán thành công",
            "matt" => $result
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Thêm thanh toán thất bại"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// CẬP NHẬT THANH TOÁN
else if ($action == 'update') {
    $matt = isset($data['matt']) ? (int)$data['matt'] : 0;
    $phuongthuc = isset($data['phuongthuc']) ? trim($data['phuongthuc']) : '';
    $ngaythanhtoan = isset($data['ngaythanhtoan']) ? $data['ngaythanhtoan'] : date('Y-m-d H:i:s');
    $sotien = isset($data['sotien']) ? (float)$data['sotien'] : 0;
    $trangthai = isset($data['trangthai']) ? trim($data['trangthai']) : '';
    $ghichu = isset($data['ghichu']) ? trim($data['ghichu']) : '';
    
    // Validate
    if ($matt <= 0) {
        echo json_encode([
            "status" => false,
            "message" => "Mã thanh toán không hợp lệ"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $payment = $model->getOneWithDetails($matt);
    if (!$payment) {
        echo json_encode([
            "status" => false,
            "message" => "Không tìm thấy thanh toán"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $updateData = [
        'phuongthuc' => $phuongthuc ?: $payment['phuongthuc'],
        'ngaythanhtoan' => $ngaythanhtoan,
        'sotien' => $sotien > 0 ? $sotien : $payment['sotien'],
        'trangthai' => $trangthai ?: $payment['trangthai'],
        'ghichu' => $ghichu
    ];
    
    $result = $model->updatePayment($matt, $updateData);
    
    if ($result) {
        // Cập nhật trạng thái đơn hàng nếu đã thanh toán
        if ($trangthai == 'Đã thanh toán') {
            $donHangModel->updateStatus($payment['madh'], 'Đã xác nhận');
        }
        
        echo json_encode([
            "status" => true,
            "message" => "Cập nhật thanh toán thành công"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Cập nhật thanh toán thất bại"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// CẬP NHẬT TRẠNG THÁI THANH TOÁN
else if ($action == 'updatestatus') {
    $matt = isset($data['matt']) ? (int)$data['matt'] : 0;
    $trangthai = isset($data['trangthai']) ? trim($data['trangthai']) : '';
    
    if ($matt <= 0 || empty($trangthai)) {
        echo json_encode([
            "status" => false,
            "message" => "Thiếu thông tin"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $result = $model->updateStatus($matt, $trangthai);
    
    if ($result) {
        echo json_encode([
            "status" => true,
            "message" => "Cập nhật trạng thái thành công"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Cập nhật trạng thái thất bại"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// XÓA THANH TOÁN
else if ($action == 'delete') {
    $matt = isset($data['matt']) ? (int)$data['matt'] : 0;
    
    if ($matt <= 0) {
        echo json_encode([
            "status" => false,
            "message" => "Mã thanh toán không hợp lệ"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $result = $model->deletePayment($matt);
    
    if ($result) {
        echo json_encode([
            "status" => true,
            "message" => "Xóa thanh toán thành công"
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Xóa thanh toán thất bại"
        ], JSON_UNESCAPED_UNICODE);
    }
}

// LẤY DANH SÁCH PHƯƠNG THỨC THANH TOÁN
else if ($action == 'getmethods') {
    $methods = $model->getPaymentMethods();
    
    echo json_encode([
        "status" => true,
        "message" => "Lấy danh sách phương thức thanh toán thành công",
        "data" => $methods
    ], JSON_UNESCAPED_UNICODE);
}

// LẤY DANH SÁCH TRẠNG THÁI THANH TOÁN
else if ($action == 'getstatuses') {
    $statuses = $model->getPaymentStatuses();
    
    echo json_encode([
        "status" => true,
        "message" => "Lấy danh sách trạng thái thành công",
        "data" => $statuses
    ], JSON_UNESCAPED_UNICODE);
}

// ACTION KHÔNG HỢP LỆ
else {
    echo json_encode([
        "status" => false,
        "message" => "Action không hợp lệ. Các action hỗ trợ: getall, getbycustomer, getone, getbyorder, getunpaid, add, update, updatestatus, delete, getmethods, getstatuses"
    ], JSON_UNESCAPED_UNICODE);
}
