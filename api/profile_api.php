<?php
header("Content-Type: application/json");

// Kết nối database
include "db.php";

// Đọc dữ liệu từ input
$data   = json_decode(file_get_contents("php://input"), true);
$action = isset($data['action']) ? $data['action'] : '';

// ===================== LẤY THÔNG TIN CÁ NHÂN =====================
if ($action == 'get') {
    $user_id = $conn->real_escape_string($data['user_id']);
    $role    = $conn->real_escape_string($data['role']);

    if ($role == 0) { // Khách hàng
        $sql = "SELECT tenkh, diachi, sdt FROM khachhang WHERE makh = '$user_id'";
    } else { // Nhân viên hoặc Admin
        $sql = "SELECT tennv as tenkh, diachi, sdt, ns FROM nhanvien WHERE manv = '$user_id'";
    }

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        echo json_encode([
            "status"  => true,
            "message" => "Lấy thông tin cá nhân thành công",
            "data"    => $result->fetch_assoc()
        ]);
    } else {
        echo json_encode(["status" => false, "message" => "Không tìm thấy thông tin cá nhân"]);
    }
}

// ===================== CẬP NHẬT THÔNG TIN CÁ NHÂN =====================
else if ($action == 'update') {
    $user_id = $conn->real_escape_string($data['user_id']);
    $role    = $conn->real_escape_string($data['role']);
    $name    = $conn->real_escape_string($data['name'] ?? '');
    $phone   = $conn->real_escape_string($data['phone'] ?? '');
    $address = $conn->real_escape_string($data['address'] ?? '');
    $birthday = isset($data['birthday']) ? $conn->real_escape_string($data['birthday']) : null;

    if (empty($name)) {
        echo json_encode(["status" => false, "message" => "Tên không được để trống"]);
        exit;
    }

    if ($role == 0) { // Khách hàng
        $sql = "UPDATE khachhang 
                SET tenkh = '$name', diachi = '$address', sdt = '$phone' 
                WHERE makh = '$user_id'";
    } else { // Nhân viên
        if ($birthday) {
            $sql = "UPDATE nhanvien 
                    SET tennv = '$name', diachi = '$address', sdt = '$phone', ns = '$birthday' 
                    WHERE manv = '$user_id'";
        } else {
            $sql = "UPDATE nhanvien 
                    SET tennv = '$name', diachi = '$address', sdt = '$phone' 
                    WHERE manv = '$user_id'";
        }
    }

    if ($conn->query($sql)) {
        echo json_encode(["status" => true, "message" => "Cập nhật thông tin cá nhân thành công"]);
    } else {
        echo json_encode(["status" => false, "message" => "Lỗi cập nhật: " . $conn->error]);
    }
}

// ===================== TẠO PROFILE MỚI =====================
else if ($action == 'create') {
    $user_id = $conn->real_escape_string($data['user_id']);
    $role    = $conn->real_escape_string($data['role']);
    $name    = $conn->real_escape_string($data['name'] ?? 'User');

    if ($role == 0) { // Khách hàng
        $sql = "INSERT INTO khachhang (makh, tenkh, diachi, sdt) 
                VALUES ('$user_id', '$name', '', '')";
    } else { // Nhân viên
        $current_date = date('Y-m-d');
        $sql = "INSERT INTO nhanvien (manv, tennv, diachi, sdt, ns) 
                VALUES ('$user_id', '$name', '', '', '$current_date')";
    }

    if ($conn->query($sql)) {
        echo json_encode(["status" => true, "message" => "Tạo profile thành công"]);
    } else {
        echo json_encode(["status" => false, "message" => "Lỗi tạo profile: " . $conn->error]);
    }
}

else {
    echo json_encode(["status" => false, "message" => "Action không hợp lệ"]);
}
?>
