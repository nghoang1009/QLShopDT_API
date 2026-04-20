<?php
/**
 * VanChuyenController - Controller quản lý vận chuyển
 */

class VanChuyenController extends Controller {
    
    private $vanChuyenModel;
    private $donHangModel;
    private $khachHangModel;
    
    public function __construct() {
        parent::__construct();
        require_once BASE_PATH . '/model/VanChuyen.php';
        require_once BASE_PATH . '/model/DonHang.php';
        require_once BASE_PATH . '/model/KhachHang.php';
        
        $this->vanChuyenModel = new VanChuyen();
        $this->donHangModel = new DonHang();
        $this->khachHangModel = new KhachHang();
    }
    
    /**
     * Danh sách vận chuyển
     */
    public function index() {
        $this->requireLogin();
        
        $role = $_SESSION['role'] ?? 0;
        
        // Chỉ admin/nhân viên mới xem được
        if ($role == 0) {
            $this->error403('Bạn không có quyền truy cập trang này');
            return;
        }
        
        $shippings = $this->vanChuyenModel->getAllWithDetails();
        
        $this->view('vanchuyen/index', [
            'page_title' => 'Quản lý Vận chuyển',
            'active_nav' => 'vanchuyen',
            'shippings' => $shippings,
            'role' => $role,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }
    
    /**
     * Chi tiết vận chuyển
     */
    public function show($mavc) {
        $this->requireRole([1, 2]);
        
        $shipping = $this->vanChuyenModel->getOneWithDetails($mavc);
        
        if (!$shipping) {
            $this->setFlash('error', 'Không tìm thấy thông tin vận chuyển');
            $this->redirect('/vanchuyen');
            return;
        }
        
        // Lấy chi tiết đơn hàng
        $orderDetails = $this->donHangModel->getOrderDetails($shipping['madh']);
        
        $this->view('vanchuyen/detail', [
            'page_title' => 'Chi tiết Vận chuyển #' . $mavc,
            'active_nav' => 'vanchuyen',
            'shipping' => $shipping,
            'orderDetails' => $orderDetails
        ]);
    }
    
    /**
     * Form thêm vận chuyển
     */
    public function create() {
        $this->requireRole([1, 2]);
        
        // Lấy các đơn hàng đã xác nhận nhưng chưa có vận chuyển
        $orders = $this->donHangModel->getAllWithDetails();
        $confirmedOrders = array_filter($orders, function($order) {
            return in_array($order['trangthai'], ['Đã xác nhận', 'Đang giao']);
        });
        
        $customers = $this->khachHangModel->getAll();
        
        $this->view('vanchuyen/create', [
            'page_title' => 'Thêm Vận chuyển',
            'active_nav' => 'vanchuyen',
            'orders' => $confirmedOrders,
            'customers' => $customers
        ]);
    }
    
    /**
     * Xử lý thêm vận chuyển
     */
    public function store() {
        $this->requireRole([1, 2]);
        $this->verifyCsrf();
        
        $madh = $this->input('madh');
        $makh = $this->input('makh');
        $ngaygiao = $this->input('ngaygiao');
        
        // Validate
        if (empty($madh) || empty($makh) || empty($ngaygiao)) {
            $this->setFlash('error', 'Vui lòng nhập đầy đủ thông tin');
            $this->redirect('/vanchuyen/add');
            return;
        }
        
        // Kiểm tra đơn hàng tồn tại
        $order = $this->donHangModel->getOneWithDetails($madh);
        if (!$order) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng');
            $this->redirect('/vanchuyen/add');
            return;
        }
        
        $result = $this->vanChuyenModel->add($madh, $makh, $ngaygiao);
        
        if ($result) {
            // Cập nhật trạng thái đơn hàng
            $this->donHangModel->updateStatus($madh, 'Đang giao');
            
            $this->setFlash('success', 'Thêm vận chuyển thành công');
        } else {
            $this->setFlash('error', 'Thêm vận chuyển thất bại');
        }
        
        $this->redirect('/vanchuyen');
    }
    
    /**
     * Form chỉnh sửa vận chuyển
     */
    public function edit($mavc) {
        $this->requireRole([1, 2]);
        
        $shipping = $this->vanChuyenModel->getOneWithDetails($mavc);
        
        if (!$shipping) {
            $this->setFlash('error', 'Không tìm thấy thông tin vận chuyển');
            $this->redirect('/vanchuyen');
            return;
        }
        
        $orders = $this->donHangModel->getAllWithDetails();
        $customers = $this->khachHangModel->getAll();
        
        $this->view('vanchuyen/edit', [
            'page_title' => 'Sửa Vận chuyển #' . $mavc,
            'active_nav' => 'vanchuyen',
            'shipping' => $shipping,
            'orders' => $orders,
            'customers' => $customers
        ]);
    }
    
    /**
     * Xử lý cập nhật vận chuyển
     */
    public function update() {
        $this->requireRole([1, 2]);
        $this->verifyCsrf();
        
        $mavc = $this->input('mavc');
        $madh = $this->input('madh');
        $makh = $this->input('makh');
        $ngaygiao = $this->input('ngaygiao');
        
        $shipping = $this->vanChuyenModel->getOneWithDetails($mavc);
        
        if (!$shipping) {
            $this->setFlash('error', 'Không tìm thấy thông tin vận chuyển');
            $this->redirect('/vanchuyen');
            return;
        }
        
        $result = $this->vanChuyenModel->updateShipping($mavc, $madh, $makh, $ngaygiao);
        
        if ($result) {
            $this->setFlash('success', 'Cập nhật vận chuyển thành công');
        } else {
            $this->setFlash('error', 'Cập nhật vận chuyển thất bại');
        }
        
        $this->redirect('/vanchuyen');
    }
    
    /**
     * Xóa vận chuyển
     */
    public function delete($mavc) {
        $this->requireRole([1, 2]);
        
        $shipping = $this->vanChuyenModel->getOneWithDetails($mavc);
        
        if (!$shipping) {
            $this->setFlash('error', 'Không tìm thấy thông tin vận chuyển');
            $this->redirect('/vanchuyen');
            return;
        }
        
        $result = $this->vanChuyenModel->deleteShipping($mavc);
        
        if ($result) {
            $this->setFlash('success', 'Xóa vận chuyển thành công');
        } else {
            $this->setFlash('error', 'Xóa vận chuyển thất bại');
        }
        
        $this->redirect('/vanchuyen');
    }
    
    /**
     * Xác nhận đã giao hàng
     */
    public function confirm($mavc) {
        $this->requireRole([1, 2]);
        
        $shipping = $this->vanChuyenModel->getOneWithDetails($mavc);
        
        if (!$shipping) {
            $this->setFlash('error', 'Không tìm thấy thông tin vận chuyển');
            $this->redirect('/vanchuyen');
            return;
        }
        
        // Cập nhật trạng thái đơn hàng thành đã giao
        $result = $this->donHangModel->updateStatus($shipping['madh'], 'Đã giao');
        
        if ($result) {
            $this->setFlash('success', 'Xác nhận giao hàng thành công');
        } else {
            $this->setFlash('error', 'Xác nhận giao hàng thất bại');
        }
        
        $this->redirect('/vanchuyen');
    }
}
