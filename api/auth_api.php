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

if ($username === '' || $password === '') {
    echo json_encode([
        "status" => false,
        "message" => "Tên đăng nhập và mật khẩu không được để trống"
    ]);
    $conn->close();
    exit;
}

$stmt = $conn->prepare("SELECT matk, tentk, role FROM taikhoan WHERE tentk = ? AND mk = ?");
$stmt->bind_param('ss', $username, $password);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $user = $result->fetch_assoc();
    echo json_encode([
        "status" => true,
        "message" => "Đăng nhập thành công",
        "user" => [
            "matk" => $user['matk'],
            "tentk" => $user['tentk'],
            "role" => $user['role']
        ]
    ]);
} else {
    echo json_encode([
        "status" => false,
        "message" => "Sai tên đăng nhập hoặc mật khẩu"
    ]);
}

$stmt->close();
$conn->close();
?>
