<?php
/**
 * ThongSo Model - Quản lý thông số kỹ thuật sản phẩm
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once BASE_PATH . '/core/Model.php';

class ThongSo extends Model {
    protected $table = 'thongso';
    protected $primaryKey = 'mats';
    
    /**
     * Lấy tất cả thông số của một sản phẩm
     */
    public function getByProduct($masp) {
        $sql = "SELECT ts.mats, ts.tents, ts.giatri, ts.masp, sp.tensp
                FROM thongso ts
                JOIN sanpham sp ON ts.masp = sp.masp
                WHERE ts.masp = ?
                ORDER BY ts.mats ASC";
        return $this->db->select($sql, 's', [$masp]);
    }
    
    /**
     * Lấy một thông số theo mã
     */
    public function getOne($mats) {
        $sql = "SELECT ts.mats, ts.tents, ts.giatri, ts.masp, sp.tensp
                FROM thongso ts
                JOIN sanpham sp ON ts.masp = sp.masp
                WHERE ts.mats = ?";
        $result = $this->db->select($sql, 'i', [$mats]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Thêm thông số mới
     */
    public function add($tents, $masp, $giatri) {
        $sql = "INSERT INTO thongso (tents, masp, giatri) VALUES (?, ?, ?)";
        return $this->db->insert($sql, 'sss', [$tents, $masp, $giatri]);
    }
    
    /**
     * Cập nhật thông số
     */
    public function updateSpec($mats, $tents, $masp, $giatri) {
        $sql = "UPDATE thongso SET tents = ?, masp = ?, giatri = ? WHERE mats = ?";
        return $this->db->execute($sql, 'sssi', [$tents, $masp, $giatri, $mats]);
    }
    
    /**
     * Xóa thông số
     */
    public function deleteSpec($mats) {
        return $this->delete($mats);
    }
    
    /**
     * Xóa tất cả thông số của sản phẩm
     */
    public function deleteByProduct($masp) {
        $sql = "DELETE FROM thongso WHERE masp = ?";
        return $this->db->execute($sql, 's', [$masp]);
    }
}
