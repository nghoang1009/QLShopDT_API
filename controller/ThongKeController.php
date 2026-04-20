<?php
/**
 * ThongKeController - Controller thống kê và báo cáo
 */

class ThongKeController extends Controller {
    
    private $thongKeModel;
    
    public function __construct() {
        parent::__construct();
        require_once BASE_PATH . '/model/ThongKe.php';
        $this->thongKeModel = new ThongKe();
    }
    
    /**
     * Trang thống kê tổng quan
     */
    public function index() {
        $this->requireRole([1, 2]); // Chỉ admin và nhân viên
        
        // Lấy dữ liệu tổng quan
        $overview = $this->thongKeModel->getOverview();
        
        // Top sản phẩm bán chạy
        $topProducts = $this->thongKeModel->getTopProducts(5);
        
        // Top khách hàng
        $topCustomers = $this->thongKeModel->getTopCustomers(5);
        
        // Thống kê theo trạng thái đơn hàng
        $ordersByStatus = $this->thongKeModel->getOrdersByStatus();
        
        // Doanh thu theo danh mục
        $revenueByCategory = $this->thongKeModel->getRevenueByCategory();
        
        $this->view('thongke/index', [
            'page_title' => 'Thống kê',
            'active_nav' => 'thongke',
            'overview' => $overview,
            'topProducts' => $topProducts,
            'topCustomers' => $topCustomers,
            'ordersByStatus' => $ordersByStatus,
            'revenueByCategory' => $revenueByCategory
        ]);
    }
    
    /**
     * Thống kê doanh thu
     */
    public function revenue() {
        $this->requireRole([1, 2]);
        
        // Lấy tham số
        $year = $this->input('year', date('Y'));
        $startDate = $this->input('start_date');
        $endDate = $this->input('end_date');
        
        // Doanh thu theo tháng
        $monthlyRevenue = $this->thongKeModel->getRevenueByMonth($year);
        
        // Doanh thu theo khoảng thời gian (nếu có)
        $periodRevenue = null;
        if ($startDate && $endDate) {
            $periodRevenue = $this->thongKeModel->getRevenueByPeriod($startDate, $endDate);
        }
        
        // Tổng quan
        $overview = $this->thongKeModel->getOverview();
        
        $this->view('thongke/revenue', [
            'page_title' => 'Thống kê Doanh thu',
            'active_nav' => 'thongke',
            'year' => $year,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'monthlyRevenue' => $monthlyRevenue,
            'periodRevenue' => $periodRevenue,
            'overview' => $overview
        ]);
    }
    
    /**
     * Thống kê sản phẩm
     */
    public function products() {
        $this->requireRole([1, 2]);
        
        $limit = $this->input('limit', 20);
        
        // Top sản phẩm bán chạy
        $topProducts = $this->thongKeModel->getTopProducts($limit);
        
        // Doanh thu theo danh mục
        $revenueByCategory = $this->thongKeModel->getRevenueByCategory();
        
        $this->view('thongke/products', [
            'page_title' => 'Thống kê Sản phẩm',
            'active_nav' => 'thongke',
            'topProducts' => $topProducts,
            'revenueByCategory' => $revenueByCategory,
            'limit' => $limit
        ]);
    }
    
    /**
     * Thống kê khách hàng
     */
    public function customers() {
        $this->requireRole([1, 2]);
        
        $limit = $this->input('limit', 20);
        
        // Top khách hàng
        $topCustomers = $this->thongKeModel->getTopCustomers($limit);
        
        // Tổng số khách hàng
        $overview = $this->thongKeModel->getOverview();
        
        $this->view('thongke/customers', [
            'page_title' => 'Thống kê Khách hàng',
            'active_nav' => 'thongke',
            'topCustomers' => $topCustomers,
            'totalCustomers' => $overview['tong_khachhang'],
            'limit' => $limit
        ]);
    }
    
    /**
     * Thống kê đơn hàng
     */
    public function orders() {
        $this->requireRole([1, 2]);
        
        // Thống kê theo trạng thái
        $ordersByStatus = $this->thongKeModel->getOrdersByStatus();
        
        // Tổng quan
        $overview = $this->thongKeModel->getOverview();
        
        $this->view('thongke/orders', [
            'page_title' => 'Thống kê Đơn hàng',
            'active_nav' => 'thongke',
            'ordersByStatus' => $ordersByStatus,
            'totalOrders' => $overview['tong_donhang']
        ]);
    }
    
    /**
     * API lấy dữ liệu chart (AJAX)
     */
    public function chartData() {
        $this->requireRole([1, 2]);
        
        $type = $this->input('type', 'monthly');
        $year = $this->input('year', date('Y'));
        
        $data = [];
        
        switch ($type) {
            case 'monthly':
                $data = $this->thongKeModel->getRevenueByMonth($year);
                break;
            case 'category':
                $data = $this->thongKeModel->getRevenueByCategory();
                break;
            case 'status':
                $data = $this->thongKeModel->getOrdersByStatus();
                break;
            default:
                $data = $this->thongKeModel->getOverview();
        }
        
        $this->json(['success' => true, 'data' => $data]);
    }
    
    /**
     * Xuất báo cáo PDF/Excel (placeholder)
     */
    public function export() {
        $this->requireRole([1, 2]);
        
        $format = $this->input('format', 'pdf');
        $type = $this->input('type', 'overview');
        
        // TODO: Implement export functionality
        // For now, redirect back with message
        
        $this->setFlash('info', 'Tính năng xuất báo cáo đang được phát triển');
        $this->redirect('/thongke');
    }
}
