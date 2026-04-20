<?php
/**
 * DonHang Model - Quản lý đơn hàng
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once BASE_PATH . '/core/Model.php';

class DonHang extends Model {
    protected $table = 'donhang';
    protected $primaryKey = 'madh';
    
    /**
     * Lấy tất cả đơn hàng với thông tin khách hàng và nhân viên
     */
    public function getAllWithDetails() {
        $sql = "SELECT dh.*, kh.tenkh, kh.diachi, kh.sdt, nv.tennv 
                FROM donhang dh 
                LEFT JOIN khachhang kh ON dh.makh = kh.makh 
                LEFT JOIN nhanvien nv ON dh.manv = nv.manv 
                ORDER BY dh.madh DESC";
        return $this->db->select($sql);
    }
    
    /**
     * Lấy đơn hàng theo mã khách hàng
     */
    public function getByCustomer($makh) {
        $sql = "SELECT dh.*, kh.tenkh, kh.diachi, kh.sdt, nv.tennv 
                FROM donhang dh 
                LEFT JOIN khachhang kh ON dh.makh = kh.makh 
                LEFT JOIN nhanvien nv ON dh.manv = nv.manv 
                WHERE dh.makh = ?
                ORDER BY dh.madh DESC";
        return $this->db->select($sql, 'i', [$makh]);
    }
    
    /**
     * Lấy chi tiết 1 đơn hàng
     */
    public function getOneWithDetails($madh) {
        $sql = "SELECT dh.*, kh.tenkh, kh.diachi, kh.sdt, nv.tennv 
                FROM donhang dh 
                LEFT JOIN khachhang kh ON dh.makh = kh.makh 
                LEFT JOIN nhanvien nv ON dh.manv = nv.manv 
                WHERE dh.madh = ?";
        $result = $this->db->select($sql, 'i', [$madh]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Lấy chi tiết các sản phẩm trong đơn hàng
     */
    public function getOrderDetails($madh) {
        $sql = "SELECT ct.*, sp.tensp, sp.gia, sp.hinhanh, sp.hang
                FROM chitietdonhang ct 
                LEFT JOIN sanpham sp ON ct.masp = sp.masp 
                WHERE ct.madh = ?";
        return $this->db->select($sql, 'i', [$madh]);
    }
    
    /**
     * Tạo đơn hàng mới
     */
    public function createOrder($makh, $trigia, $manv = null) {
        $sql = "INSERT INTO donhang (makh, ngaydat, trigia, trangthai, manv) 
                VALUES (?, NOW(), ?, 'Chờ xác nhận', ?)";
        return $this->db->insert($sql, 'idi', [$makh, $trigia, $manv]);
    }
    
    /**
     * Thêm chi tiết đơn hàng
     */
    public function addOrderDetail($madh, $masp, $soluong, $dongia) {
        $sql = "INSERT INTO chitietdonhang (madh, masp, soluong, dongia) 
                VALUES (?, ?, ?, ?)";
        return $this->db->insert($sql, 'iiid', [$madh, $masp, $soluong, $dongia]);
    }
    
    /**
     * Cập nhật trạng thái đơn hàng
     */
    public function updateStatus($madh, $trangthai) {
        $sql = "UPDATE donhang SET trangthai = ? WHERE madh = ?";
        return $this->db->execute($sql, 'si', [$trangthai, $madh]);
    }
    
    /**
     * Cập nhật trị giá đơn hàng
     */
    public function updateTrigia($madh, $trigia) {
        $sql = "UPDATE donhang SET trigia = ? WHERE madh = ?";
        return $this->db->execute($sql, 'di', [$trigia, $madh]);
    }
    
    /**
     * Xóa đơn hàng và chi tiết
     */
    public function deleteOrder($madh) {
        // Xóa chi tiết trước
        $this->db->execute("DELETE FROM chitietdonhang WHERE madh = ?", 'i', [$madh]);
        // Xóa đơn hàng
        return $this->db->execute("DELETE FROM donhang WHERE madh = ?", 'i', [$madh]);
    }
}
