<?php
header("Content-Type: application/json");

// Kết nối database
include "db.php";

// Đọc dữ liệu từ input
$data = json_decode(file_get_contents("php://input"), true);
$action = isset($data['action']) ? $data['action'] : '';

// XEM TẤT CẢ NHÂN VIÊN
if($action == 'getall') {
    $sql = "SELECT * FROM nhanvien ORDER BY manv ASC";
    $result = $conn->query($sql);
    
    if($result) {
        $employees = [];
        while($row = $result->fetch_assoc()) {
            $employees[] = $row;
        }
        
        echo json_encode([
            "status" => true,
            "message" => "Lấy danh sách nhân viên thành công",
            "data" => $employees,
            "total" => count($employees)
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Lỗi: " . $conn->error
        ]);
    }
}

// XEM 1 NHÂN VIÊN
else if($action == 'getone') {
    $manv = $data['manv'];
    
    $sql = "SELECT * FROM nhanvien WHERE manv = '$manv'";
    $result = $conn->query($sql);
    
    if($result && $result->num_rows > 0) {
        $employee = $result->fetch_assoc();
        
        echo json_encode([
            "status" => true,
            "message" => "Lấy thông tin nhân viên thành công",
            "data" => $employee
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Không tìm thấy nhân viên"
        ]);
    }
}

// THÊM NHÂN VIÊN
else if($action == 'add') {
    $tennv = $data['tennv'];
    $diachi = $data['diachi'] ?? '';
    $sdt = $data['sdt'] ?? '';
    $ns = $data['ns'] ?? '';
    
    $sql = "INSERT INTO nhanvien (tennv, diachi, sdt, ns) VALUES ('$tennv', '$diachi', '$sdt', '$ns')";
    
    if($conn->query($sql)) {
        $new_id = $conn->insert_id;
        
        echo json_encode([
            "status" => true,
            "message" => "Thêm nhân viên thành công",
            "manv" => $new_id
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Lỗi: " . $conn->error
        ]);
    }
}

// CẬP NHẬT NHÂN VIÊN
else if($action == 'update') {
    $manv = $data['manv'];
    $tennv = $data['tennv'];
    $diachi = $data['diachi'] ?? '';
    $sdt = $data['sdt'] ?? '';
    $ns = $data['ns'] ?? '';
    
    $sql = "UPDATE nhanvien SET tennv = '$tennv', diachi = '$diachi', sdt = '$sdt', ns = '$ns' WHERE manv = '$manv'";
    
    if($conn->query($sql)) {
        echo json_encode([
            "status" => true,
            "message" => "Cập nhật nhân viên thành công"
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Lỗi: " . $conn->error
        ]);
    }
}

// XÓA NHÂN VIÊN
else if($action == 'delete') {
    $manv = $data['manv'];
    
    $sql = "DELETE FROM nhanvien WHERE manv = '$manv'";
    
    if($conn->query($sql)) {
        echo json_encode([
            "status" => true,
            "message" => "Xóa nhân viên thành công"
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Lỗi: " . $conn->error
        ]);
    }
}

else {
    echo json_encode([
        "status" => false,
        "message" => "Action không hợp lệ. Sử dụng: getall, getone, add, update, delete"
    ]);
}

$conn->close();
?>
