<?php
/**
 * DanhMuc Model - Quản lý danh mục sản phẩm
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once BASE_PATH . '/core/Model.php';

class DanhMuc extends Model {
    protected $table = 'danhmuc';
    protected $primaryKey = 'madm';
    
    /**
     * Lấy tất cả danh mục
     */
    public function getAllCategories() {
        return $this->getAll('madm ASC');
    }
    
    /**
     * Tìm danh mục theo ID
     */
    public function findById($madm) {
        $sql = "SELECT * FROM {$this->table} WHERE madm = ?";
        $result = $this->db->select($sql, 'i', [$madm]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Thêm danh mục mới
     */
    public function add($tendm) {
        return $this->create(['tendm' => $tendm]);
    }
    
    /**
     * Cập nhật danh mục
     */
    public function updateCategory($madm, $tendm) {
        return $this->update($madm, ['tendm' => $tendm]);
    }
    
    /**
     * Xóa danh mục
     */
    public function deleteCategory($madm) {
        return $this->delete($madm);
    }
    
    /**
     * Kiểm tra danh mục có sản phẩm không
     */
    public function hasProducts($madm) {
        $sql = "SELECT COUNT(*) as total FROM sanpham WHERE madm = ?";
        $result = $this->db->select($sql, 'i', [$madm]);
        return $result && (int)$result[0]['total'] > 0;
    }
    
    /**
     * Tìm kiếm danh mục theo tên
     */
    public function search($keyword) {
        $sql = "SELECT * FROM {$this->table} WHERE tendm LIKE ? ORDER BY madm ASC";
        return $this->db->select($sql, 's', ["%$keyword%"]);
    }
}
