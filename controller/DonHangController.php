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
     * GET /donhang/detail/{madh} — Xem chi tiết đơn hàng
     */
    public function show($madh) {
        $this->requireLogin();
        header('Location: ' . BASE_URL . '/views/donhang/donhang_chitiet.php?madh=' . (int)$madh);
        exit();
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
        $updated = false;
        $failed  = false;

        if (isset($input['trigia'])) {
            $r = $this->donHangModel->updateTrigia($id, $input['trigia']);
            if ($r === false) $failed = true;
            else $updated = true;
        }
        if (isset($input['trangthai'])) {
            $r = $this->donHangModel->updateStatus($id, $input['trangthai']);
            if ($r === false) $failed = true;
            else $updated = true;
        }

        if ($failed) {
            http_response_code(500);
            echo json_encode(['status' => false, 'message' => 'Cập nhật thất bại'], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode(['status' => true, 'message' => 'Cập nhật thành công'], JSON_UNESCAPED_UNICODE);
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
