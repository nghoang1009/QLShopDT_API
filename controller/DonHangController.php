<?php
/**
 * DonHangController - Controller quản lý đơn hàng
 */

class DonHangController extends Controller {
    
    private $donHangModel;
    private $khachHangModel;
    private $nhanVienModel;
    private $sanPhamModel;
    private $gioHangModel;
    
    public function __construct() {
        parent::__construct();
        require_once BASE_PATH . '/model/DonHang.php';
        require_once BASE_PATH . '/model/KhachHang.php';
        require_once BASE_PATH . '/model/NhanVien.php';
        require_once BASE_PATH . '/model/SanPham.php';
        require_once BASE_PATH . '/model/GioHang.php';
        
        $this->donHangModel = new DonHang();
        $this->khachHangModel = new KhachHang();
        $this->nhanVienModel = new NhanVien();
        $this->sanPhamModel = new SanPham();
        $this->gioHangModel = new GioHang();
    }
    
    /**
     * Danh sách đơn hàng
     */
    public function index() {
        $this->requireLogin();
        
        $role = $_SESSION['role'] ?? 0;
        $userid = $_SESSION['userid'] ?? 0;
        
        // Admin/Nhân viên xem tất cả, khách hàng chỉ xem của mình
        if ($role == 0) {
            $orders = $this->donHangModel->getByCustomer($userid);
        } else {
            $orders = $this->donHangModel->getAllWithDetails();
        }
        
        $canEdit = in_array($role, [1, 2]);
        
        $this->view('donhang/index', [
            'page_title' => 'Quản lý Đơn hàng',
            'active_nav' => 'donhang',
            'orders' => $orders,
            'role' => $role,
            'canEdit' => $canEdit,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ]);
    }
    
    /**
     * Chi tiết đơn hàng
     */
    public function show($madh) {
        $this->requireLogin();
        
        $role = $_SESSION['role'] ?? 0;
        $userid = $_SESSION['userid'] ?? 0;
        
        $order = $this->donHangModel->getOneWithDetails($madh);
        
        if (!$order) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng');
            $this->redirect('/donhang');
            return;
        }
        
        // Khách hàng chỉ xem được đơn của mình
        if ($role == 0 && $order['makh'] != $userid) {
            $this->error403('Bạn không có quyền xem đơn hàng này');
            return;
        }
        
        // Lấy chi tiết sản phẩm trong đơn
        $orderDetails = $this->donHangModel->getOrderDetails($madh);
        
        $this->view('donhang/detail', [
            'page_title' => 'Chi tiết Đơn hàng #' . $madh,
            'active_nav' => 'donhang',
            'order' => $order,
            'orderDetails' => $orderDetails,
            'role' => $role
        ]);
    }
    
    /**
     * Form tạo đơn hàng mới (từ giỏ hàng)
     */
    public function create() {
        $this->requireLogin();
        
        $userid = $_SESSION['userid'] ?? 0;
        
        // Lấy giỏ hàng
        $cartItems = $this->gioHangModel->getByCustomer($userid);
        
        if (empty($cartItems)) {
            $this->setFlash('error', 'Giỏ hàng trống, không thể tạo đơn hàng');
            $this->redirect('/giohang');
            return;
        }
        
        // Tính tổng tiền
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['soluong'] * $item['gia'];
        }
        
        // Lấy thông tin khách hàng
        $customer = $this->khachHangModel->findById($userid);
        
        $this->view('donhang/create', [
            'page_title' => 'Đặt hàng',
            'active_nav' => 'giohang',
            'cartItems' => $cartItems,
            'total' => $total,
            'customer' => $customer
        ]);
    }
    
    /**
     * Xử lý tạo đơn hàng
     */
    public function store() {
        $this->requireLogin();
        $this->verifyCsrf();
        
        $userid = $_SESSION['userid'] ?? 0;
        $role = $_SESSION['role'] ?? 0;
        
        // Lấy giỏ hàng
        $cartItems = $this->gioHangModel->getByCustomer($userid);
        
        if (empty($cartItems)) {
            $this->setFlash('error', 'Giỏ hàng trống');
            $this->redirect('/giohang');
            return;
        }
        
        // Tính tổng tiền
        $total = 0;
        foreach ($cartItems as $item) {
            $total += $item['soluong'] * $item['gia'];
        }
        
        // Tạo đơn hàng
        $manv = ($role != 0) ? $userid : null;
        $madh = $this->donHangModel->createOrder($userid, $total, $manv);
        
        if (!$madh) {
            $this->setFlash('error', 'Tạo đơn hàng thất bại');
            $this->redirect('/giohang');
            return;
        }
        
        // Thêm chi tiết đơn hàng
        foreach ($cartItems as $item) {
            $this->donHangModel->addOrderDetail($madh, $item['masp'], $item['soluong'], $item['gia']);
            
            // Giảm số lượng tồn kho
            $this->sanPhamModel->updateStock($item['masp'], -$item['soluong']);
        }
        
        // Xóa giỏ hàng
        $this->gioHangModel->clearCart($userid);
        
        $this->setFlash('success', 'Đặt hàng thành công! Mã đơn hàng: #' . $madh);
        $this->redirect('/donhang/detail/' . $madh);
    }
    
    /**
     * Form chỉnh sửa đơn hàng (chỉ admin/nhân viên)
     */
    public function edit($madh) {
        $this->requireRole([1, 2]);
        
        $order = $this->donHangModel->getOneWithDetails($madh);
        
        if (!$order) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng');
            $this->redirect('/donhang');
            return;
        }
        
        $orderDetails = $this->donHangModel->getOrderDetails($madh);
        $customers = $this->khachHangModel->getAll();
        $employees = $this->nhanVienModel->getAll();
        
        // Trạng thái có thể chọn
        $statuses = ['Chờ xác nhận', 'Đã xác nhận', 'Đang giao', 'Đã giao', 'Đã hủy'];
        
        $this->view('donhang/edit', [
            'page_title' => 'Sửa Đơn hàng #' . $madh,
            'active_nav' => 'donhang',
            'order' => $order,
            'orderDetails' => $orderDetails,
            'customers' => $customers,
            'employees' => $employees,
            'statuses' => $statuses
        ]);
    }
    
    /**
     * Xử lý cập nhật đơn hàng
     */
    public function update() {
        $this->requireRole([1, 2]);
        $this->verifyCsrf();
        
        $madh = $this->input('madh');
        $trangthai = $this->input('trangthai');
        $manv = $this->input('manv');
        
        $order = $this->donHangModel->getOneWithDetails($madh);
        
        if (!$order) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng');
            $this->redirect('/donhang');
            return;
        }
        
        // Cập nhật trạng thái
        $result = $this->donHangModel->updateStatus($madh, $trangthai);
        
        // Cập nhật nhân viên xử lý nếu có
        if ($manv) {
            $this->db->execute("UPDATE donhang SET manv = ? WHERE madh = ?", 'ii', [$manv, $madh]);
        }
        
        if ($result) {
            $this->setFlash('success', 'Cập nhật đơn hàng thành công');
        } else {
            $this->setFlash('error', 'Cập nhật đơn hàng thất bại');
        }
        
        $this->redirect('/donhang');
    }
    
    /**
     * Cập nhật nhanh trạng thái (AJAX)
     */
    public function updateStatus() {
        $this->requireRole([1, 2]);
        
        $madh = $this->input('madh');
        $trangthai = $this->input('trangthai');
        
        if (!$madh || !$trangthai) {
            $this->json(['success' => false, 'message' => 'Thiếu thông tin'], 400);
            return;
        }
        
        $result = $this->donHangModel->updateStatus($madh, $trangthai);
        
        if ($result) {
            $this->json(['success' => true, 'message' => 'Cập nhật thành công']);
        } else {
            $this->json(['success' => false, 'message' => 'Cập nhật thất bại'], 500);
        }
    }
    
    /**
     * Xóa đơn hàng
     */
    public function delete($madh) {
        $this->requireRole([1, 2]);
        
        $order = $this->donHangModel->getOneWithDetails($madh);
        
        if (!$order) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng');
            $this->redirect('/donhang');
            return;
        }
        
        // Chỉ xóa được đơn chưa giao
        if ($order['trangthai'] == 'Đã giao') {
            $this->setFlash('error', 'Không thể xóa đơn hàng đã giao');
            $this->redirect('/donhang');
            return;
        }
        
        // Hoàn lại số lượng tồn kho nếu đơn bị hủy
        if ($order['trangthai'] != 'Đã hủy') {
            $orderDetails = $this->donHangModel->getOrderDetails($madh);
            foreach ($orderDetails as $item) {
                $this->sanPhamModel->updateStock($item['masp'], $item['soluong']);
            }
        }
        
        $result = $this->donHangModel->deleteOrder($madh);
        
        if ($result) {
            $this->setFlash('success', 'Xóa đơn hàng thành công');
        } else {
            $this->setFlash('error', 'Xóa đơn hàng thất bại');
        }
        
        $this->redirect('/donhang');
    }
    
    /**
     * Hủy đơn hàng (khách hàng)
     */
    public function cancel($madh) {
        $this->requireLogin();
        
        $role = $_SESSION['role'] ?? 0;
        $userid = $_SESSION['userid'] ?? 0;
        
        $order = $this->donHangModel->getOneWithDetails($madh);
        
        if (!$order) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng');
            $this->redirect('/donhang');
            return;
        }
        
        // Kiểm tra quyền
        if ($role == 0 && $order['makh'] != $userid) {
            $this->error403('Bạn không có quyền hủy đơn hàng này');
            return;
        }
        
        // Chỉ hủy được đơn chờ xác nhận
        if ($order['trangthai'] != 'Chờ xác nhận') {
            $this->setFlash('error', 'Chỉ có thể hủy đơn hàng đang chờ xác nhận');
            $this->redirect('/donhang/detail/' . $madh);
            return;
        }
        
        // Hoàn lại số lượng tồn kho
        $orderDetails = $this->donHangModel->getOrderDetails($madh);
        foreach ($orderDetails as $item) {
            $this->sanPhamModel->updateStock($item['masp'], $item['soluong']);
        }
        
        // Cập nhật trạng thái
        $result = $this->donHangModel->updateStatus($madh, 'Đã hủy');
        
        if ($result) {
            $this->setFlash('success', 'Hủy đơn hàng thành công');
        } else {
            $this->setFlash('error', 'Hủy đơn hàng thất bại');
        }
        
        $this->redirect('/donhang');
    }

    // ===================== RESTful API Methods =====================

    /**
     * GET /api/donhang
     * ?makh= để lấy đơn của một khách hàng
     */
    public function apiIndex() {
        header('Content-Type: application/json');
        $makh = $_GET['makh'] ?? null;
        if ($makh) {
            $orders = $this->donHangModel->getByCustomer($makh);
        } else {
            $orders = $this->donHangModel->getAllWithDetails();
        }
        echo json_encode(['status' => true, 'data' => $orders ?: []]);
    }

    /**
     * GET /api/donhang/{id}
     * Trả về header + chitiet của đơn hàng
     */
    public function apiShow($id) {
        header('Content-Type: application/json');
        $donhang = $this->donHangModel->getOneWithDetails($id);
        if (!$donhang) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy đơn hàng']);
            return;
        }
        $chitiet = $this->donHangModel->getOrderDetails($id);
        $donhang['chitiet'] = $chitiet ?: [];
        echo json_encode(['status' => true, 'data' => $donhang]);
    }

    /**
     * POST /api/donhang
     * Body: makh, trigia, manv (optional)
     */
    public function apiStore() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $makh  = $input['makh']  ?? null;
        $trigia = $input['trigia'] ?? 0;
        $manv  = $input['manv']  ?? null;
        if (!$makh) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Thiếu makh']);
            return;
        }
        $result = $this->donHangModel->createOrder($makh, $trigia, $manv);
        if ($result) {
            echo json_encode(['status' => true, 'message' => 'Tạo đơn hàng thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Tạo đơn hàng thất bại']);
        }
    }

    /**
     * PUT /api/donhang/{id}
     * Body: trigia (optional), trangthai (optional)
     */
    public function apiUpdate($id) {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $ok = false;
        if (isset($input['trigia'])) {
            $ok = $this->donHangModel->updateTrigia($id, $input['trigia']);
        }
        if (isset($input['trangthai'])) {
            $ok = $this->donHangModel->updateStatus($id, $input['trangthai']);
        }
        if ($ok) {
            echo json_encode(['status' => true, 'message' => 'Cập nhật thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Cập nhật thất bại']);
        }
    }

    /**
     * DELETE /api/donhang/{id}
     */
    public function apiDestroy($id) {
        header('Content-Type: application/json');
        $result = $this->donHangModel->deleteOrder($id);
        if ($result) {
            echo json_encode(['status' => true, 'message' => 'Xóa đơn hàng thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Xóa đơn hàng thất bại']);
        }
    }
}
