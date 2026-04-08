<?php
header("Content-Type: application/json");

// Kết nối database
include "db.php";

// Đọc dữ liệu từ frontend
$data   = json_decode(file_get_contents("php://input"), true);
$action = isset($data['action']) ? $data['action'] : '';

//  ĐĂNG NHẬP 
if ($action == 'login') {
    $tentk   = $conn->real_escape_string($data['email'] ?? $data['tentk'] ?? '');
    $matkhau = $data['matkhau'] ?? '';

    if (empty($tentk) || empty($matkhau)) {
        echo json_encode([
            "status"  => false,
            "message" => "Vui lòng nhập đầy đủ email/tên tài khoản và mật khẩu"
        ]);
        $conn->close();
        exit;
    }

    $sql = "SELECT tk.*, kh.tenkh, kh.diachi, kh.sdt 
            FROM taikhoan tk 
            LEFT JOIN khachhang kh ON tk.matk = kh.makh 
            WHERE tk.tentk = '$tentk' 
              AND tk.matkhau = '$matkhau'";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        unset($user['matkhau']);   // Không trả mật khẩu về client

        echo json_encode([
            "status"  => true,
            "message" => "Đăng nhập thành công",
            "data"    => $user
        ]);
    } else {
        echo json_encode([
            "status"  => false,
            "message" => "Tên tài khoản hoặc mật khẩu không đúng"
        ]);
    }
} 
else {
    echo json_encode([
        "status"  => false,
        "message" => "Action không hợp lệ. Sử dụng: login"
    ]);
}

$conn->close();
?>