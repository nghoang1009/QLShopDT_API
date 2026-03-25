<?php
    header("Content-Type: application/json");
    include "./db.php";

    $data = json_decode(file_get_contents("php://input"), true);

    $madm    = $data['madm'];
    $tensp   = $data['tensp'];
    $gia     = $data['gia'];
    $sl      = $data['sl'];
    $hang    = $data['hang'];
    $baohanh = $data['baohanh'];
    $hinhanh = $data['hinhanh'];
    $ghichu  = $data['ghichu'];

    $sql = "INSERT INTO `sanpham` (`madm`, `tensp`, `gia`, `sl`, `hang`, `baohanh`, `hinhanh`, `ghichu`) 
            VALUES ('$madm', '$tensp', '$gia', '$sl', '$hang', '$baohanh', '$hinhanh', '$ghichu')";

    $result = $conn -> query($sql);

    if($result)
        {
            echo json_encode([
                "status" => true,
                "message" => "Them san pham thanh cong",
            ]);
        }
    else
        {
            echo json_encode([
                "status" => false,
                "message" => "Them san pham that bai"
            ]); 
        }
?>