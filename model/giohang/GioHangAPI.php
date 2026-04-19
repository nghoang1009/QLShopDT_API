<?php
require $_SERVER['DOCUMENT_ROOT'] . "/QLShopDT_API/includes/api_helper.php";

class GioHangAPI {
    /**
     * @return array - Trả về toàn bộ giỏ hàng có trong CSDL
     */
    public static function getAllGioHang(){
        $result = callGioHangAPI(["action" => "getall"]);
        return ($result && $result["status"]) ? $result["data"] : [];
    }

    /**
     * @param int $makh - Mã của khách hàng
     * @return array - Trả về toàn bộ item có trong giỏ của khách hàng đó
     */
    public static function getGioHang($makh){
        $result = callGioHangAPI(["action" => "get", "makh" => "$makh"]);
        return ($result && $result["status"]) ? $result["data"] : [];
    }
}
?>