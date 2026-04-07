<?php
include_once "../../includes/api_helper.php";

class NhanVien {
    /**
     * Lấy tất cả nhân viên từ API
     * @return array Mảng các nhân viên
     */
    public static function getAllEmployees() {
        $result = callNhanvienAPI(['action' => 'getall']);
        return ($result && $result['status']) ? $result['data'] : [];
    }
}
?>