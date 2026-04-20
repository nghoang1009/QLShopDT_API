<?php
/**
 * KhachHang Model - Quản lý khách hàng
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once BASE_PATH . '/core/Model.php';

class KhachHang extends Model {
    protected $table = 'khachhang';
    protected $primaryKey = 'makh';
    
    /**
     * Lấy tất cả khách hàng kèm thông tin tài khoản
     */
    public function getAllWithAccount($orderBy = 'kh.makh DESC') {
        $sql = "SELECT kh.*, tk.tentk, tk.role 
                FROM khachhang kh 
                LEFT JOIN taikhoan tk ON kh.makh = tk.matk 
                ORDER BY $orderBy";
        return $this->db->select($sql);
    }
    
    /**
     * Lấy tất cả khách hàng
     */
    public function getAll($orderBy = 'makh ASC') {
        return $this->db->select("SELECT * FROM khachhang ORDER BY $orderBy");
    }
    
    /**
     * Tìm khách hàng theo ID kèm thông tin tài khoản
     */
    public function findWithAccount($makh) {
        $sql = "SELECT kh.*, tk.tentk 
                FROM khachhang kh 
                LEFT JOIN taikhoan tk ON kh.makh = tk.matk 
                WHERE kh.makh = ?";
        $result = $this->db->select($sql, 'i', [$makh]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Tìm khách hàng theo ID
     */
    public function findById($makh) {
        $sql = "SELECT * FROM khachhang WHERE makh = ?";
        $result = $this->db->select($sql, 'i', [$makh]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Tìm khách hàng theo username
     */
    public function findByUsername($username) {
        $sql = "SELECT kh.makh 
                FROM taikhoan tk 
                JOIN khachhang kh ON tk.matk = kh.makh 
                WHERE tk.tentk = ?";
        $result = $this->db->select($sql, 's', [$username]);
        return $result ? (int)$result[0]['makh'] : false;
    }
    
    /**
     * Tìm khách hàng theo mã tài khoản
     */
    public function findByAccountId($matk) {
        $sql = "SELECT * FROM khachhang WHERE makh = ?";
        $result = $this->db->select($sql, 'i', [$matk]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Thêm khách hàng mới
     */
    public function add($data) {
        $sql = "INSERT INTO khachhang (makh, tenkh, diachi, sdt) VALUES (?, ?, ?, ?)";
        return $this->db->insert($sql, 'isss', [
            $data['makh'],
            $data['tenkh'],
            $data['diachi'] ?? '',
            $data['sdt'] ?? ''
        ]);
    }
    
    /**
     * Cập nhật thông tin khách hàng
     */
    public function updateCustomer($makh, $data) {
        $sql = "UPDATE khachhang SET tenkh = ?, diachi = ?, sdt = ? WHERE makh = ?";
        return $this->db->execute($sql, 'sssi', [
            $data['tenkh'],
            $data['diachi'] ?? '',
            $data['sdt'] ?? '',
            $makh
        ]);
    }
    
    /**
     * Xóa khách hàng
     */
    public function deleteCustomer($makh) {
        return $this->delete($makh);
    }
    
    /**
     * Tìm kiếm khách hàng
     */
    public function search($keyword) {
        $sql = "SELECT * FROM khachhang WHERE tenkh LIKE ? OR sdt LIKE ? OR diachi LIKE ? ORDER BY makh ASC";
        return $this->db->select($sql, 'sss', ["%$keyword%", "%$keyword%", "%$keyword%"]);
    }
    
    /**
     * Đếm tổng số khách hàng
     */
    public function countAll() {
        $sql = "SELECT COUNT(*) as total FROM khachhang";
        $result = $this->db->select($sql);
        return $result ? (int)$result[0]['total'] : 0;
    }
}
