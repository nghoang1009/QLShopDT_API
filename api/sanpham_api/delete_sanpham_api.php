<?php
    header("Content-Type: application/json");
    include "./db.php";

    $data = json_decode(file_get_contents("php://input"), true);

    $masp = $data['masp'];

    // Lấy tên file ảnh trước khi xóa
    $getImageSQL = "SELECT hinhanh FROM sanpham WHERE masp = '$masp'";
    $result = $conn->query($getImageSQL);
    $row = $result->fetch_assoc();
    $hinhanh = $row['hinhanh'];

    $sql = "DELETE FROM sanpham WHERE masp = '$masp'";
    $result = $conn->query($sql);

    if($result)
        {
            // Xóa file ảnh
            $imagePath = "../img/" . $hinhanh;
            if(file_exists($imagePath))
                {
                    unlink($imagePath);
                }
            echo json_encode([
                "status" => true,
                "message" => "Xoa san pham thanh cong"
            ]);
        }
    else
        {
            echo json_encode([
                "status" => false,
                "message" => "Xoa san pham that bai"
            ]);
        }
?>