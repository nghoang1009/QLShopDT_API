<?php
    header("Content-Type: application/json");
    include "./db.php";

    $sql = "SELECT sp.masp, sp.tensp, sp.gia, sp.sl, sp.hang, sp.baohanh, sp.ghichu, sp.hinhanh, sp.madm, dm.tendm
            FROM sanpham sp
            JOIN danhmuc dm ON sp.madm = dm.madm";

    $result = $conn->query($sql);

    if($result)
        {
            $data = [];
            while($row = $result->fetch_assoc())
                {
                    $data[] = $row;
                }
            echo json_encode([
                "status" => true,
                "message" => "Lay danh sach san pham thanh cong",
                "data" => $data
            ]);
        }
    else
        {
            echo json_encode([
                "status" => false,
                "message" => "Lay danh sach san pham that bai"
            ]);
        }
?>