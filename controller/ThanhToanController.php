<?php
/**
 * ThanhToanController - Controller quản lý thanh toán
 */

class ThanhToanController extends Controller {
    
    private $thanhToanModel;
    private $donHangModel;
    
    public function __construct() {
        parent::__construct();
        require_once BASE_PATH . '/model/ThanhToan.php';
        require_once BASE_PATH . '/model/DonHang.php';
        
        $this->thanhToanModel = new ThanhToan();
        $this->donHangModel = new DonHang();
    }
    
    /**
     * Danh sách thanh toán
     */
    public function index() {
        $this->requireLogin();
        
        $role = $_SESSION['role'] ?? 0;
        $userid = $_SESSION['userid'] ?? 0;
        
        // Admin/Nhân viên xem tất cả, khách hàng chỉ xem của mình
        if ($role == 0) {
            $payments = $this->thanhToanModel->getByCustomer($userid);
        } else {
            $payments = $this->thanhToanModel->getAllWithDetails();
        }
        
        $canEdit = in_array($role, [1, 2]);
        
        $this->view('thanhtoan/index', [
            'page_title' => 'Quản lý Thanh toán',
            'active_nav' => 'thanhtoan',
            'payments' => $payments,
            'role' => $role,
            'canEdit' => $canEdit,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }
    
    /**
     * Chi tiết thanh toán
     */
    public function show($matt) {
        $this->requireLogin();
        
        $role = $_SESSION['role'] ?? 0;
        $userid = $_SESSION['userid'] ?? 0;
        
        $payment = $this->thanhToanModel->getOneWithDetails($matt);
        
        if (!$payment) {
            $this->setFlash('error', 'Không tìm thấy thanh toán');
            $this->redirect('/thanhtoan');
            return;
        }
        
        // Lấy thông tin đơn hàng
        $order = $this->donHangModel->getOneWithDetails($payment['madh']);
        $orderDetails = $this->donHangModel->getOrderDetails($payment['madh']);
        
        $this->view('thanhtoan/detail', [
            'page_title' => 'Chi tiết Thanh toán #' . $matt,
            'active_nav' => 'thanhtoan',
            'payment' => $payment,
            'order' => $order,
            'orderDetails' => $orderDetails,
            'role' => $role
        ]);
    }
    
    /**
     * Form thêm thanh toán
     */
    public function create() {
        $this->requireRole([1, 2]);
        
        // Lấy đơn hàng chưa thanh toán
        $unpaidOrders = $this->thanhToanModel->getUnpaidOrders();
        $paymentMethods = $this->thanhToanModel->getPaymentMethods();
        
        $this->view('thanhtoan/create', [
            'page_title' => 'Thêm Thanh toán',
            'active_nav' => 'thanhtoan',
            'unpaidOrders' => $unpaidOrders,
            'paymentMethods' => $paymentMethods
        ]);
    }
    
    /**
     * Xử lý thêm thanh toán
     */
    public function store() {
        $this->requireRole([1, 2]);
        $this->verifyCsrf();
        
        $madh = $this->input('madh');
        $phuongthuc = $this->input('phuongthuc');
        $sotien = $this->input('sotien');
        $trangthai = $this->input('trangthai', 'Chờ xác nhận');
        $ghichu = $this->input('ghichu');
        
        // Validate
        if (empty($madh) || empty($phuongthuc) || empty($sotien)) {
            $this->setFlash('error', 'Vui lòng nhập đầy đủ thông tin');
            $this->redirect('/thanhtoan/add');
            return;
        }
        
        // Kiểm tra đơn hàng tồn tại
        $order = $this->donHangModel->getOneWithDetails($madh);
        if (!$order) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng');
            $this->redirect('/thanhtoan/add');
            return;
        }
        
        // Kiểm tra đã thanh toán chưa
        $existingPayment = $this->thanhToanModel->getByOrder($madh);
        if ($existingPayment && $existingPayment['trangthai'] == 'Đã thanh toán') {
            $this->setFlash('error', 'Đơn hàng này đã được thanh toán');
            $this->redirect('/thanhtoan');
            return;
        }
        
        $data = [
            'madh' => $madh,
            'phuongthuc' => $phuongthuc,
            'sotien' => $sotien,
            'trangthai' => $trangthai,
            'ghichu' => $ghichu
        ];
        
        $result = $this->thanhToanModel->add($data);
        
        if ($result) {
            // Cập nhật trạng thái đơn hàng nếu đã thanh toán
            if ($trangthai == 'Đã thanh toán') {
                $this->donHangModel->updateStatus($madh, 'Đã xác nhận');
            }
            
            $this->setFlash('success', 'Thêm thanh toán thành công');
        } else {
            $this->setFlash('error', 'Thêm thanh toán thất bại');
        }
        
        $this->redirect('/thanhtoan');
    }
    
    /**
     * Form chỉnh sửa thanh toán
     */
    public function edit($matt) {
        $this->requireRole([1, 2]);
        
        $payment = $this->thanhToanModel->getOneWithDetails($matt);
        
        if (!$payment) {
            $this->setFlash('error', 'Không tìm thấy thanh toán');
            $this->redirect('/thanhtoan');
            return;
        }
        
        $paymentMethods = $this->thanhToanModel->getPaymentMethods();
        $paymentStatuses = $this->thanhToanModel->getPaymentStatuses();
        
        $this->view('thanhtoan/edit', [
            'page_title' => 'Sửa Thanh toán #' . $matt,
            'active_nav' => 'thanhtoan',
            'payment' => $payment,
            'paymentMethods' => $paymentMethods,
            'paymentStatuses' => $paymentStatuses
        ]);
    }
    
    /**
     * Xử lý cập nhật thanh toán
     */
    public function update() {
        $this->requireRole([1, 2]);
        $this->verifyCsrf();
        
        $matt = $this->input('matt');
        $phuongthuc = $this->input('phuongthuc');
        $ngaythanhtoan = $this->input('ngaythanhtoan');
        $sotien = $this->input('sotien');
        $trangthai = $this->input('trangthai');
        $ghichu = $this->input('ghichu');
        
        $payment = $this->thanhToanModel->getOneWithDetails($matt);
        
        if (!$payment) {
            $this->setFlash('error', 'Không tìm thấy thanh toán');
            $this->redirect('/thanhtoan');
            return;
        }
        
        $data = [
            'phuongthuc' => $phuongthuc,
            'ngaythanhtoan' => $ngaythanhtoan,
            'sotien' => $sotien,
            'trangthai' => $trangthai,
            'ghichu' => $ghichu
        ];
        
        $result = $this->thanhToanModel->updatePayment($matt, $data);
        
        if ($result) {
            // Cập nhật trạng thái đơn hàng nếu đã thanh toán
            if ($trangthai == 'Đã thanh toán') {
                $this->donHangModel->updateStatus($payment['madh'], 'Đã xác nhận');
            }
            
            $this->setFlash('success', 'Cập nhật thanh toán thành công');
        } else {
            $this->setFlash('error', 'Cập nhật thanh toán thất bại');
        }
        
        $this->redirect('/thanhtoan');
    }
    
    /**
     * Xóa thanh toán
     */
    public function delete($matt) {
        $this->requireRole([1, 2]);
        
        $payment = $this->thanhToanModel->getOneWithDetails($matt);
        
        if (!$payment) {
            $this->setFlash('error', 'Không tìm thấy thanh toán');
            $this->redirect('/thanhtoan');
            return;
        }
        
        $result = $this->thanhToanModel->deletePayment($matt);
        
        if ($result) {
            $this->setFlash('success', 'Xóa thanh toán thành công');
        } else {
            $this->setFlash('error', 'Xóa thanh toán thất bại');
        }
        
        $this->redirect('/thanhtoan');
    }
    
    /**
     * Thanh toán nhanh từ đơn hàng
     */
    public function quickPay($madh) {
        $this->requireLogin();
        
        $role = $_SESSION['role'] ?? 0;
        $userid = $_SESSION['userid'] ?? 0;
        
        $order = $this->donHangModel->getOneWithDetails($madh);
        
        if (!$order) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng');
            $this->redirect('/donhang');
            return;
        }
        
        // Kiểm tra quyền (khách hàng chỉ thanh toán đơn của mình)
        if ($role == 0 && $order['makh'] != $userid) {
            $this->error403('Bạn không có quyền thanh toán đơn hàng này');
            return;
        }
        
        // Kiểm tra đã thanh toán chưa
        $existingPayment = $this->thanhToanModel->getByOrder($madh);
        if ($existingPayment && $existingPayment['trangthai'] == 'Đã thanh toán') {
            $this->setFlash('error', 'Đơn hàng này đã được thanh toán');
            $this->redirect('/donhang/detail/' . $madh);
            return;
        }
        
        $orderDetails = $this->donHangModel->getOrderDetails($madh);
        $paymentMethods = $this->thanhToanModel->getPaymentMethods();
        
        $this->view('thanhtoan/quick_pay', [
            'page_title' => 'Thanh toán đơn hàng #' . $madh,
            'active_nav' => 'donhang',
            'order' => $order,
            'orderDetails' => $orderDetails,
            'paymentMethods' => $paymentMethods
        ]);
    }
    
    /**
     * Xử lý thanh toán nhanh
     */
    public function processQuickPay() {
        $this->requireLogin();
        $this->verifyCsrf();
        
        $role = $_SESSION['role'] ?? 0;
        $userid = $_SESSION['userid'] ?? 0;
        
        $madh = $this->input('madh');
        $phuongthuc = $this->input('phuongthuc');
        
        $order = $this->donHangModel->getOneWithDetails($madh);
        
        if (!$order) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng');
            $this->redirect('/donhang');
            return;
        }
        
        // Kiểm tra quyền
        if ($role == 0 && $order['makh'] != $userid) {
            $this->error403('Bạn không có quyền thanh toán đơn hàng này');
            return;
        }
        
        $data = [
            'madh' => $madh,
            'phuongthuc' => $phuongthuc,
            'sotien' => $order['trigia'],
            'trangthai' => 'Đã thanh toán',
            'ghichu' => 'Thanh toán online'
        ];
        
        $result = $this->thanhToanModel->add($data);
        
        if ($result) {
            // Cập nhật trạng thái đơn hàng
            $this->donHangModel->updateStatus($madh, 'Đã xác nhận');
            
            $this->setFlash('success', 'Thanh toán thành công!');
            $this->redirect('/donhang/detail/' . $madh);
        } else {
            $this->setFlash('error', 'Thanh toán thất bại');
            $this->redirect('/thanhtoan/quick-pay/' . $madh);
        }
    }
}
