<?php
header("Content-Type: application/json");

// Kết nối database
include "db.php";

// Chỉ chấp nhận POST với JSON body
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        "status" => false,
        "message" => "Phương thức không hợp lệ"
    ]);
    $conn->close();
    exit;
}

$data = json_decode(file_get_contents("php://input"), true);
$username = trim($data['username'] ?? '');
$password = trim($data['password'] ?? '');
$confirm = trim($data['confirm_password'] ?? '');
$name = trim($data['name'] ?? '');
$address = trim($data['address'] ?? '');
$phone = trim($data['phone'] ?? '');

if ($username === '' || $password === '' || $confirm === '' || $name === '') {
    echo json_encode([
        "status" => false,
        "message" => "Tên đăng nhập, mật khẩu và họ tên là bắt buộc"
    ]);
    $conn->close();
    exit;
}

if ($password !== $confirm) {
    echo json_encode([
        "status" => false,
        "message" => "Mật khẩu xác nhận không khớp"
    ]);
    $conn->close();
    exit;
}

// Kiểm tra username đã tồn tại
$stmt = $conn->prepare("SELECT tentk FROM taikhoan WHERE tentk = ?");
$stmt->bind_param('s', $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    echo json_encode([
        "status" => false,
        "message" => "Tên đăng nhập đã tồn tại"
    ]);
    $stmt->close();
    $conn->close();
    exit;
}
$stmt->close();

// Tạo tài khoản
$stmt = $conn->prepare("INSERT INTO taikhoan (tentk, mk, role) VALUES (?, ?, 0)");
$stmt->bind_param('ss', $username, $password);

if ($stmt->execute()) {
    $matk = $conn->insert_id;
    $stmt->close();

    // Tạo thông tin khách hàng
    $stmt2 = $conn->prepare("INSERT INTO khachhang (makh, tenkh, diachi, sdt) VALUES (?, ?, ?, ?)");
    $stmt2->bind_param('isss', $matk, $name, $address, $phone);

    if ($stmt2->execute()) {
        $stmt2->close();

        // Tạo giỏ hàng
        $stmt3 = $conn->prepare("INSERT INTO giohang (makh) VALUES (?)");
        $stmt3->bind_param('i', $matk);
        $stmt3->execute();
        $stmt3->close();

        echo json_encode([
            "status" => true,
            "message" => "Đăng ký thành công"
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "message" => "Lỗi khi tạo thông tin khách hàng"
        ]);
    }
} else {
    echo json_encode([
        "status" => false,
        "message" => "Lỗi khi tạo tài khoản"
    ]);
}

$conn->close();
?>