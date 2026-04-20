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
     * Trang thống kê tổng quan (web view)
     */
    public function index() {
        $this->requireRole([1, 2]);

        $overview = $this->thongKeModel->getOverview();
        $topProducts = $this->thongKeModel->getTopProducts(5);
        $topCustomers = $this->thongKeModel->getTopCustomers(5);
        $ordersByStatus = $this->thongKeModel->getOrdersByStatus();
        $revenueByCategory = $this->thongKeModel->getRevenueByCategory();

        $this->view('thongke/index', [
            'page_title' => 'Thống kê',
            'active_nav' => 'thongke',
            'overview' => $overview,
            'topProducts' => $topProducts,
            'topCustomers' => $topCustomers,
            'ordersByStatus' => $ordersByStatus,
            'revenueByCategory' => $revenueByCategory,
        ]);
    }
    
    // ===================== RESTful API Methods =====================

    /**
     * GET /api/thongke
     * Tổng quan thống kê
     */
    public function apiIndex() {
        header('Content-Type: application/json');
        $this->requireRole([1, 2]);
        $overview = $this->thongKeModel->getOverview();
        echo json_encode(['status' => true, 'data' => $overview], JSON_UNESCAPED_UNICODE);
    }

    /**
     * GET /api/thongke/revenue
     * ?year=2025  hoặc  ?start_date=2025-01-01&end_date=2025-12-31
     */
    public function apiRevenue() {
        header('Content-Type: application/json');
        $this->requireRole([1, 2]);
        $year      = $_GET['year']       ?? date('Y');
        $startDate = $_GET['start_date'] ?? null;
        $endDate   = $_GET['end_date']   ?? null;

        if ($startDate && $endDate) {
            $data = $this->thongKeModel->getRevenueByPeriod($startDate, $endDate);
        } else {
            $data = $this->thongKeModel->getRevenueByMonth((int)$year);
        }
        echo json_encode(['status' => true, 'data' => $data ?: []], JSON_UNESCAPED_UNICODE);
    }

    /**
     * GET /api/thongke/products
     * ?limit=20
     */
    public function apiProducts() {
        header('Content-Type: application/json');
        $this->requireRole([1, 2]);
        $limit = (int)($_GET['limit'] ?? 20);
        $data  = $this->thongKeModel->getTopProducts($limit);
        echo json_encode(['status' => true, 'data' => $data ?: []], JSON_UNESCAPED_UNICODE);
    }

    /**
     * GET /api/thongke/customers
     * ?limit=20
     */
    public function apiCustomers() {
        header('Content-Type: application/json');
        $this->requireRole([1, 2]);
        $limit = (int)($_GET['limit'] ?? 20);
        $data  = $this->thongKeModel->getTopCustomers($limit);
        echo json_encode(['status' => true, 'data' => $data ?: []], JSON_UNESCAPED_UNICODE);
    }

    /**
     * GET /api/thongke/orders
     * Thống kê theo trạng thái đơn hàng
     */
    public function apiOrders() {
        header('Content-Type: application/json');
        $this->requireRole([1, 2]);
        $data = $this->thongKeModel->getOrdersByStatus();
        echo json_encode(['status' => true, 'data' => $data ?: []], JSON_UNESCAPED_UNICODE);
    }

    /**
     * GET /api/thongke/category
     * Doanh thu theo danh mục
     */
    public function apiCategory() {
        header('Content-Type: application/json');
        $this->requireRole([1, 2]);
        $data = $this->thongKeModel->getRevenueByCategory();
        echo json_encode(['status' => true, 'data' => $data ?: []], JSON_UNESCAPED_UNICODE);
    }
}
