<?php
    header("Content-Type: application/json; charset: utf-8");
    include "db.php";

    $post_data = json_decode(file_get_contents("php://input"), true);

    $action = isset($post_data["action"]) ? $post_data["action"] : '';

    switch ($action){
        case "getall":
            $sql = "SELECT gi.maitem, gi.magio, gi.masp, gi.sl, 
                    sp.tensp, sp.gia, sp.hinhanh, sp.hang, sp.gia*gi.sl AS thanhtien
                    FROM giohang_item gi
                    JOIN sanpham sp ON gi.masp = sp.masp";
            $result = $conn->query($sql);

            if ($result)
                echo json_encode([
                    "status" => true,
                    "data" => $result->fetch_all(MYSQLI_ASSOC)
            ]);
            else {
                echo json_encode([
                    "status" => false,
                    "message" => "Không tìm thấy bản ghi nào trong table giỏ hàng."
                ]);
            }
        break;


        case "get":
            if (isset($post_data["makh"]))
                $makh = $post_data["makh"];
            else {
                echo json_encode(["status" => false, "message" => "Không tìm thấy giá trị makh trong dữ liệu nhận được"]);
                exit();
            }

            $sql = "SELECT ghi.maitem, ghi.magio, ghi.masp, ghi.sl, 
                    sp.tensp, sp.gia, sp.hinhanh, sp.hang, sp.gia*ghi.sl AS thanhtien
                    FROM giohang gh
                    JOIN giohang_item ghi ON gh.magio = ghi.magio
                    JOIN sanpham sp ON ghi.masp = sp.masp
                    WHERE gh.makh = $makh";
            $result = $conn->query($sql);

            if ($result) {
                echo json_encode([
                    "status" => true,
                    "data" => $result->fetch_all(MYSQLI_ASSOC)
                ]);
            } else {
                echo json_encode(["status" => false, "message" => "Query thất bại"]);
            }
        break;


        default:
            echo json_encode([
                "status" => false,
                "message" => "Hành động không tồn tại"
            ]);
            break;
    }
?>