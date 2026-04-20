<?php
header("Content-Type: application/json; charset=utf-8");

// Load cấu hình và database
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/model/GioHang.php';
require_once dirname(__DIR__) . '/model/SanPham.php';

$post_data = json_decode(file_get_contents("php://input"), true);
$action = isset($post_data["action"]) ? $post_data["action"] : '';

$gioHangModel = new GioHang();
$sanPhamModel = new SanPham();

switch ($action) {
    case "getall":
        $result = $gioHangModel->getAllItems();

        if ($result !== false) {
            echo json_encode([
                "status" => true,
                "data" => $result
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Không tìm thấy bản ghi nào trong giỏ hàng"
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "get":
        if (!isset($post_data["makh"])) {
            echo json_encode([
                "status" => false,
                "message" => "Thiếu mã khách hàng (makh)"
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $makh = (int)$post_data["makh"];
        $result = $gioHangModel->getByCustomer($makh);

        echo json_encode([
            "status" => true,
            "data" => $result ?: []
        ], JSON_UNESCAPED_UNICODE);
        break;

    case "add":
        // Kiểm tra dữ liệu bắt buộc
        if (!isset($post_data["makh"]) || !isset($post_data["masp"]) || !isset($post_data["sl"])) {
            echo json_encode([
                "status" => false,
                "message" => "Chưa điền đầy đủ thông tin: makh, masp, sl"
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $makh = (int)$post_data["makh"];
        $masp = (int)$post_data["masp"];
        $sl_them = (int)$post_data["sl"];

        // Kiểm tra dữ liệu đầu vào
        if ($makh <= 0) {
            echo json_encode([
                "status" => false,
                "message" => "Mã khách hàng phải là số nguyên lớn hơn 0"
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
        if ($masp <= 0) {
            echo json_encode([
                "status" => false,
                "message" => "Mã sản phẩm phải là số nguyên lớn hơn 0"
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }
        if ($sl_them <= 0) {
            echo json_encode([
                "status" => false,
                "message" => "Số lượng phải là số nguyên lớn hơn 0"
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }

        // Kiểm tra sản phẩm có tồn tại không
        if (!$sanPhamModel->exists($masp)) {
            echo json_encode([
                "status" => false,
                "message" => "Sản phẩm không tồn tại"
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }

        // Thêm vào giỏ hàng
        $result = $gioHangModel->addToCart($makh, $masp, $sl_them);
        
        echo json_encode([
            "status" => $result['success'],
            "message" => $result['message']
        ], JSON_UNESCAPED_UNICODE);
        break;

    case "update":
        if (!isset($post_data["makh"]) || !isset($post_data["masp"]) || !isset($post_data["sl"])) {
            echo json_encode([
                "status" => false,
                "message" => "Chưa điền đầy đủ thông tin: makh, masp, sl"
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $makh = (int)$post_data["makh"];
        $masp = (int)$post_data["masp"];
        $sl_moi = (int)$post_data["sl"];

        $magio = $gioHangModel->getCartId($makh);
        
        if (!$magio) {
            echo json_encode([
                "status" => false,
                "message" => "Không tìm thấy giỏ hàng"
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }

        // Kiểm tra tồn kho
        $stock = $sanPhamModel->getStock($masp);
        if ($sl_moi > $stock) {
            echo json_encode([
                "status" => false,
                "message" => "Số lượng vượt quá tồn kho (còn $stock sản phẩm)"
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }

        if ($sl_moi <= 0) {
            $result = $gioHangModel->removeItem($magio, $masp);
            $message = "Đã xóa sản phẩm khỏi giỏ hàng";
        } else {
            $result = $gioHangModel->updateItemQuantity($magio, $masp, $sl_moi);
            $message = "Cập nhật số lượng thành công";
        }

        echo json_encode([
            "status" => $result !== false,
            "message" => $result !== false ? $message : "Cập nhật thất bại"
        ], JSON_UNESCAPED_UNICODE);
        break;

    case "remove":
        if (!isset($post_data["makh"]) || !isset($post_data["masp"])) {
            echo json_encode([
                "status" => false,
                "message" => "Chưa điền đầy đủ thông tin: makh, masp"
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $makh = (int)$post_data["makh"];
        $masp = (int)$post_data["masp"];

        $magio = $gioHangModel->getCartId($makh);
        
        if ($magio) {
            $result = $gioHangModel->removeItem($magio, $masp);
            echo json_encode([
                "status" => $result !== false,
                "message" => $result !== false ? "Xóa sản phẩm thành công" : "Xóa thất bại"
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Không tìm thấy giỏ hàng"
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "clear":
        if (!isset($post_data["makh"])) {
            echo json_encode([
                "status" => false,
                "message" => "Thiếu mã khách hàng (makh)"
            ], JSON_UNESCAPED_UNICODE);
            exit();
        }

        $makh = (int)$post_data["makh"];
        $magio = $gioHangModel->getCartId($makh);
        
        if ($magio) {
            $result = $gioHangModel->clearCart($magio);
            echo json_encode([
                "status" => $result !== false,
                "message" => $result !== false ? "Xóa giỏ hàng thành công" : "Xóa thất bại"
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "status" => false,
                "message" => "Không tìm thấy giỏ hàng"
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    default:
        echo json_encode([
            "status" => false,
            "message" => "Hành động không hợp lệ. Sử dụng: getall, get, add, update, remove, clear"
        ], JSON_UNESCAPED_UNICODE);
        break;
}
?>