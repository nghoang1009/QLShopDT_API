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
    
    // ===================== Web Methods =====================

    /**
     * GET /donhang/create — Trang xác nhận đặt hàng (chỉ khách hàng role=0)
     */
    public function create() {
        $this->requireLogin();

        $role     = (int)($_SESSION['role']     ?? -1);
        $username = $_SESSION['username']        ?? '';
        $userid   = (int)($_SESSION['userid']   ?? 0);

        if ($role !== 0) {
            $this->error403('Chỉ khách hàng mới có thể đặt hàng qua giỏ hàng');
            return;
        }

        $makh  = $this->gioHangModel->findCustomerByUsername($username);
        $items = $makh ? $this->gioHangModel->getByCustomer($makh) : [];

        if (empty($items)) {
            $this->setFlash('error', 'Giỏ hàng trống, không thể đặt hàng');
            $this->redirect('/giohang');
            return;
        }

        $total    = array_sum(array_column($items, 'thanhtien'));
        $customer = $this->khachHangModel->findById($makh);

        $this->view('donhang/checkout', [
            'page_title' => 'Xác nhận đặt hàng',
            'active_nav' => 'giohang',
            'items'      => $items,
            'total'      => $total,
            'customer'   => $customer,
            'makh'       => $makh,
        ]);
    }

    /**
     * POST /donhang/create — Xử lý đặt hàng từ giỏ hàng
     */
    public function placeOrder() {
        $this->requireLogin();

        $role     = (int)($_SESSION['role']   ?? -1);
        $username = $_SESSION['username']      ?? '';

        if ($role !== 0) {
            $this->error403('Chỉ khách hàng mới có thể đặt hàng qua giỏ hàng');
            return;
        }

        $makh  = $this->gioHangModel->findCustomerByUsername($username);
        $items = $makh ? $this->gioHangModel->getByCustomer($makh) : [];

        if (empty($items)) {
            $this->setFlash('error', 'Giỏ hàng trống');
            $this->redirect('/giohang');
            return;
        }

        $total = array_sum(array_column($items, 'thanhtien'));
        $madh  = $this->donHangModel->createOrder($makh, $total);

        if (!$madh) {
            $this->setFlash('error', 'Tạo đơn hàng thất bại, vui lòng thử lại');
            $this->redirect('/donhang/create');
            return;
        }

        // Thêm chi tiết từng sản phẩm
        foreach ($items as $item) {
            $this->donHangModel->addOrderDetail($madh, (int)$item['masp'], (int)$item['sl']);
        }

        // Xóa giỏ hàng
        $magio = $this->gioHangModel->getCartId($makh);
        if ($magio) {
            $this->gioHangModel->clearCart($magio);
        }

        $this->setFlash('success', 'Đặt hàng thành công! Mã đơn hàng: #' . $madh);
        $this->redirect('/giohang');
    }

    /**
     * POST /donhang/{madh}/cancel — Khách hàng hủy đơn hàng của mình
     * Chỉ được hủy khi trạng thái là "Chờ xác nhận"
     */
    public function cancel($madh) {
        $this->requireLogin();

        $role     = (int)($_SESSION['role']   ?? -1);
        $username = $_SESSION['username']      ?? '';

        if ($role !== 0) {
            $this->error403('Chỉ khách hàng mới có thể hủy đơn hàng');
            return;
        }

        $madh = (int)$madh;
        $order = $this->donHangModel->getOneWithDetails($madh);

        $donhangUrl = BASE_URL . '/views/donhang/donhang.php';

        if (!$order) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng #' . $madh);
            header('Location: ' . $donhangUrl);
            exit();
        }

        // Kiểm tra đơn hàng có thuộc về khách hàng này không
        $makh = $this->gioHangModel->findCustomerByUsername($username);
        if (!$makh || (int)$order['makh'] !== (int)$makh) {
            $this->error403('Bạn không có quyền hủy đơn hàng này');
            return;
        }

        // Kiểm tra trạng thái
        if ($order['trangthai'] !== 'Chờ xác nhận') {
            $this->setFlash('error', 'Chỉ có thể hủy đơn hàng ở trạng thái "Chờ xác nhận"');
            header('Location: ' . $donhangUrl);
            exit();
        }

        $result = $this->donHangModel->cancelOrder($madh);
        if ($result) {
            $this->setFlash('success', 'Đã hủy đơn hàng #' . $madh . ' thành công');
        } else {
            $this->setFlash('error', 'Hủy đơn hàng thất bại, vui lòng thử lại');
        }
        header('Location: ' . $donhangUrl);
        exit();
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

    // ===================== WEB View Methods =====================

    /**
     * GET /donhang
     * Danh sách đơn hàng
     */
    public function index() {
        $this->requireLogin();

        $role = $_SESSION['role'] ?? 0;
        $userid = $_SESSION['userid'] ?? 0;

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
     * GET /donhang/{id}
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

        if ($role == 0 && $order['makh'] != $userid) {
            $this->error403('Bạn không có quyền xem đơn hàng này');
            return;
        }

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
     * GET /donhang/create
     * Form tạo đơn hàng mới
     * Khách hàng: xác nhận giỏ hàng → đặt hàng
     * Admin/Staff: chọn khách hàng → nhập tiền
     */
    public function create() {
        $this->requireLogin();
        
        $role = (int)($_SESSION['role'] ?? 0);
        $userid = $_SESSION['userid'] ?? 0;

        if ($role == 0) {
            // Khách hàng: lấy giỏ hàng của mình
            $cartItems = $this->gioHangModel->getByCustomer($userid);
            $customer = $this->khachHangModel->findById($userid);
            
            $total = 0;
            foreach ($cartItems as $item) {
                $total += $item['sl'] * $item['gia'];
            }

            $this->view('donhang/donhang_create', [
                'page_title' => 'Đặt hàng',
                'active_nav' => 'donhang',
                'role' => $role,
                'cartItems' => $cartItems,
                'total' => $total,
                'customer' => $customer
            ]);
        } else {
            // Admin/Staff: danh sách khách hàng
            $this->requireRole([1, 2]);
            
            $customers = $this->khachHangModel->getAll();

            $this->view('donhang/donhang_create', [
                'page_title' => 'Tạo đơn hàng',
                'active_nav' => 'donhang',
                'role' => $role,
                'customers' => $customers
            ]);
        }
    }

    /**
     * POST /donhang
     * Xử lý tạo đơn hàng
     * Khách hàng (role 0): tạo từ giỏ hàng
     * Admin/Staff (role 1,2): tạo từ form
     */
    public function store() {
        $this->requireLogin();
        $this->verifyCsrf();

        $role = (int)($_SESSION['role'] ?? 0);
        $userid = $_SESSION['userid'] ?? 0;

        if ($role == 0) {
            // ========== KHÁCH HÀNG: Đặt hàng từ giỏ ==========
            $makh = $userid;
            
            // Lấy giỏ hàng
            $cartItems = $this->gioHangModel->getByCustomer($makh);
            
            if (empty($cartItems)) {
                $this->setFlash('error', 'Giỏ hàng trống, không thể tạo đơn hàng');
                $this->redirect('/giohang');
                return;
            }
            
            // Tính tổng tiền từ giỏ
            $trigia = 0;
            foreach ($cartItems as $item) {
                $trigia += $item['sl'] * $item['gia'];
            }
            
            // Tạo đơn hàng
            $madh = $this->donHangModel->createOrder($makh, $trigia, null);
            
            if (!$madh) {
                $this->setFlash('error', 'Tạo đơn hàng thất bại');
                $this->redirect('/giohang');
                return;
            }
            
            // Thêm chi tiết sản phẩm
            foreach ($cartItems as $item) {
                $this->donHangModel->addOrderDetail($madh, $item['masp'], $item['sl'], $item['gia']);
                // Giảm tồn kho
                $this->sanPhamModel->updateStock($item['masp'], -$item['sl']);
            }
            
            // Xóa giỏ hàng
            $magio = $this->gioHangModel->getCartId($makh);
            if ($magio) {
                $this->gioHangModel->clearCart($magio);
            }
            
            $this->setFlash('success', 'Đặt hàng thành công! Mã đơn hàng: #' . $madh);
            $this->redirect('/donhang/' . $madh);

        } else {
            // ========== ADMIN/STAFF: Tạo đơn từ form ==========
            $this->requireRole([1, 2]);
            
            $makh = (int)($this->input('makh') ?? 0);
            $trigia = (float)($this->input('trigia') ?? 0);
            
            if ($makh <= 0 || $trigia < 0) {
                $this->setFlash('error', 'Vui lòng nhập đầy đủ thông tin hợp lệ');
                $this->redirect('/donhang/create');
                return;
            }
            
            $madh = $this->donHangModel->createOrder($makh, $trigia, $userid);
            
            if ($madh) {
                $this->setFlash('success', 'Tạo đơn hàng thành công (Mã: #' . $madh . ')');
                $this->redirect('/donhang/' . $madh);
            } else {
                $this->setFlash('error', 'Tạo đơn hàng thất bại');
                $this->redirect('/donhang/create');
            }
        }
    }

    /**
     * GET /donhang/{id}/edit
     * Form chỉnh sửa đơn hàng
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
     * POST /donhang/{id}
     * Xử lý cập nhật đơn hàng
     */
    public function update($madh = null) {
        $this->requireRole([1, 2]);
        $this->verifyCsrf();

        if (!$madh) {
            $madh = (int)($this->input('madh') ?? 0);
        }

        if ($madh <= 0) {
            $this->setFlash('error', 'Mã đơn hàng không hợp lệ');
            $this->redirect('/donhang');
            return;
        }

        $trangthai = $this->input('trangthai');
        $manv = $this->input('manv');

        $order = $this->donHangModel->getOneWithDetails($madh);

        if (!$order) {
            $this->setFlash('error', 'Không tìm thấy đơn hàng');
            $this->redirect('/donhang');
            return;
        }

        if (!empty($trangthai)) {
            $this->donHangModel->updateStatus($madh, $trangthai);
        }

        if (!empty($manv)) {
            $this->db->execute("UPDATE donhang SET manv = ? WHERE madh = ?", 'ii', [$manv, $madh]);
        }

        $this->setFlash('success', 'Cập nhật đơn hàng thành công');
        $this->redirect('/donhang/' . $madh);
    }

    /**
     * GET /donhang/{id}/delete
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

        if ($order['trangthai'] == 'Đã giao') {
            $this->setFlash('error', 'Không thể xóa đơn hàng đã giao');
            $this->redirect('/donhang');
            return;
        }

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
}
