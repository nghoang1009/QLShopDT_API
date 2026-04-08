<?php
include_once "../../includes/api_helper.php";

class DanhMuc {
    /**
     * Lấy tất cả danh mục từ API
     * @return array Mảng các danh mục
     */
    public static function getAllCategories() {
        $result = callDanhmucAPI(['action' => 'getall']);
        return ($result && $result['status']) ? $result['data'] : [];
    }
}
?>