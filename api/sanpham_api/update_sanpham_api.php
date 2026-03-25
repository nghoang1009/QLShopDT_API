<?php
    header("Content-Type: application/json");
    include "./db.php";

    $data = json_decode(file_get_contents("php://input"), true);

    $masp    = $data['masp'];
    $tensp   = $data['tensp'];
    $gia     = $data['gia'];
    $sl      = $data['sl'];
    $hang    = $data['hang'];
    $baohanh = $data['baohanh'];
    $ghichu  = $data['ghichu'];
    $hinhanh = $data['hinhanh'];
    $madm    = $data['madm'];

    $sql = "UPDATE sanpham SET
                tensp    = '$tensp',
                gia      = '$gia',
                sl       = '$sl',
                hang     = '$hang',
                baohanh  = '$baohanh',
                ghichu   = '$ghichu',
                hinhanh  = '$hinhanh',
                madm     = '$madm'
            WHERE masp = '$masp'";

    $result = $conn->query($sql);

    if($result)
        {
            echo json_encode([
                "status" => true,
                "message" => "Sua san pham thanh cong"
            ]);
        }
    else
        {
            echo json_encode([
                "status" => false,
                "message" => $conn->error
            ]);
        }
?>