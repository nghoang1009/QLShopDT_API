<?php
/**
 * ThongKe Model - Thống kê doanh thu và báo cáo
 */

if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}
require_once BASE_PATH . '/core/Model.php';

class ThongKe extends Model {
    protected $table = 'donhang';
    protected $primaryKey = 'madh';
    
    /**
     * Thống kê tổng quan
     */
    public function getOverview() {
        // Tổng doanh thu
        $sql_revenue = "SELECT COALESCE(SUM(trigia), 0) as tong_doanhthu FROM donhang WHERE trangthai = 'Đã giao'";
        $revenue = $this->db->select($sql_revenue);
        
        // Tổng đơn hàng
        $sql_orders = "SELECT COUNT(*) as tong_donhang FROM donhang";
        $orders = $this->db->select($sql_orders);
        
        // Tổng khách hàng
        $sql_customers = "SELECT COUNT(*) as tong_khachhang FROM khachhang";
        $customers = $this->db->select($sql_customers);
        
        // Tổng sản phẩm
        $sql_products = "SELECT COUNT(*) as tong_sanpham FROM sanpham";
        $products = $this->db->select($sql_products);
        
        return [
            'tong_doanhthu' => $revenue[0]['tong_doanhthu'] ?? 0,
            'tong_donhang' => $orders[0]['tong_donhang'] ?? 0,
            'tong_khachhang' => $customers[0]['tong_khachhang'] ?? 0,
            'tong_sanpham' => $products[0]['tong_sanpham'] ?? 0
        ];
    }
    
    /**
     * Thống kê doanh thu theo khoảng thời gian
     */
    public function getRevenueByPeriod($startDate, $endDate) {
        $sql = "SELECT DATE(ngaydat) as ngay, SUM(trigia) as doanhthu, COUNT(*) as so_don
                FROM donhang 
                WHERE ngaydat BETWEEN ? AND ? AND trangthai = 'Đã giao'
                GROUP BY DATE(ngaydat)
                ORDER BY ngay ASC";
        return $this->db->select($sql, 'ss', [$startDate, $endDate]);
    }
    
    /**
     * Thống kê doanh thu theo tháng
     */
    public function getRevenueByMonth($year) {
        $sql = "SELECT MONTH(ngaydat) as thang, SUM(trigia) as doanhthu, COUNT(*) as so_don
                FROM donhang 
                WHERE YEAR(ngaydat) = ? AND trangthai = 'Đã giao'
                GROUP BY MONTH(ngaydat)
                ORDER BY thang ASC";
        return $this->db->select($sql, 'i', [$year]);
    }
    
    /**
     * Sản phẩm bán chạy nhất
     */
    public function getTopProducts($limit = 10) {
        $sql = "SELECT sp.masp, sp.tensp, sp.hinhanh, sp.gia, sp.hang,
                       SUM(ct.soluong) as so_luong_ban, SUM(ct.soluong * ct.dongia) as doanhthu
                FROM chitietdonhang ct
                JOIN sanpham sp ON ct.masp = sp.masp
                JOIN donhang dh ON ct.madh = dh.madh
                WHERE dh.trangthai = 'Đã giao'
                GROUP BY sp.masp
                ORDER BY so_luong_ban DESC
                LIMIT ?";
        return $this->db->select($sql, 'i', [$limit]);
    }
    
    /**
     * Khách hàng mua nhiều nhất
     */
    public function getTopCustomers($limit = 10) {
        $sql = "SELECT kh.makh, kh.tenkh, kh.sdt, kh.diachi,
                       COUNT(dh.madh) as so_don, SUM(dh.trigia) as tong_chi
                FROM khachhang kh
                JOIN donhang dh ON kh.makh = dh.makh
                WHERE dh.trangthai = 'Đã giao'
                GROUP BY kh.makh
                ORDER BY tong_chi DESC
                LIMIT ?";
        return $this->db->select($sql, 'i', [$limit]);
    }
    
    /**
     * Thống kê theo trạng thái đơn hàng
     */
    public function getOrdersByStatus() {
        $sql = "SELECT trangthai, COUNT(*) as so_luong 
                FROM donhang 
                GROUP BY trangthai";
        return $this->db->select($sql);
    }
    
    /**
     * Thống kê theo danh mục
     */
    public function getRevenueByCategory() {
        $sql = "SELECT dm.madm, dm.tendm, 
                       COUNT(DISTINCT ct.madh) as so_don,
                       SUM(ct.soluong) as so_luong_ban,
                       SUM(ct.soluong * ct.dongia) as doanhthu
                FROM danhmuc dm
                JOIN sanpham sp ON dm.madm = sp.madm
                JOIN chitietdonhang ct ON sp.masp = ct.masp
                JOIN donhang dh ON ct.madh = dh.madh
                WHERE dh.trangthai = 'Đã giao'
                GROUP BY dm.madm
                ORDER BY doanhthu DESC";
        return $this->db->select($sql);
    }
}
