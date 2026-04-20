<?php
/**
 * Thống kê API - Sử dụng Model
 */
header("Content-Type: application/json; charset=utf-8");

// Load Model
require_once dirname(__DIR__) . '/model/ThongKe.php';

// Đọc dữ liệu từ input
$data = json_decode(file_get_contents("php://input"), true);
$action = isset($data['action']) ? $data['action'] : '';

// Khởi tạo Model
$model = new ThongKe();

// TỔNG QUAN
if ($action == 'overview' || $action == 'getoverview') {
    $overview = $model->getOverview();
    
    echo json_encode([
        "status" => true,
        "message" => "Lấy dữ liệu tổng quan thành công",
        "data" => $overview
    ], JSON_UNESCAPED_UNICODE);
}

// DOANH THU THEO KHOẢNG THỜI GIAN
else if ($action == 'revenue_period' || $action == 'getrevenueperiod') {
    $startDate = isset($data['start_date']) ? $data['start_date'] : date('Y-m-01');
    $endDate = isset($data['end_date']) ? $data['end_date'] : date('Y-m-d');
    
    $revenue = $model->getRevenueByPeriod($startDate, $endDate);
    
    // Tính tổng doanh thu trong khoảng
    $total = 0;
    $totalOrders = 0;
    if ($revenue) {
        foreach ($revenue as $item) {
            $total += $item['doanhthu'];
            $totalOrders += $item['so_don'];
        }
    }
    
    echo json_encode([
        "status" => true,
        "message" => "Lấy doanh thu theo khoảng thời gian thành công",
        "data" => $revenue ?: [],
        "summary" => [
            "start_date" => $startDate,
            "end_date" => $endDate,
            "total_revenue" => $total,
            "total_orders" => $totalOrders
        ]
    ], JSON_UNESCAPED_UNICODE);
}

// DOANH THU THEO THÁNG
else if ($action == 'revenue_monthly' || $action == 'getrevenuemonthly') {
    $year = isset($data['year']) ? (int)$data['year'] : (int)date('Y');
    
    $revenue = $model->getRevenueByMonth($year);
    
    // Tính tổng năm
    $totalYear = 0;
    $totalOrders = 0;
    if ($revenue) {
        foreach ($revenue as $item) {
            $totalYear += $item['doanhthu'];
            $totalOrders += $item['so_don'];
        }
    }
    
    echo json_encode([
        "status" => true,
        "message" => "Lấy doanh thu theo tháng thành công",
        "data" => $revenue ?: [],
        "year" => $year,
        "summary" => [
            "total_revenue" => $totalYear,
            "total_orders" => $totalOrders
        ]
    ], JSON_UNESCAPED_UNICODE);
}

// TOP SẢN PHẨM BÁN CHẠY
else if ($action == 'top_products' || $action == 'gettopproducts') {
    $limit = isset($data['limit']) ? (int)$data['limit'] : 10;
    
    $products = $model->getTopProducts($limit);
    
    echo json_encode([
        "status" => true,
        "message" => "Lấy top sản phẩm bán chạy thành công",
        "data" => $products ?: [],
        "limit" => $limit
    ], JSON_UNESCAPED_UNICODE);
}

// TOP KHÁCH HÀNG
else if ($action == 'top_customers' || $action == 'gettopcustomers') {
    $limit = isset($data['limit']) ? (int)$data['limit'] : 10;
    
    $customers = $model->getTopCustomers($limit);
    
    echo json_encode([
        "status" => true,
        "message" => "Lấy top khách hàng thành công",
        "data" => $customers ?: [],
        "limit" => $limit
    ], JSON_UNESCAPED_UNICODE);
}

// THỐNG KÊ THEO TRẠNG THÁI ĐƠN HÀNG
else if ($action == 'orders_by_status' || $action == 'getordersbystatus') {
    $stats = $model->getOrdersByStatus();
    
    echo json_encode([
        "status" => true,
        "message" => "Lấy thống kê theo trạng thái thành công",
        "data" => $stats ?: []
    ], JSON_UNESCAPED_UNICODE);
}

// DOANH THU THEO DANH MỤC
else if ($action == 'revenue_by_category' || $action == 'getrevenuebycategory') {
    $revenue = $model->getRevenueByCategory();
    
    echo json_encode([
        "status" => true,
        "message" => "Lấy doanh thu theo danh mục thành công",
        "data" => $revenue ?: []
    ], JSON_UNESCAPED_UNICODE);
}

// LẤY TẤT CẢ DỮ LIỆU THỐNG KÊ (DASHBOARD)
else if ($action == 'dashboard' || $action == 'getdashboard') {
    $year = isset($data['year']) ? (int)$data['year'] : (int)date('Y');
    $limit = isset($data['limit']) ? (int)$data['limit'] : 5;
    
    // Lấy tất cả dữ liệu
    $overview = $model->getOverview();
    $monthlyRevenue = $model->getRevenueByMonth($year);
    $topProducts = $model->getTopProducts($limit);
    $topCustomers = $model->getTopCustomers($limit);
    $ordersByStatus = $model->getOrdersByStatus();
    $revenueByCategory = $model->getRevenueByCategory();
    
    echo json_encode([
        "status" => true,
        "message" => "Lấy dữ liệu dashboard thành công",
        "data" => [
            "overview" => $overview,
            "monthly_revenue" => $monthlyRevenue ?: [],
            "top_products" => $topProducts ?: [],
            "top_customers" => $topCustomers ?: [],
            "orders_by_status" => $ordersByStatus ?: [],
            "revenue_by_category" => $revenueByCategory ?: []
        ],
        "year" => $year
    ], JSON_UNESCAPED_UNICODE);
}

// SO SÁNH DOANH THU 2 KHOẢNG THỜI GIAN
else if ($action == 'compare' || $action == 'comparereference') {
    $period1_start = isset($data['period1_start']) ? $data['period1_start'] : '';
    $period1_end = isset($data['period1_end']) ? $data['period1_end'] : '';
    $period2_start = isset($data['period2_start']) ? $data['period2_start'] : '';
    $period2_end = isset($data['period2_end']) ? $data['period2_end'] : '';
    
    if (empty($period1_start) || empty($period1_end) || empty($period2_start) || empty($period2_end)) {
        echo json_encode([
            "status" => false,
            "message" => "Vui lòng cung cấp đầy đủ thời gian cho 2 khoảng"
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $revenue1 = $model->getRevenueByPeriod($period1_start, $period1_end);
    $revenue2 = $model->getRevenueByPeriod($period2_start, $period2_end);
    
    // Tính tổng
    $total1 = 0;
    $total2 = 0;
    if ($revenue1) foreach ($revenue1 as $item) $total1 += $item['doanhthu'];
    if ($revenue2) foreach ($revenue2 as $item) $total2 += $item['doanhthu'];
    
    // Tính % thay đổi
    $change = 0;
    if ($total1 > 0) {
        $change = (($total2 - $total1) / $total1) * 100;
    }
    
    echo json_encode([
        "status" => true,
        "message" => "So sánh doanh thu thành công",
        "data" => [
            "period1" => [
                "start" => $period1_start,
                "end" => $period1_end,
                "revenue" => $total1,
                "details" => $revenue1 ?: []
            ],
            "period2" => [
                "start" => $period2_start,
                "end" => $period2_end,
                "revenue" => $total2,
                "details" => $revenue2 ?: []
            ],
            "change_percent" => round($change, 2),
            "change_amount" => $total2 - $total1
        ]
    ], JSON_UNESCAPED_UNICODE);
}

// ACTION KHÔNG HỢP LỆ
else {
    echo json_encode([
        "status" => false,
        "message" => "Action không hợp lệ. Các action hỗ trợ: overview, revenue_period, revenue_monthly, top_products, top_customers, orders_by_status, revenue_by_category, dashboard, compare"
    ], JSON_UNESCAPED_UNICODE);
}
