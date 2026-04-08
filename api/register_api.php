<?php
header("Content-Type: application/json");

// Kết nối database
include "db.php";

// Đọc dữ liệu từ frontend
$data   = json_decode(file_get_contents("php://input"), true);
$action = isset($data['action']) ? $data['action'] : '';

//  ĐĂNG KÝ
if ($action == 'register') {
    $tenkh   = $conn->real_escape_string($data['hoten'] ?? $data['tenkh'] ?? '');
    $tentk   = $conn->real_escape_string($data['email'] ?? '');   // email sẽ thành tentk
    $matkhau = $data['matkhau'] ?? '';
    $sdt     = $conn->real_escape_string($data['sodienthoai'] ?? $data['sdt'] ?? '');
    $diachi  = $conn->real_escape_string($data['diachi'] ?? '');

    if (empty($tenkh) || empty($tentk) || empty($matkhau) || empty($sdt)) {
        echo json_encode([
            "status"  => false,
            "message" => "Vui lòng điền đầy đủ thông tin bắt buộc"
        ]);
        $conn->close();
        exit;
    }

    // Kiểm tra tentk (email) đã tồn tại chưa
    $check = "SELECT matk FROM taikhoan WHERE tentk = '$tentk'";
    if ($conn->query($check)->num_rows > 0) {
        echo json_encode([
            "status"  => false,
            "message" => "Email đã tồn tại"
        ]);
        $conn->close();
        exit;
    }

    // Tạo tài khoản 
    $sql_tk = "INSERT INTO taikhoan (tentk, matkhau, role) 
               VALUES ('$tentk', '$matkhau', '0')";

    if ($conn->query($sql_tk)) {
        $makh = $conn->insert_id;

        // Tạo thông tin khách hàng
        $sql_kh = "INSERT INTO khachhang (makh, tenkh, diachi, sdt) 
                   VALUES ('$makh', '$tenkh', '$diachi', '$sdt')";

        if ($conn->query($sql_kh)) {
            echo json_encode([
                "status"  => true,
                "message" => "Đăng ký thành công",
                "data"    => ["makh" => $makh]
            ]);
        } else {
            // Rollback nếu insert khachhang lỗi
            $conn->query("DELETE FROM taikhoan WHERE matk = $makh");
            echo json_encode([
                "status"  => false,
                "message" => "Lỗi thêm khách hàng: " . $conn->error
            ]);
        }
    } else {
        echo json_encode([
            "status"  => false,
            "message" => "Lỗi tạo tài khoản: " . $conn->error
        ]);
    }
} 
else {
    echo json_encode([
        "status"  => false,
        "message" => "Action không hợp lệ."
    ]);
}

$conn->close();
?>