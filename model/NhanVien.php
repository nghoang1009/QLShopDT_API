<?php
/**
 * NhanVien Model - Quản lý nhân viên
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once BASE_PATH . '/core/Model.php';

class NhanVien extends Model {
    protected $table = 'nhanvien';
    protected $primaryKey = 'manv';
    
    /**
     * Lấy tất cả nhân viên
     */
    public function getAll($orderBy = 'manv ASC') {
        return $this->db->select("SELECT * FROM nhanvien ORDER BY $orderBy");
    }
    
    /**
     * Tìm nhân viên theo ID
     */
    public function findById($manv) {
        $sql = "SELECT * FROM nhanvien WHERE manv = ?";
        $result = $this->db->select($sql, 'i', [$manv]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Thêm nhân viên mới
     */
    public function add($data) {
        $sql = "INSERT INTO nhanvien (manv, tennv, diachi, sdt, ns) VALUES (?, ?, ?, ?, ?)";
        return $this->db->insert($sql, 'issss', [
            $data['manv'],
            $data['tennv'],
            $data['diachi'] ?? '',
            $data['sdt'] ?? '',
            $data['ns'] ?? null
        ]);
    }
    
    /**
     * Cập nhật thông tin nhân viên
     */
    public function updateStaff($manv, $data) {
        $sql = "UPDATE nhanvien SET tennv = ?, sdt = ?, ns = ? WHERE manv = ?";
        return $this->db->execute($sql, 'sssi', [
            $data['tennv'],
            $data['sdt'] ?? '',
            $data['ns'] ?? null,
            $manv
        ]);
    }
    
    /**
     * Xóa nhân viên
     */
    public function deleteStaff($manv) {
        return $this->delete($manv);
    }
    
    /**
     * Tìm kiếm nhân viên
     */
    public function search($keyword) {
        $sql = "SELECT * FROM nhanvien WHERE tennv LIKE ? OR sdt LIKE ? ORDER BY manv ASC";
        return $this->db->select($sql, 'ss', ["%$keyword%", "%$keyword%"]);
    }
    
    /**
     * Đếm tổng số nhân viên
     */
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM nhanvien";
        $result = $this->db->select($sql);
        return $result ? (int)$result[0]['total'] : 0;
    }
}
