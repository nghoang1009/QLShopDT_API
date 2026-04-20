<?php
/**
 * ThanhToan Model - Quản lý thanh toán
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once BASE_PATH . '/core/Model.php';

class ThanhToan extends Model {
    protected $table = 'thanhtoan';
    protected $primaryKey = 'matt';
    
    /**
     * Lấy tất cả thanh toán kèm thông tin đơn hàng, khách hàng
     */
    public function getAllWithDetails() {
        $sql = "SELECT tt.*, dh.ngaydat, dh.trigia, kh.tenkh, kh.sdt, nv.tennv 
                FROM thanhtoan tt
                JOIN donhang dh ON tt.madh = dh.madh
                JOIN khachhang kh ON dh.makh = kh.makh
                LEFT JOIN nhanvien nv ON dh.manv = nv.manv
                ORDER BY tt.ngaythanhtoan DESC";
        return $this->db->select($sql);
    }
    
    /**
     * Lấy thanh toán theo khách hàng
     */
    public function getByCustomer($makh) {
        $sql = "SELECT tt.*, dh.ngaydat, dh.trigia, kh.tenkh, kh.sdt, nv.tennv 
                FROM thanhtoan tt
                JOIN donhang dh ON tt.madh = dh.madh
                JOIN khachhang kh ON dh.makh = kh.makh
                LEFT JOIN nhanvien nv ON dh.manv = nv.manv
                WHERE kh.makh = ?
                ORDER BY tt.ngaythanhtoan DESC";
        return $this->db->select($sql, 'i', [$makh]);
    }
    
    /**
     * Lấy một thanh toán theo mã
     */
    public function getOneWithDetails($matt) {
        $sql = "SELECT tt.*, dh.ngaydat, dh.trigia, dh.trangthai as trangthai_dh, 
                       kh.tenkh, kh.sdt, kh.diachi, nv.tennv 
                FROM thanhtoan tt
                JOIN donhang dh ON tt.madh = dh.madh
                JOIN khachhang kh ON dh.makh = kh.makh
                LEFT JOIN nhanvien nv ON dh.manv = nv.manv
                WHERE tt.matt = ?";
        $result = $this->db->select($sql, 'i', [$matt]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Lấy thanh toán theo đơn hàng
     */
    public function getByOrder($madh) {
        $sql = "SELECT * FROM thanhtoan WHERE madh = ?";
        $result = $this->db->select($sql, 'i', [$madh]);
        return $result ? $result[0] : null;
    }
    
    /**
     * Thêm thanh toán mới
     */
    public function add($data) {
        $sql = "INSERT INTO thanhtoan (madh, phuongthuc, ngaythanhtoan, sotien, trangthai, ghichu) 
                VALUES (?, ?, ?, ?, ?, ?)";
        return $this->db->insert($sql, 'issdss', [
            $data['madh'],
            $data['phuongthuc'],
            $data['ngaythanhtoan'] ?? date('Y-m-d H:i:s'),
            $data['sotien'],
            $data['trangthai'] ?? 'Chờ xác nhận',
            $data['ghichu'] ?? ''
        ]);
    }
    
    /**
     * Cập nhật thanh toán
     */
    public function updatePayment($matt, $data) {
        $sql = "UPDATE thanhtoan SET phuongthuc = ?, ngaythanhtoan = ?, sotien = ?, trangthai = ?, ghichu = ? 
                WHERE matt = ?";
        return $this->db->execute($sql, 'ssdssi', [
            $data['phuongthuc'],
            $data['ngaythanhtoan'],
            $data['sotien'],
            $data['trangthai'],
            $data['ghichu'] ?? '',
            $matt
        ]);
    }
    
    /**
     * Cập nhật trạng thái thanh toán
     */
    public function updateStatus($matt, $trangthai) {
        $sql = "UPDATE thanhtoan SET trangthai = ? WHERE matt = ?";
        return $this->db->execute($sql, 'si', [$trangthai, $matt]);
    }
    
    /**
     * Xóa thanh toán
     */
    public function deletePayment($matt) {
        return $this->delete($matt);
    }
    
    /**
     * Lấy danh sách đơn hàng chưa thanh toán
     */
    public function getUnpaidOrders() {
        $sql = "SELECT dh.*, kh.tenkh, kh.sdt 
                FROM donhang dh
                JOIN khachhang kh ON dh.makh = kh.makh
                WHERE dh.madh NOT IN (SELECT madh FROM thanhtoan WHERE trangthai = 'Đã thanh toán')
                ORDER BY dh.ngaydat DESC";
        return $this->db->select($sql);
    }
    
    /**
     * Các phương thức thanh toán hợp lệ
     */
    public function getPaymentMethods() {
        return [
            'Tiền mặt',
            'Chuyển khoản',
            'Thẻ tín dụng',
            'Ví điện tử',
            'COD'
        ];
    }
    
    /**
     * Các trạng thái thanh toán hợp lệ
     */
    public function getPaymentStatuses() {
        return [
            'Chờ xác nhận',
            'Đã thanh toán',
            'Đã hủy',
            'Hoàn tiền'
        ];
    }
}
