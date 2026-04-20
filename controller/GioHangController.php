<?php
/**
 * GioHangController - Controller quản lý giỏ hàng
 */

class GioHangController extends Controller {
    
    private $gioHangModel;
    
    public function __construct() {
        parent::__construct();
        require_once BASE_PATH . '/model/GioHang.php';
        require_once BASE_PATH . '/model/SanPham.php';
        $this->gioHangModel = new GioHang();
    }

    /**
     * Trang giỏ hàng (web view)
     */
    public function index() {
        $this->requireLogin();

        $role = $_SESSION['role'] ?? 0;
        $username = $_SESSION['username'];

        if ((int)$role === 0) {
            $makh = $this->gioHangModel->findCustomerByUsername($username);

            if (!$makh) {
                $this->view('giohang/index', [
                    'error' => 'Không tìm thấy thông tin khách hàng',
                    'page_title' => 'Giỏ hàng',
                    'active_nav' => 'giohang',
                    'items' => [],
                    'total' => 0,
                    'role' => (int)$role,
                ]);
                return;
            }

            $items = $this->gioHangModel->getByCustomer($makh);
            $total = $this->gioHangModel->getCartTotal($makh);
        } else {
            $items = $this->gioHangModel->getAllItemsWithCustomer();
            $total = array_sum(array_column($items ?: [], 'thanhtien'));
        }

        $this->view('giohang/index', [
            'items' => $items,
            'total' => $total,
            'role' => (int)$role,
            'page_title' => 'Giỏ hàng',
            'active_nav' => 'giohang',
        ]);
    }
    
    // ===================== RESTful API Methods =====================

    /**
     * GET /api/giohang
     * Role 0: giỏ hàng của chính khách hàng
     * Role 1/2: tất cả giỏ hàng
     */
    public function apiGet() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['username'])) {
            http_response_code(401);
            echo json_encode(['status' => false, 'message' => 'Chưa đăng nhập'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $role = (int)($_SESSION['role'] ?? 0);

        if ($role === 0) {
            $makh = $this->gioHangModel->findCustomerByUsername($_SESSION['username']);
            if (!$makh) {
                echo json_encode(['status' => true, 'data' => [], 'total' => 0], JSON_UNESCAPED_UNICODE);
                return;
            }
            $items = $this->gioHangModel->getByCustomer($makh);
            $total = $this->gioHangModel->getCartTotal($makh);
        } else {
            $items = $this->gioHangModel->getAllItems();
            $total = array_sum(array_column($items ?: [], 'thanhtien'));
        }

        echo json_encode([
            'status' => true,
            'data'   => $items ?: [],
            'total'  => $total,
        ], JSON_UNESCAPED_UNICODE);
    }

    /**
     * POST /api/giohang
     * Body: masp, sl
     * Chỉ dành cho khách hàng (role 0)
     */
    public function apiAdd() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['username']) || (int)($_SESSION['role'] ?? -1) !== 0) {
            http_response_code(403);
            echo json_encode(['status' => false, 'message' => 'Chỉ khách hàng mới có thể thêm vào giỏ'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
        $masp  = (int)($input['masp'] ?? 0);
        $sl    = (int)($input['sl']   ?? 1);

        if ($masp <= 0) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Mã sản phẩm không hợp lệ'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $makh = $this->gioHangModel->findCustomerByUsername($_SESSION['username']);
        if (!$makh) {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy thông tin khách hàng'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $result = $this->gioHangModel->addToCart($makh, $masp, $sl);
        if ($result['success']) {
            echo json_encode(['status' => true, 'message' => $result['message']], JSON_UNESCAPED_UNICODE);
        } else {
            http_response_code(400);
            echo json_encode(['status' => false, 'message' => $result['message']], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * PUT /api/giohang/{masp}
     * Body: sl (số lượng mới; 0 = xóa)
     * Chỉ dành cho khách hàng (role 0)
     */
    public function apiUpdate($masp) {
        header('Content-Type: application/json');
        if (!isset($_SESSION['username']) || (int)($_SESSION['role'] ?? -1) !== 0) {
            http_response_code(403);
            echo json_encode(['status' => false, 'message' => 'Không có quyền'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $input = json_decode(file_get_contents('php://input'), true) ?: [];
        $sl    = (int)($input['sl'] ?? 0);
        $masp  = (int)$masp;

        $makh  = $this->gioHangModel->findCustomerByUsername($_SESSION['username']);
        $magio = $this->gioHangModel->getCartId($makh);

        if (!$magio) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy giỏ hàng'], JSON_UNESCAPED_UNICODE);
            return;
        }

        if ($sl <= 0) {
            $ok  = $this->gioHangModel->removeItem($magio, $masp);
            $msg = 'Đã xóa sản phẩm khỏi giỏ hàng';
        } else {
            $sanPhamModel = new SanPham();
            $stock = $sanPhamModel->getStock($masp);
            if ($sl > $stock) {
                http_response_code(400);
                echo json_encode(['status' => false, 'message' => "Số lượng vượt quá tồn kho (còn $stock)"], JSON_UNESCAPED_UNICODE);
                return;
            }
            $ok  = $this->gioHangModel->updateItemQuantity($magio, $masp, $sl);
            $msg = 'Cập nhật số lượng thành công';
        }

        echo json_encode(['status' => $ok !== false, 'message' => $ok !== false ? $msg : 'Cập nhật thất bại'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * DELETE /api/giohang/{masp}
     * Xóa 1 sản phẩm khỏi giỏ
     */
    public function apiRemove($masp) {
        header('Content-Type: application/json');
        if (!isset($_SESSION['username']) || (int)($_SESSION['role'] ?? -1) !== 0) {
            http_response_code(403);
            echo json_encode(['status' => false, 'message' => 'Không có quyền'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $masp  = (int)$masp;
        $makh  = $this->gioHangModel->findCustomerByUsername($_SESSION['username']);
        $magio = $this->gioHangModel->getCartId($makh);

        if (!$magio) {
            http_response_code(404);
            echo json_encode(['status' => false, 'message' => 'Không tìm thấy giỏ hàng'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $ok = $this->gioHangModel->removeItem($magio, $masp);
        echo json_encode(['status' => $ok !== false, 'message' => $ok !== false ? 'Đã xóa sản phẩm' : 'Xóa thất bại'], JSON_UNESCAPED_UNICODE);
    }

    /**
     * DELETE /api/giohang
     * Xóa toàn bộ giỏ hàng
     */
    public function apiClear() {
        header('Content-Type: application/json');
        if (!isset($_SESSION['username']) || (int)($_SESSION['role'] ?? -1) !== 0) {
            http_response_code(403);
            echo json_encode(['status' => false, 'message' => 'Không có quyền'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $makh  = $this->gioHangModel->findCustomerByUsername($_SESSION['username']);
        $magio = $this->gioHangModel->getCartId($makh);

        if (!$magio) {
            echo json_encode(['status' => true, 'message' => 'Giỏ hàng đã trống'], JSON_UNESCAPED_UNICODE);
            return;
        }

        $ok = $this->gioHangModel->clearCart($magio);
        echo json_encode(['status' => $ok !== false, 'message' => $ok !== false ? 'Đã xóa toàn bộ giỏ hàng' : 'Xóa thất bại'], JSON_UNESCAPED_UNICODE);
    }
}
