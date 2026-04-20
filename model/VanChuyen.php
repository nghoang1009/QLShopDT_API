<?php
/**
 * VanChuyen Model - Quản lý vận chuyển
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once BASE_PATH . '/core/Model.php';

class VanChuyen extends Model {
    protected $table = 'vanchuyen';
    protected $primaryKey = 'mavc';
    
    /**
     * Lấy tất cả vận chuyển với thông tin khách hàng và đơn hàng
     */
    public function getAllWithDetails() {
        $sql = "SELECT vc.*, kh.tenkh, kh.diachi, kh.sdt, dh.ngaydat, dh.trigia
                FROM vanchuyen vc
                LEFT JOIN khachhang kh ON vc.makh = kh.makh
                LEFT JOIN donhang dh ON vc.madh = dh.madh
                ORDER BY vc.mavc ASC";
        return $this->db->select($sql);
    }
    
    /**
     * Lấy một vận chuyển theo mã
     */
    public function getOneWithDetails($mavc) {
        $sql = "SELECT vc.*, kh.tenkh, kh.diachi, kh.sdt, dh.ngaydat, dh.trigia
                FROM vanchuyen vc
                LEFT JOIN khachhang kh ON vc.makh = kh.makh
                LEFT JOIN donhang dh ON vc.madh = dh.madh
                WHERE vc.mavc = ?";
        $result = $this->db->select($sql, 'i', [$mavc]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Lấy vận chuyển theo đơn hàng
     */
    public function getByOrder($madh) {
        $sql = "SELECT vc.*, kh.tenkh, kh.diachi, kh.sdt
                FROM vanchuyen vc
                LEFT JOIN khachhang kh ON vc.makh = kh.makh
                WHERE vc.madh = ?";
        $result = $this->db->select($sql, 'i', [$madh]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Thêm vận chuyển mới
     */
    public function add($madh, $makh, $ngaygiao) {
        $sql = "INSERT INTO vanchuyen (madh, makh, ngaygiao) VALUES (?, ?, ?)";
        return $this->db->insert($sql, 'iis', [$madh, $makh, $ngaygiao]);
    }
    
    /**
     * Cập nhật vận chuyển
     */
    public function updateShipping($mavc, $madh, $makh, $ngaygiao) {
        $sql = "UPDATE vanchuyen SET madh = ?, makh = ?, ngaygiao = ? WHERE mavc = ?";
        return $this->db->execute($sql, 'iisi', [$madh, $makh, $ngaygiao, $mavc]);
    }
    
    /**
     * Xóa vận chuyển
     */
    public function deleteShipping($mavc) {
        return $this->delete($mavc);
    }
}
